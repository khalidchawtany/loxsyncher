<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\BelongsToManyDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements JWTSubject
{
    use BelongsToManyDepartment;
    use CausesActivity;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Impersonate;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'kurdish_name',
        'email',
        'password',
        'open_transaction_after_login',
        'job_description',
        'is_staff',
        'external_view',
        'external_update',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected $appends = ['departments'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return (new LogOptions())
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function canImpersonate()
    {
        return $this->can('impersonate_user');
    }

    public function canBeImpersonated()
    {
        return $this->name != 'super';
    }

    // public function getDepartmentsAttribute()
    // {
    //     return $this->departments()->select('name');
    // }

    public function departments()
    {
        return once(function () {
            return $this->belongsToMany(Department::class);
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function FetchCausers($activities)
    {
        $causerIds = $activities->map(function ($row) {
            return $row->activities->map(function ($activity) {
                return $activity->causer_id;
            });
        })->flatten()->unique();

        return User::select(['id', 'kurdish_name'])
            ->whereKey($causerIds)
            ->get();
    }

    // Bypass error of update user password
    protected $onceListeners = [];

    public function __get($key)
    {
        if (starts_with($key, '___once_listener__')) {
            return $this->onceListeners[$key];
        }

        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        if (starts_with($key, '___once_listener__')) {
            $this->onceListeners[$key] = $value;

            return;
        }
        parent::__set($key, $value);
    }
}
