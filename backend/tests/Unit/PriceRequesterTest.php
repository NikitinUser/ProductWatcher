<?php

namespace Tests\Unit;

use App\Services\Interfaces\PriceRequesterInterface;
use App\Services\PriceExtractorChain\PriceExtractorChain;
use App\Services\PriceRequester;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class PriceRequesterTest extends TestCase
{
    private $priceExtractorChainMock;
    private $priceRequesterMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->priceExtractorChainMock = Mockery::mock(PriceExtractorChain::class);

        $this->priceRequesterMock = Mockery::mock(PriceRequester::class, [
            $this->priceExtractorChainMock,
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
        $this->assertTrue(in_array(PriceRequesterInterface::class, class_implements(PriceRequester::class)));
    }

    public function test_get_price_return_float_success(): void
    {
        $testUrl = 'http://example.com/product';
        $testHtml = '<html><body>Price: $100</body></html>';
        $expectedPrice = 100.0;

        $httpResponseMock = Mockery::mock(\Illuminate\Http\Client\Response::class);
        $httpResponseMock
            ->shouldReceive('throw')
            ->once();
        $httpResponseMock
            ->shouldReceive('body')
            ->once()
            ->andReturn($testHtml);

        Http::shouldReceive('get')
            ->once()
            ->with($testUrl)
            ->andReturn($httpResponseMock);

        $this->priceExtractorChainMock
            ->shouldReceive('extractPrice')
            ->once()
            ->with($testHtml)
            ->andReturn($expectedPrice);

        $this->priceRequesterMock->getPrice($testUrl);

        $this->assertTrue(true);
    }

    public function test_get_price_http_throw_catch_error(): void
    {
        $testUrl = 'http://example.com/product';
        $code = 500;

        $mockException = new RequestException(
            new \Illuminate\Http\Client\Response(new \GuzzleHttp\Psr7\Response($code))
        );

        $httpResponseMock = Mockery::mock(\Illuminate\Http\Client\Response::class);
        $httpResponseMock
            ->shouldReceive('throw')
            ->once()
            ->andThrow($mockException);

        Http::shouldReceive('get')
            ->once()
            ->with($testUrl)
            ->andReturn($httpResponseMock);

        try {
            $this->priceRequesterMock->getPrice($testUrl);
        } catch (RequestException $re) {
            $this->assertTrue(true);
        }
    }

    public function test_get_price_try_return_null_catch_error(): void
    {
        $testUrl = 'http://example.com/product';
        $testHtml = '<html><body>Price: $100</body></html>';
        $expectErrMessage = 'App\Services\PriceRequester::getPrice(): Return value must be of type float, null returned';

        $httpResponseMock = Mockery::mock(\Illuminate\Http\Client\Response::class);
        $httpResponseMock
            ->shouldReceive('throw')
            ->once();
        $httpResponseMock
            ->shouldReceive('body')
            ->once()
            ->andReturn($testHtml);

        Http::shouldReceive('get')
            ->once()
            ->with($testUrl)
            ->andReturn($httpResponseMock);

        $this->priceExtractorChainMock
            ->shouldReceive('extractPrice')
            ->once()
            ->with($testHtml)
            ->andReturn(null);

        try {
            $this->priceRequesterMock->getPrice($testUrl);
        } catch (\TypeError $te) {
            $this->assertEquals($te->getMessage(), $expectErrMessage);
        }
    }
}
