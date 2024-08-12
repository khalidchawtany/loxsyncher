<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;
use App\Http\Controllers\Reports\BaseReportController;

use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class DailyReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showDailyReport()
    {
        return view('reports.daily');
    }

    public function downloadDailyReports(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $transactions = $this->getDailyReportQuery($request)
            ->get();

        $this->createExcel($transactions->toArray(), [
            'Result',
            'Unit',
            'Amount',
            'Paid Amount',
            'Plate',
            'Office',
            'Product Type',
            'Product Name',
            'Department',
            'T. Date',
            'T. Id',
            'Payment. Id',
        ], 'DailyReports' . now() . '.xlsx');
    }

    public function listDailyReport(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getDailyReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getDailyReportStats();

        //TODO: if more than one trucks enters a transactions then the sum will be
        //misleading
        $stats = [
            [
                'name' => 'Transaction Trucks Count',
                'value' => $stats->transaction_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Amount Sum',
                'value' => $stats->amount_sum,
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    public function printDailyReport(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $transactions = $this->getDailyReportQuery($request)
            ->get();

        $stats = $this->getDailyReportStats();

        $info = [
            'amount_sum' => $stats->amount_sum,
            'transaction_count' => $stats->transaction_count,
        ];

        return view('reports.printDailyReport', compact(['info', 'transactions']));
    }

    private function getDailyReportQuery($request)
    {
        return JQueryBuilder::for(Transaction::class)
            ->whereIn('products.department_id', request()->user_departments)
            ->leftJoin('transaction_checks_view', 'transaction_checks_view.transaction_id', 'transactions.id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->leftJoin('trucks', 'trucks.id', 'transactions.truck_id')
            ->join('payments', 'payments.transaction_id', '=', 'transactions.id', 'LEFT OUTER')
            ->allowedFilters(
                'transaction_checks_view.status',
                'transactions.unit',
                'transactions.amount',
                'payments.amount',
                'trucks.plate',
                'offices.name',
                'transactions.product_type',
                'category_id',
                'products.kurdish_name',
                'departments.name',
                AllowedFilter::custom('transactions.date_time', new BetweenFilter),
                'transactions.batch_count',
                'transactions.id',
                'payments.id'
            )
            ->selectRaw('
                    transaction_checks_view.status as result,
                    transactions.unit as unit,
                    transactions.amount as amount,
                    payments.amount as paid_amount,
                    trucks.plate as plate,
                    offices.name as office_name,
                    transactions.product_type as product_type,
                    products.kurdish_name as product_name,
                    departments.name as department_name,
                    transactions.date_time as transaction_date,
                    transactions.id as transaction_id,
                    transactions.batch_count as batch_count,
                    payments.id as payment_id
                ')
            ->where('transactions.deleted_at', null)
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                /* return $query->orderBy($request->sort, $request->order); */
                return $query->orderBy('transactions.id', 'desc');
            });
    }

    private function getDailyReportStats()
    {
        return JQueryBuilder::for(Transaction::class)
            ->whereIn('products.department_id', request()->user_departments)
            ->join('payments', 'payments.transaction_id', 'transactions.id')
            ->leftJoin('transaction_checks_view', 'transaction_checks_view.transaction_id', 'transactions.id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->leftJoin('trucks', 'trucks.id', 'transactions.truck_id')
            ->allowedFilters(
                'transaction_checks_view.status',
                'transactions.unit',
                'transactions.amount',
                'payments.amount',
                'trucks.plate',
                'offices.name',
                'transactions.product_type',
                'category_id',
                'products.kurdish_name',
                'departments.name',
                'transactions.batch_count',
                AllowedFilter::custom('transactions.date_time', new BetweenFilter),
                'transactions.id',
                'payments.id'
            )
            ->selectRaw('
                    sum(transactions.amount) as amount_sum,
                    count(transactions.id) as transaction_count
                ')
            ->first();
    }
}
