<?php

namespace App\Http\Controllers\Reports\Activities;

use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use App\Filters\ExcludeFilter;
use App\Http\Controllers\Reports\BaseReportController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;

class TransactionActivitiesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showTransactionsActivityReport()
    {
        return view('reports.activities.transactions');
    }

    public function listTransactionsActivityReport(Request $request)
    {
        if (getFilterRule('transactions.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $paginatedRows = JQueryBuilder::for(Transaction::class)
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('products', 'products.id', '=', 'transactions.product_id', 'LEFT OUTER')

            ->whereRaw("(( SELECT count(*) FROM `activity_log` WHERE
            `activity_log`.`subject_id` = `transactions`.`id`
            AND `activity_log`.`subject_type` = 'App\\\Models\\\Transaction'
          ) > 2)")
            ->selectRaw('
                    transactions.id as transaction_id,
                    users.kurdish_name as user_name,
                    transactions.product_type,
                    products.kurdish_name as product_name,
                    transactions.created_at
                ')
            ->allowedFilters([
                'transactions.id',
                AllowedFilter::custom('transactions.created_at', new BetweenFilter),
                'transactions.update_count',
                'transactions.product_type',
                'products.kurdish_name',
                'users.kurdish_name',
            ])
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('transactions.id', 'desc');
            })
            ->addSubSelect(
                'update_count',
                Activity::selectRaw('count(*) as update_count')
                    ->whereColumn('activity_log.subject_id', 'transactions.id')
                    ->where('activity_log.subject_type', 'App\\Models\\Transaction')
            )
            // ->where('update_count', '>', 2)
            ->jsonJPaginate();

        $rows = collect($paginatedRows['rows']);

        $transaction_ids = $rows->pluck('transaction_id');

        $history = Activity::whereIn('subject_id', $transaction_ids)
            ->where(['subject_type' => 'App\\Models\\Transaction'])
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
            $updates = $historyGroupedById[$row['transaction_id']];
            $row['updates'] = $updates;

            return $row;
        });

        $paginatedRows['rows'] = $rows;

        return array_merge($paginatedRows, ['stat_rows' => []]);
    }

    public function printTransactionsActivityReport(Request $request)
    {
        if (getFilterRule('transactions.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $rows = JQueryBuilder::for(Transaction::class)
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('products', 'products.id', '=', 'transactions.product_id', 'LEFT OUTER')
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
                    transactions.id as transaction_id,
                    users.kurdish_name as user_name,
                    transactions.product_type,
                    products.kurdish_name as product_name,
                    transactions.created_at as date
                ')
            ->allowedFilters(
                AllowedFilter::custom('activity_count', new ExcludeFilter),
                'transactions.id',
                'transactions.created_at',
                'transactions.product_type',
                'products.kurdish_name',
                'users.kurdish_name'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('transactions.id', 'desc');
            })
            ->get();

        return view('reports.activities.printTransactionsActivityReport', compact(['rows']));
    }
}
