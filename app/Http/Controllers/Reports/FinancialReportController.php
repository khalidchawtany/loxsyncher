<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;
use App\Http\Controllers\Reports\BaseReportController;

use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class FinancialReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showFinancialReport()
    {
        return view('reports.financial');
    }

    public function downloadFinancialReports(Request $request)
    {
        if (FilterRulesHelper::get('payments.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $transactions = $this->getFinancialReportQuery($request)
            ->get();

        $this->createExcel($transactions->toArray(), [
            'Result',
            'Paid Amount',
            'Office',
            'Product Type',
            'Product Name',
            'Department',
            'T. Date',
            'T. Id',
            'Payment. Id',
            'Invoice #',
            'P. Date',
        ], 'FinancialReports' . now() . '.xlsx');
    }

    public function listFinancialReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('payments.date_time');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('payments.date_time');

        if (!$this->valiateDaterange('view_financial_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $jsonJPaginate = $this->getFinancialReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getFinancialReportStat();

        $stats = [
            [
                'name' => 'Transaction Count',
                'value' => $stats->transaction_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Paid Amount Sum',
                'value' => number_format($stats->paid_amount_sum) . ' IQD',
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    public function printFinancialReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('payments.date_time');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('payments.date_time');

        if (!$this->valiateDaterange('view_financial_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $transactions = $this->getFinancialReportQuery($request)
            ->get();

        $stats = $this->getFinancialReportStat();

        $info = [
            'paid_amount' => $stats->paid_amount_sum,
            'transaction_count' => $stats->transaction_count,
        ];

        return view('reports.printFinancialReport', compact(['info', 'transactions']));
    }

    private function getFinancialReportQuery($request)
    {
        return  JQueryBuilder::for(Transaction::class)
            ->whereIn('products.department_id', request()->user_departments)
            ->join('payments', 'payments.transaction_id', 'transactions.id')
            ->leftJoin('transaction_checks_view', 'transaction_checks_view.transaction_id', 'transactions.id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->allowedFilters(
                'transaction_checks_view.status',
                'payments.amount',
                'offices.name',
                'transactions.product_type',
                'products.kurdish_name',
                'departments.name',
                'transactions.date_time',
                AllowedFilter::exact('transactions.id'),
                'transactions.amount',
                AllowedFilter::exact('payments.id'),
                AllowedFilter::exact('payments.invoice_number'),
                AllowedFilter::custom('payments.date_time', new BetweenFilter)
            )
            ->selectRaw('
                    transaction_checks_view.status as result,
                    payments.amount as paid_amount,
                    offices.name as office_name,
                    transactions.product_type as product_type,
                    products.kurdish_name as product_name,
                    departments.name as department_name,
                    transactions.date_time as transaction_date,
                    transactions.id as transaction_id,
                    payments.id as payment_id,
                    payments.invoice_number,
                    payments.date_time as payment_date
                ')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('transactions.id', 'desc');
            });
    }

    private function getFinancialReportStat()
    {
        return JQueryBuilder::for(Transaction::class)
            ->whereIn('products.department_id', request()->user_departments)
            ->join('payments', 'payments.transaction_id', 'transactions.id')
            ->leftJoin('transaction_checks_view', 'transaction_checks_view.transaction_id', 'transactions.id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->allowedFilters(
                'transaction_checks_view.status',
                'payments.amount',
                'offices.name',
                'transactions.product_type',
                'products.kurdish_name',
                'departments.name',
                'transactions.date_time',
                'transactions.id',
                'transactions.amount',
                'payments.id',
                'payments.invoice_number',
                AllowedFilter::custom('payments.date_time', new BetweenFilter)
            )
            ->selectRaw('
                    sum(payments.amount) as paid_amount_sum,
                    count(transactions.id) as transaction_count
                ')
            ->first();
    }
}
