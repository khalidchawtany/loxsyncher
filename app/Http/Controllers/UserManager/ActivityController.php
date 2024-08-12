<?php

namespace App\Http\Controllers\UserManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Adapters\JQueryBuilder;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_activity_log');
    }

    public function index()
    {
        return view('user_manager.activities');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(Activity::class)
            ->join('users', 'users.id', 'activity_log.causer_id')
            ->selectRaw('
                        users.kurdish_name as user_name,
                        activity_log.*
            ')
            ->allowedSorts([
                'activity_log.id',
                'users.kurdish_name',
                'activity_log.subject_type',
                'activity_log.subject_id',
                'activity_log.description',
                'activity_log.properties',
                'activity_log.created_at',
            ])
            ->allowedFilters([
                'activity_log.id',
                'users.kurdish_name',
                'activity_log.subject_type',
                'activity_log.subject_id',
                'activity_log.description',
                'activity_log.properties',
                'activity_log.created_at',
            ])
            ->orderBy('activity_log.id', 'DESC')
            ->jsonJPaginate();
    }

    public function listModelActivityNames()
    {
        $modelActivityNames = Activity::distinct('properties')->get(['properties']);

        $modelActivities = [];
        foreach ($modelActivityNames as $modelActivityName) {
            $properties = collect(json_decode($modelActivityName->properties));
            $modelActivities[] = $properties;
        }

        return $modelActivities;
    }
}
