<?php

namespace App\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;

interface HomeServiceInterface
{
    public function index(int $perPage = 30): LengthAwarePaginator;
}
