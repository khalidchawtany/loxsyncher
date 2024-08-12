<?php

namespace App\Helpers;

class FilterHelper
{
    private $filter;

    public function __construct()
    {
        $this->filter = '';
    }

    /**
     * Adds filter to the filter text
     *
     * @param    $query
     * @param    $filter
     */
    public function applyFilter(string $cond, string $op = ' AND ')
    {
        if (empty($cond)) {
            return;
        }

        if (!empty($this->filter)) {
            $this->filter .= $op;
        }

        $this->filter .= $cond;
    }

    /**
     * Returns the constructed filter rules as raw sql conditions
     *
     * @return filter reules
     */
    public function getFilterRules($prependWhere = false)
    {
        if (empty($this->filter)) {
            return '';
        }

        if ($prependWhere) {
            return 'WHERE ' . $this->filter;
        }

        return $this->filter;
    }
}
