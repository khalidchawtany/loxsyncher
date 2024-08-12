<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class DateRangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (is_array($value)) {
            $value = (object) $value;
            $query->whereBetween($property, [$value->from, $value->to]);

            return;
        }

        return $query->whereDate($property, $value);
    }
}
