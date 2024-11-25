<?php

namespace App\Services;

use App\Dto\PriceLogDto;
use App\Repositories\PriceLogRepository;

class PriceLogService
{
    public function __construct(
        private PriceLogRepository $priceLogRepository
    ) {
    }

    public function log(PriceLogDto $dto)
    {

    }
}
