<?php

namespace App\Repositories;

use App\Models\PriceLog;

class PriceLogRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = app()->make($this->model());
    }

    /**
     * @return string
     */
    public function model()
    {
        return PriceLog::class;
    }
}
