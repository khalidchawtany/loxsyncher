<?php

namespace App\Utils;


class PaginationHelper
{
    public static function get($paginator): array
    {
        return [
            'page' => $paginator->currentPage(),
            'rowsNumber' => $paginator->total(),
            'rowsPerPage' => $paginator->perPage(),
            ...SortHelper::get(),
        ];
    }
}
