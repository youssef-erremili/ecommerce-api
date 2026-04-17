<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait Paginator
{
    public function paginateResource(LengthAwarePaginator $paginator): array
    {
        return [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'first_page' => 1,
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'prev_page' => $paginator->previousPageUrl() ?? null,
            'next_page' => $paginator->nextPageUrl() ?? null,
        ];
    }
}
