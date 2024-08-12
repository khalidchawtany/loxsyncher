<?php

namespace App\Services\JsonListProducers;

class BaseJsonListProducer
{
    /**
     * Filters out blocked items. $opts['hide_blocked'] can be used to
     * enable this filter
     *
     * @param    $query QueryBuilder
     * @param    $opts array $opts['hide_blocked']
     * @return QueryBuilder
     */
    public static function excludeBlocked($query, $opts)
    {
        $hide_blocked = array_has($opts, 'hide_blocked') ? $opts['hide_blocked'] : false;

        return $query->when($hide_blocked, function ($query) {
            $query->where('blocked', '<>', true);
        });
    }

    /**
     * Performs projection based on $opts['columns']  or $opts['select_raw']
     *
     * @param    $opts array $opts['columns', 'select_raw']
     * @return QueryBuilder
     */
    public static function select($query, $opts)
    {
        $columns = array_has($opts, 'columns') ? $opts['columns'] : [];

        $query->when(count($columns) != 0, function ($query) use ($columns) {
            $query->select($columns);
        });

        $selectRaw = array_has($opts, 'select_raw') ? $opts['select_raw'] : false;

        return $query->when($selectRaw, function ($query) use ($selectRaw) {
            $query->selectRaw($selectRaw);
        });
    }

    /**
     * Prepends None to the list of items.
     * $opts['prepend_none_for_label'] can be used to set the column
     * for which the None option is to be prepended.
     *
     * @param    $items array of items that we prepend the Emoty entry
     * @param    $opts array $opts['prepend_none_for_label']
     * @return array
     */
    public static function prependNone($items, $opts)
    {
        $prepend_none_for_label = array_has($opts, 'prepend_none_for_label') ? $opts['prepend_none_for_label'] : false;

        if ($prepend_none_for_label != false) {
            if (strlen($prepend_none_for_label) > 0) {
                return prependNone($items, $prepend_none_for_label);
            }

            return prependNone($items);
        }

        return $items;
    }
}
