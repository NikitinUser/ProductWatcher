<?php

namespace Tests\Unit;

use App\Enums\PricesResultEnum;
use App\Services\Interfaces\ProductSpyInterface;
use App\Services\PriceLogService;
use App\Services\PriceRequester;
use App\Services\ProductSpyService;
use App\Services\TargetProductService;
use Exception;
use Illuminate\Http\Client\RequestException;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductSpyServiceTest extends TestCase
{
    private $targetProductServiceMock;
    private $priceLogServiceMock;
    private $priceRequesterMock;
    private $productSpyServiceMock;

    private $mockProducts = [
        [
            'product_id' => 1,
            'url' => 'http://192.168.0.1/',
        ],
        [
            'product_id' => 2,
            'url' => 'http://192.168.0.1/',
        ],
        [
            'product_id' => 3,
            'url' => 'http://192.168.0.1/',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetProductServiceMock = Mockery::mock(TargetProductService::class);
        $this->priceLogServiceMock = Mockery::mock(PriceLogService::class);
        $this->priceRequesterMock = Mockery::mock(PriceRequester::class);

        $this->productSpyServiceMock = Mockery::mock(ProductSpyService::class, [
            $this->priceLogServiceMock,
            $this->priceRequesterMock,
            $this->targetProductServiceMock,
        ])
            ->makePartial();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_instaceof_success(): void
    {
        $this->assertTrue(in_array(ProductSpyInterface::class, class_implements(ProductSpyService::class)));
    }

    public function test_run_try_products_success(): void
    {
        $this->targetProductServiceMock->shouldReceive('getActive')
            ->once()
            ->andReturn($this->mockProducts);

        $this->priceRequesterMock->shouldReceive('getPrice')
            ->times(count($this->mockProducts))
            ->andReturn(100.0);

        $this->priceLogServiceMock->shouldReceive('log')
            ->times(count($this->mockProducts))
            ->withArgs(function ($logDto) {
                return in_array($logDto->target_product_id, array_column($this->mockProducts, 'product_id'))
                    && $logDto->price === 100.0
                    && $logDto->result === PricesResultEnum::SUCCESS;
            });

        $this->productSpyServiceMock->run();

        $this->assertTrue(true);
    }

    public function test_run_first_catch_timeout_success(): void
    {
        foreach (ProductSpyService::AVAILABLE_TO_RETRY_HTTP_CODES as $code) {
            $this->targetProductServiceMock->shouldReceive('getActive')
                ->once()
                ->andReturn($this->mockProducts);

            $mockException = new RequestException(
                new \Illuminate\Http\Client\Response(new \GuzzleHttp\Psr7\Response($code))
            );

            $this->priceRequesterMock->shouldReceive('getPrice')
                ->times(count($this->mockProducts))
                ->andThrow($mockException);;

            $this->priceLogServiceMock->shouldReceive('log')
                ->times(count($this->mockProducts))
                ->withArgs(function ($logDto) {
                    return in_array($logDto->target_product_id, array_column($this->mockProducts, 'product_id'))
                        && $logDto->price === null
                        && $logDto->result === PricesResultEnum::REPEAT
                        && !empty($logDto->message);
                });

            $this->productSpyServiceMock->run();

            $this->assertTrue(true);
        }
    }

    public function test_run_first_catch_another_http_err_success(): void
    {
        foreach (Response::$statusTexts as $code => $text) {
            if ($code < Response::HTTP_MULTIPLE_CHOICES || in_array($code, ProductSpyService::AVAILABLE_TO_RETRY_HTTP_CODES)) {
                continue;
            }

            $this->targetProductServiceMock->shouldReceive('getActive')
                ->once()
                ->andReturn($this->mockProducts);

            $mockException = new RequestException(
                new \Illuminate\Http\Client\Response(new \GuzzleHttp\Psr7\Response($code))
            );

            $this->priceRequesterMock->shouldReceive('getPrice')
                ->times(count($this->mockProducts))
                ->andThrow($mockException);;

            $this->priceLogServiceMock->shouldReceive('log')
                ->times(count($this->mockProducts))
                ->withArgs(function ($logDto) {
                    return in_array($logDto->target_product_id, array_column($this->mockProducts, 'product_id'))
                        && $logDto->price === null
                        && $logDto->result === PricesResultEnum::UNAVAILABLE
                        && !empty($logDto->message);
                });

            $this->productSpyServiceMock->run();

            $this->assertTrue(true);
        }
    }

    public function test_run_second_catch_success(): void
    {
        $this->targetProductServiceMock->shouldReceive('getActive')
            ->once()
            ->andReturn($this->mockProducts);

        $mockException = new Exception("smth err");

        $this->priceRequesterMock->shouldReceive('getPrice')
            ->times(count($this->mockProducts))
            ->andThrow($mockException);;

        $this->priceLogServiceMock->shouldReceive('log')
            ->times(count($this->mockProducts))
            ->withArgs(function ($logDto) {
                return in_array($logDto->target_product_id, array_column($this->mockProducts, 'product_id'))
                    && $logDto->price === null
                    && $logDto->result === PricesResultEnum::UNAVAILABLE
                    && !empty($logDto->message);
            });

        $this->productSpyServiceMock->run();

        $this->assertTrue(true);
    }
}
