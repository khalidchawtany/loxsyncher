<?php

namespace App\Utils;

use Illuminate\Support\Str;

class AssetsHelper
{
    public static function path($asset)
    {
        $site_name = Str::lower(config('app.site_name'));

        return "{$site_name}/{$asset}";
    }

    public static function img($img)
    {
        $site_path = self::path($img);

        return asset("img/$site_path");
    }
}
