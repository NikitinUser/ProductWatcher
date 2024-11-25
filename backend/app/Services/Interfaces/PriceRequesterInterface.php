<?php

namespace App\Services\Interfaces;

interface PriceRequesterInterface
{
    public function getPrice(string $url): float;
}
