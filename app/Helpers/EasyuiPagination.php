<?php

namespace App\Helpers;

/**
 * The controllers that use easyui pagination
 */
class EasyuiPagination
{
    public static function paginageData($request, $query, $customSize = 10)
    {
        $page = ($request->has('page')) ? $request->page : 1;
        $rows = ($request->has('rows')) ? $request->rows : $customSize;
        $offset = ($page - 1) * $rows;
        $total = $query->count();

        $data = $query->offset($offset)->limit($rows)->get();

        $reachedMax = ($total - ($offset + $rows) <= 0);

        return ['rows' => $data, 'total' => $total, 'reachedMax' => $reachedMax];
    }
}
