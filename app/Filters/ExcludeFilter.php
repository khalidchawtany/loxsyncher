<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ExcludeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        return $query;
    }

    // public function __invoke(Builder $query, $value, string $property)
    // {
    //     $query->whereHas('permissions', function (Builder $query) use ($value) {
    //         $query->where('name', $value);
    //     });
    // }
}
