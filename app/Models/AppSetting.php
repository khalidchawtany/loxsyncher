<?php

namespace App\Models;

/**
 * @mixin IdeHelperAppSetting
 */
class AppSetting extends BaseModel
{
    protected $fillable = [
        'name',
        'value',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static $ALL_APP_SETTING = null;

    public static function unset($key)
    {
        $setting = self::where('name', $key);

        if (!empty($setting)) {
            $setting->delete();
        }
    }

    public static function set($key, $value)
    {
        $setting = self::get($key);
        if (empty($setting)) {
            self::create([
                'name' => $key,
                'value' => $value,
                'user_id' => User::first()->id,
            ]);
        }
    }

    public static function get($key)
    {
        if (self::$ALL_APP_SETTING == null) {
            self::$ALL_APP_SETTING = self::all();
        }

        /* $setting = self::where(['name' => $key])->first(); */
        $setting = self::getAppSettingFromKey($key);

        return optional($setting)->value;
    }

    private static function getAppSettingFromKey($key)
    {
        return self::$ALL_APP_SETTING->filter(function ($appSetting) use ($key) {
            return $appSetting->name == $key;
        })->first();
    }
}
