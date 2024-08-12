<?php

namespace App\Utils;

class FilterRulesHelper
{
    public static function get($ruleKey = null)
    {
        if ($ruleKey == null) {
            return self::getAll();
        }

        $rule = null;

        $filterRules = self::getAll();

        $filterRules->each(function ($filterRule) use (&$rule, $ruleKey) {
            if ($filterRule->field == $ruleKey && isset($filterRule->value)) {
                $rule = $filterRule->value;
            }
        });

        return $rule;
    }

    public static function pop($ruleKey)
    {
        $filterValue = self::get($ruleKey);
        $filterRules = self::getAll();

        $filterRules = $filterRules->filter(function ($filterRule) use ($ruleKey) {
            return $filterRule->field != $ruleKey;
        });

        request()->request->add(['filterRules' => $filterRules]);

        return $filterValue;
    }

    private static function getAll()
    {
        return collect(json_decode(request('filterRules')));
    }
}
