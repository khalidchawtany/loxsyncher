<?php

namespace App\Adapters;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class JQueryBuilder
{
    private static function adaptPagination($request)
    {
        $paginationParameterName = config('json-api-paginate.pagination_parameter');
        $pageSizeParameterName = config('json-api-paginate.size_parameter');
        $pageNumberParameterName = config('json-api-paginate.number_parameter');

        $newPaginationParamas = [];

        // ?page=x => page[number]=x
        if ($request->has('page')) {
            $newPaginationParamas = array_merge_recursive($newPaginationParamas, [
                $paginationParameterName => [$pageNumberParameterName => $request->page],
            ]);
        }

        // ?row=x => page[size]=x
        if ($request->has('rows')) {
            $newPaginationParamas = array_merge_recursive($newPaginationParamas, [
                $paginationParameterName => [$pageSizeParameterName => $request->rows],
            ]);
        }

        return $newPaginationParamas;
    }

    private static function adaptFilters($request)
    {
        if (!$request->has('filterRules')) {
            return [];
        }

        $filterRules = json_decode($request->filterRules);
        if (count($filterRules) == 0) {
            return [];
        }

        $newFilrers = [];

        foreach ($filterRules as $rule) {
            if (!isset($rule->op) || in_array($rule->op, ['contains', 'equal', 'like', 'dateTimeBetween', 'dateBetween'])) {
                $newFilrers = array_merge_recursive(
                    $newFilrers,
                    ['filter' => [$rule->field => $rule->value]]
                );
            }
        }

        return $newFilrers;
    }

    private static function adaptSort($request)
    {
        if (!$request->has('sort')) {
            return [];
        }

        $desc = ($request->has('order') && $request->get('order') == 'desc')
        ? '-'
        : '';

        return  ['sort' => $desc. $request->sort];

    }

    private static function transform($request): Request
    {
        $request = $request ?? request();

        $newQueryString = [];

        $newQueryString = array_merge($newQueryString, self::adaptPagination($request));
        $newQueryString = array_merge($newQueryString, self::adaptFilters($request));
        $newQueryString = array_merge($newQueryString, self::adaptSort($request));

        $request->merge($newQueryString);

        return $request;
    }

    public static function for($subject, ?Request $request = null): QueryBuilder
    {
        $transformedRequest = self::transform($request);

        return QueryBuilder::for($subject, $transformedRequest);
    }
}
