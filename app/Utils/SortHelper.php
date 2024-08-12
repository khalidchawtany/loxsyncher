<?php

namespace App\Utils;

class SortHelper
{
    public static function get(): array
    {
        $request = request();

        if ($request->has('sort')) {
            $sort = $request->get('sort');

            return [
                'descending' => $sort[0] === '-',
                'sortBy' => ltrim($sort, '-'),
            ];
        }

        return [
            'descending' => true,
            'sortBy' => 'id',
        ];
    }
}
