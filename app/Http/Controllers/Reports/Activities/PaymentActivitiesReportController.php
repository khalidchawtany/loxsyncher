<?php

namespace App\Http\Controllers\Reports\Activities;


use App\Models\Payment;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\Reports\BaseReportController;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\ExcludeFilter;
class PaymentActivitiesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showPaymentsActivityReport()
    {
        return view('reports.activities.payments');
    }

    public function listPaymentsActivityReport(Request $request)
    {
        if (getFilterRule('payments.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $paginatedRows = JQueryBuilder::for(Payment::class)
            ->join('users', 'users.id', 'payments.user_id')
            ->join('transactions', 'transactions.id', '=', 'payments.transaction_id', 'LEFT OUTER')
            ->join('products', 'products.id', '=', 'transactions.product_id', 'LEFT OUTER')
            ->selectRaw('
                    payments.id as payment_id,
                    payments.amount as paid_amount,
                    users.kurdish_name as user_name,
                    transactions.id as transaction_id,
                    transactions.product_type as product_type,
                    products.kurdish_name as product_name,
                    payments.created_at
                ')
            ->allowedFilters(
                'payments.id',
                'payments.created_at',
                'payments.update_count',
                'payments.amount',
                'transactions.id',
                'transactions.product_type',
                'products.kurdish_name',
                'users.kurdish_name'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('payments.id', 'desc');
            })
            ->jsonJPaginate();

        $rows = collect($paginatedRows['rows']);

        $payment_ids = $rows->pluck('payment_id');

        $history = Activity::whereIn('subject_id', $payment_ids)
            ->where(['subject_type' => 'App\Models\Payment'])
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
            if (isset($historyGroupedById[$row['payment_id']])) {
                $row['update_count'] = count($historyGroupedById[$row['payment_id']]);
                $updates = $historyGroupedById[$row['payment_id']];
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

    public function printPaymentsActivityReport(Request $request)
    {
        if (getFilterRule('payments.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $rows = JQueryBuilder::for(Payment::class)
            ->join('users', 'users.id', 'payments.user_id')
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
                    payments.id as payment_id,
                    users.kurdish_name as user_name,
                    payments.amount as paid_amount,
                    payments.created_at as date
                ')
            ->allowedFilters(
                AllowedFilter::custom('activity_count', new ExcludeFilter),
                'payments.id',
                'payments.created_at',
                'payments.amount',
                'users.kurdish_name'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('payments.id', 'desc');
            })
            ->get();

        return view('reports.activities.printPaymentsActivityReport', compact(['rows']));
    }
}
