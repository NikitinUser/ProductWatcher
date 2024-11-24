<?php

namespace App\Services;

use App\Dto\PriceLogDto;
use App\Enums\PricesResultEnum;
use App\Services\Interfaces\ProductSpyInterface;
use App\Services\PriceLogService;
use App\Services\PriceRequester;
use App\Services\TargetProductService;
use Illuminate\Http\Client\RequestException;
use Symfony\Component\HttpFoundation\Response;

class ProductSpyService implements ProductSpyInterface
{
    public const AVAILABLE_TO_RETRY_HTTP_CODES = [
        Response::HTTP_REQUEST_TIMEOUT,
        Response::HTTP_INTERNAL_SERVER_ERROR,
    ];

    public function __construct(
        private PriceLogService $priceLogService,
        private PriceRequester $priceRequester,
        private TargetProductService $targetProductService
    ) {
    }

    public function run(): void
    {
        $products = $this->targetProductService->getActive();

        /**Todo generator**/
        foreach ($products as $product) {
            $logDto = new PriceLogDto();
            $logDto->target_product_id = $product['product_id'];

            try {
                $logDto->price = $this->priceRequester->getPrice();
                $logDto->result = PricesResultEnum::SUCCESS;
            } catch (RequestException $re) {
                $statusCode = $re->response->status();
                $logDto->message = $re->getMessage();

                if (in_array($statusCode, self::AVAILABLE_TO_RETRY_HTTP_CODES)) {
                    $logDto->result = PricesResultEnum::REPEAT;
                } else {
                    $logDto->result = PricesResultEnum::UNAVAILABLE;
                }
            } catch (\Throwable $t) {
                $logDto->result = PricesResultEnum::UNAVAILABLE;
                $logDto->message = $t->getMessage();
            } finally {
                $this->priceLogService->log($logDto);
            }
        }
    }
}
