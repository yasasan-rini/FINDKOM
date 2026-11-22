<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Pager
{
    public static function paginate(Collection $items, int $per = 12): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        return new LengthAwarePaginator(
            $items->slice(($page - 1) * $per, $per)->values(),
            $items->count(), $per, $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}