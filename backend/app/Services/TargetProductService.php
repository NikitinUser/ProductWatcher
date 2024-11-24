<?php

namespace App\Services;

use App\Dto\TargetProductDto;
use App\Repositories\TargetProductRepository;

class TargetProductService
{
    public function __construct(
        private TargetProductRepository $targetProductRepository
    ) {
    }

    public function getActive(): array
    {
        return [];
    }
}
