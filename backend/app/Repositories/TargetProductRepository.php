<?php

namespace App\Repositories;

use App\Models\TargetProduct;

class TargetProductRepository
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
        return TargetProduct::class;
    }
}
