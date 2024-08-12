<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperBaseModel
 */
class BaseModel extends Model
{
    protected $modelName;

    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return (new LogOptions())
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getModelName()
    {
        if (isset($this->modelName)) {
            return $this->modelName;
        }

        $modelName = array_last(explode('\\', get_class($this)));

        return preg_replace('/(?<!\ )[A-Z]/', ' $0', $modelName);
    }


    public function getFormatedDate($value)
    {
        if ($value == null) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $value)
            ->format(config('app.date_format'));
    }

    public function setFormatedDate($value)
    {
        if ($value == null) {
            return null;
        }

        return Carbon::createFromFormat(config('app.date_format'), $value)
            ->format('Y-m-d');
    }
}
