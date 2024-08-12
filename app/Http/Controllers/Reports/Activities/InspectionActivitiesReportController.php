<?php

namespace App\Http\Controllers\Reports\Activities;

use App\Filters\ExcludeFilter;
use App\Http\Controllers\Reports\BaseReportController;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use App\Adapters\JQueryBuilder;

class InspectionActivitiesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showInspectionsActivityReport()
    {
        return view('reports.activities.inspections');
    }

    public function listInspectionsActivityReport(Request $request)
    {
        if (getFilterRule('inspections.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $paginatedRows = JQueryBuilder::for(Inspection::class)
            ->join('users', 'users.id', 'inspections.user_id')
            ->join('products', 'products.id', '=', 'inspections.product_id', 'LEFT OUTER')
            ->selectRaw('
                    inspections.id as inspection_id,
                    users.kurdish_name as user_name,
                    inspections.product_type,
                    products.kurdish_name as product_name,
                    inspections.created_at
                ')
            ->allowedFilters(
                'inspections.id',
                'inspections.created_at',
                'inspections.update_count',
                'inspections.product_type',
                'products.kurdish_name',
                'users.kurdish_name'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('inspections.id', 'desc');
            })
            ->jsonJPaginate();

        $rows = collect($paginatedRows['rows']);

        $inspection_ids = $rows->pluck('inspection_id');

        $history = Activity::whereIn('subject_id', $inspection_ids)
            ->where(['subject_type' => 'App\Models\Inspection'])
            ->join('users', 'users.id', 'activity_log.causer_id')
            ->selectRaw('
                  activity_log.subject_id,
                  activity_log.created_at as date,
                  users.kurdish_name as user_name,
                  activity_log.properties as props
                ')
            ->get();

        $historyGroupedById = $history->groupBy('subject_id')->toArray();

        $rows = $rows->map(function ($row) use ($historyGroupedById) {
            $updates = [];
            if (isset($historyGroupedById[$row['inspection_id']])) {
                $row['update_count'] = count($historyGroupedById[$row['inspection_id']]);
                $updates = $historyGroupedById[$row['inspection_id']];
            }
            /* $updates = collect($updates)->map(function ($update) { */
            /*   $props = json_decode($update['props']); */
            /*   $old = trim(prettyPrint(json_encode($props->old)), '{}\t\n\r\0\x0B"'); */
            /*   $new = trim(prettyPrint(json_encode($props->attributes)), '{}\t\n\r\0\x0B"'); */
            /*   $update['old'] = $old; */
            /*   $update['new'] = $new; */
            /*   return $update; */
            /* }); */

            $row['updates'] = $updates;

            return $row;
        });

        $paginatedRows['rows'] = $rows;

        return array_merge($paginatedRows, ['stat_rows' => []]);
    }

    public function printInspectionsActivityReport(Request $request)
    {
        if (getFilterRule('inspections.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $rows = JQueryBuilder::for(Inspection::class)
            ->join('users', 'users.id', 'inspections.user_id')
            ->join('products', 'products.id', '=', 'inspections.product_id', 'LEFT OUTER')
            ->withCount('activity')
            ->with([
                'activity' => function ($q) {
                    return $q->join('users', 'users.id', '=', 'activity_log.causer_id', 'LEFT OUTER')
                        ->selectRaw('
                                 activity_log.subject_id,
                                 activity_log.subject_type,
                                 activity_log.description,
                                 activity_log.properties,
                                 activity_log.created_at as date,
                                users.kurdish_name as user_name');
                },
            ])
            ->selectRaw('
                    inspections.id as inspection_id,
                    users.kurdish_name as user_name,
                    inspections.product_type,
                    products.kurdish_name as product_name,
                    inspections.created_at as date
                ')
            ->allowedFilters(
                AllowedFilter::custom('activity_count', new ExcludeFilter),
                'inspections.id',
                'inspections.created_at',
                'inspections.product_type',
                'products.kurdish_name',
                'users.kurdish_name'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('inspections.id', 'desc');
            })
            ->get();

        return view('reports.activities.printInspectionsActivityReport', compact(['rows']));
    }
}
