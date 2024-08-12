<?php

namespace App\Http\Controllers\Reports\Activities;

use App\Adapters\JQueryBuilder;
use App\Filters\ExcludeFilter;
use App\Http\Controllers\Reports\BaseReportController;
use App\Models\Batch;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;

class BatchActivitiesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:print_batches_activity_report', ['only' => ['printBatchesActivityReport']]);
    }

    public function showBatchesActivityReport()
    {
        return view('reports.activities.batches');
    }

    public function listBatchesActivityReport(Request $request)
    {
        if (getFilterRule('batches.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $paginatedRows = JQueryBuilder::for(Batch::class)
            ->join('users', 'users.id', 'batches.user_id')
            ->join('products', 'products.id', '=', 'batches.batch_product_id', 'LEFT OUTER')
            ->withCount('activities')
            //->when(getFilterRule('activity_count') != null, function ($q) {
            //  return $q->withCount([
            //    'activity' => function (Builder $builder) use (&$query) {
            //      $query = $builder;
            //    },
            //  ])
            //    ->setBindings($query->getBindings(), 'where')
            //    ->whereRaw("({$query->toSql()}) = ?", getFilterRule('activity_count'));
            //})
            ->with([
                'activities' => function ($q) {
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
                    batches.id as batch_id,
                    users.kurdish_name as user_name,
                    batches.product_type,
                    products.kurdish_name as product_name,
                    batches.created_at
                ')
            ->AllowedSorts([
                'activities_count',
                'batches.id',
                'batches.created_at',
                'batches.product_type',
                'products.kurdish_name',
                'users.kurdish_name',
            ])
            ->allowedFilters(
                AllowedFilter::custom('activity_count', new ExcludeFilter),
                'batches.id',
                'batches.created_at',
                'batches.product_type',
                'products.kurdish_name',
                'users.kurdish_name'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('batches.id', 'desc');
            })
            ->jsonJPaginate();

        return $paginatedRows;

        $rows = collect($paginatedRows['rows']);

        $batch_ids = $rows->pluck('batch_id');

        $history = Activity::whereIn('subject_id', $batch_ids)
            ->where(['subject_type' => 'App\Models\Batch'])
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
            $row['update_count'] = count($historyGroupedById[$row['batch_id']]);

            $updates = $historyGroupedById[$row['batch_id']];
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

    public function printBatchesActivityReport(Request $request)
    {
        if (getFilterRule('batches.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $rows = JQueryBuilder::for(Batch::class)
            ->join('users', 'users.id', 'batches.user_id')
            ->join('products', 'products.id', '=', 'batches.batch_product_id', 'LEFT OUTER')
            ->withCount('activities')
            ->with([
                'activities' => function ($q) {
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
                    batches.id as batch_id,
                    users.kurdish_name as user_name,
                    batches.product_type,
                    products.kurdish_name as product_name,
                    batches.created_at as date
                ')
            ->AllowedSorts([
                'activities_count',
                'batches.id',
                'batches.created_at',
                'batches.product_type',
                'products.kurdish_name',
                'users.kurdish_name',
            ])
            ->allowedFilters(
                AllowedFilter::custom('activity_count', new ExcludeFilter),
                'batches.id',
                'batches.created_at',
                'batches.product_type',
                'products.kurdish_name',
                'users.kurdish_name'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('batches.id', 'desc');
            })
            ->get();

        return view('reports.activities.printBatchesActivityReport', compact(['rows']));
    }
}
