<?php

namespace App\Services;

use App\Services\Interfaces\PriceRequesterInterface;
use App\Services\PriceExtractorChain\PriceExtractorChain;
use Illuminate\Support\Facades\Http;

class PriceRequester implements PriceRequesterInterface
{
    public function __construct(
        private PriceExtractorChain $priceExtractorChain
    ) {   
    }

    public function getPrice(string $url): float
    {
        $response = Http::get($url);

        $response->throw();

        $html = $response->body();

        return $this
            ->priceExtractorChain
            ->extractPrice($html);
    }
}
