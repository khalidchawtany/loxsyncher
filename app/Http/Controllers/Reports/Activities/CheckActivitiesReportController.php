<?php

namespace App\Http\Controllers\Reports\Activities;

use App\Models\Check;
use App\Http\Controllers\Reports\BaseReportController;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Adapters\JQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\BetweenFilter;

class CheckActivitiesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showChecksReport()
    {
        return view('reports.checks');
    }

    public function listChecksReport(Request $request)
    {
        if (getFilterRule('checks.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = JQueryBuilder::for(Check::class)
            ->join('batches', 'batches.id', 'checks.batch_id')
            ->join('check_types', 'check_types.id', '=', 'checks.check_type_id', 'LEFT OUTER')
            ->join('transactions', 'transactions.id', '=', 'batches.transaction_id', 'LEFT OUTER')
            ->join('products', 'products.id', 'batches.batch_product_id')
            ->join('users', 'users.id', 'checks.updated_by')
            ->selectRaw('
                        checks.id as check_id,
                        checks.status as check_status,
                        checks.created_at as check_date,
                        checks.update_count as update_count,
                        transactions.product_type as product_type,
                        products.kurdish_name as product_name,
                        users.kurdish_name as user_name,
                        check_types.category as lab,
                        check_types.subcategory as test

                ')
            ->AllowedSorts([
                'checks.id',
                'checks.status',
                'checks.created_at',
                'checks.update_count',
                'transactions.product_type',
                'products.kurdish_name',
                'users.kurdish_name',
                'check_types.category',
                'check_types.subcategory'
            ])
            ->allowedFilters(
                'checks.id',
                'checks.status',
                AllowedFilter::custom('checks.created_at', new BetweenFilter),
                'checks.update_count',
                'transactions.product_type',
                'products.kurdish_name',
                'users.kurdish_name',
                'check_types.category',
                'check_types.subcategory'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('checks.id', 'desc');
            })
            ->jsonJPaginate();

        $stats = [];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    public function showCheckUpdateHistory(Request $request)
    {
        $check_id = $request->check_id;

        $history = Activity::where([
            'subject_id' => $check_id,
            'subject_type' => 'App\Models\Check',
        ])
            ->join('users', 'users.id', 'activity_log.causer_id')
            ->join('checks', 'checks.id', '=', 'activity_log.subject_id', 'LEFT OUTER')
            ->join('batches', 'batches.id', '=', 'checks.batch_id', 'LEFT OUTER')
            ->selectRaw('
                    activity_log.created_at as date,
                    users.kurdish_name as user_name,
                    activity_log.properties as props,
                    checks.id as check_id,
                    batches.id as batch_id,
                    batches.transaction_id as transaction_id
                ')
            ->get();

        return view('reports.check_update_history', compact(['history']));
    }

    public function printChecksReport(Request $request)
    {
        if (getFilterRule('checks.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $rows = JQueryBuilder::for(Check::class)
            ->join('batches', 'batches.id', 'checks.batch_id')
            ->join('check_types', 'check_types.id', '=', 'checks.check_type_id', 'LEFT OUTER')
            ->join('transactions', 'transactions.id', '=', 'batches.transaction_id', 'LEFT OUTER')
            ->join('products', 'products.id', 'batches.batch_product_id')
            ->join('users', 'users.id', 'checks.updated_by')
            ->selectRaw('
                        checks.id as id,
                        checks.status as check_status,
                        checks.created_at as check_date,
                        checks.update_count as update_count,
                        transactions.product_type as product_type,
                        products.kurdish_name as product_name,
                        users.kurdish_name as user_name,
                        check_types.category as lab,
                        check_types.subcategory as test
                ')
            ->AllowedSorts([
                'checks.id',
                'checks.status',
                'checks.created_at',
                'checks.update_count',
                'transactions.product_type',
                'products.kurdish_name',
                'users.kurdish_name',
                'check_types.category',
                'check_types.subcategory'
            ])
            ->allowedFilters(
                'checks.id',
                'checks.status',
                AllowedFilter::custom('checks.created_at', new BetweenFilter),
                'checks.update_count',
                'transactions.product_type',
                'products.kurdish_name',
                'users.kurdish_name',
                'check_types.category',
                'check_types.subcategory'
            )
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('checks.id', 'desc');
            })
            ->with(['activities:id,subject_id,description,causer_id,created_at,properties'])
            ->get();

        $causers = User::FetchCausers($rows);

        return view('reports.printChecksActivityReport', compact(['rows', 'causers']));
    }
}
