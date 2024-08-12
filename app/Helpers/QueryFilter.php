<?php

namespace App\Helpers;

class QueryFilter
{
    /**
     * Filter easui datagrid filter
     *
     * @method filter
     *
     * @param  [QueryBuilder] $query
     * @param  [Request] $request the request that contains filter
     * @param  array  $params  additional parameter to be passed for between operatoer in sql
     * @return [QueryBuilder]
     */
    public static function filter($query, $request, $betweenFields = [])
    {
        $filterRules = collect(($request->has('filterRules')) ? json_decode($request->filterRules) : []);

        [$filterRules, $query] = self::removeBetweenParams($betweenFields, $filterRules, $query);

        $op = '';
        $filterRules->map(function ($filterRule, $key) use ($query) {
            $value = $filterRule->value;
            switch ($filterRule->op) {
                case '=':
                    $op = '=';
                    if (str_contains($filterRule->fieldType, 'date')) {
                        $value = toMysqlDate($value);
                    }
                    break;

                default:
                    $op = 'like';
                    $value = '%' . $value . '%';
                    break;
            }
            $query->where($filterRule->field, $op, $value);
        });

        return $query;
    }

    public static function removeBetweenParams($betweenFields, $filterRules, $query)
    {
        $betweenFields = collect($betweenFields);

        $remainingRules = $filterRules->filter(function ($filterRule, $key) use ($betweenFields, $query) {
            if ($betweenFields->contains($filterRule->field)) {
                $from = toMysqlDate($filterRule->filter_from);
                $to = toMysqlDate($filterRule->filter_to);
                $query->whereBetween($filterRule->field, [$from, $to]);
            }

            return !$betweenFields->contains($filterRule->field);
        });

        return [$remainingRules, $query];
    }
}
