<?php

namespace App\Models;

class Lab
{
    public static $LABS = [
        'Chemistry' => ['text' => 'Chemistry',    'Kurdish' => 'کیمیایی'],
        'Microbiology' => ['text' => 'Microbiology', 'Kurdish' => 'بەکترۆلۆجی'],
        'Physical' => ['text' => 'Physical',     'Kurdish' => 'فیزیایی'],
        'Construction' => ['text' => 'Construction', 'Kurdish' => 'تاقیگەی بیناسازی'],
        'Fuel' => ['text' => 'Fuel',         'Kurdish' => 'سوتەمەنی'],
        'Test results' => ['text' => 'Test results', 'Kurdish' => 'ئەنجامی تقیگە'],
        'Other' => ['text' => 'Other',        'Kurdish' => 'تاقیگەکانی تر'],
    ];

    public static $LAB_PERMISSIONS = [
        'view_check_chemical' => 'Chemistry',
        'view_check_bactriology' => 'Microbiology',
        'view_check_physical' => 'Physical',
        'view_check_construction' => 'Construction',
        'view_check_fuel' => 'Fuel',
    ];

    public static function GetUserLabItems()
    {
        $labs = [];
        collect(self::$LAB_PERMISSIONS)->each(function ($lab, $permission) use (&$labs) {
            if (auth()->user()->can($permission)) {
                $labs[$lab] = $lab;
            }
        });

        return $labs;
    }

    public static function GetUserLabs()
    {
        $labs = self::GetUserLabItems();

        foreach ($labs as  &$lab) {
            $lab = "'$lab'";
        }

        if (count(self::$LAB_PERMISSIONS) == count($labs)) {
            return 'All';
        }

        return collect($labs)->join(',');
    }
}
