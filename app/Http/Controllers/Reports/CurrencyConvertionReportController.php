<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Controllers\Reports\BaseReportController;
use App\Filters\BetweenFilter;

class CurrencyConvertionReportController extends BaseReportController
{
    private $filters;

    public function __construct()
    {
        parent::__construct();

        $this->filters = [
            'payments.amount',
            'payments.currency_convertion_ratio',
            'offices.name',
            'transactions.product_type',
            'products.kurdish_name',
            'departments.name',
            'transactions.date_time',
            AllowedFilter::exact('transactions.id'),
            'transactions.amount',
            AllowedFilter::exact('payments.id'),
            AllowedFilter::exact('payments.invoice_number'),
            AllowedFilter::custom('payments.date_time', new BetweenFilter),
        ];
    }

    public function showCurrencyConvertionReport()
    {
        return view('reports.currency_convertion');
    }

    public function downloadCurrencyConvertionReports(Request $request)
    {
        if (FilterRulesHelper::get('payments.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $transactions = $this->getCurrencyConvertionReportQuery($request)
            ->get();

        $this->createExcel($transactions->toArray(), [
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
        ], 'CurrencyConvertionReports' . now() . '.xlsx');
    }

    public function listCurrencyConvertionReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('payments.date_time');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('payments.date_time');

        if (!$this->valiateDaterange('view_currency_convertion_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $jsonJPaginate = $this->getCurrencyConvertionReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getCurrencyConvertionReportStat();

        $stats = [
            [
                'name' => 'Transaction Count',
                'value' => $stats->transaction_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Paid Amount IQD',
                'value' => number_format($stats->sum_paid_amount) . ' IQD',
                'group' => 'Statics',
            ],
            [
                'name' => 'Paid Amount USD',
                'value' => number_format($stats->sum_paid_amount_usd, 2) . ' USD',
                'group' => 'Statics',
            ],
            [
                'name' => 'Diff IQD',
                'value' => number_format($stats->sum_diff_iqd) . ' IQD',
                'group' => 'Statics',
            ],
            [
                'name' => 'Diff USD',
                'value' => number_format($stats->sum_diff_usd, 2) . ' USD',
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    public function printCurrencyConvertionReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('payments.date_time');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('payments.date_time');

        if (!$this->valiateDaterange('view_currency_convertion_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $transactions = $this->getCurrencyConvertionReportQuery($request)
            ->get();

        $stats = $this->getCurrencyConvertionReportStat();

        $info = [
            'sum_paid_amount' => $stats->sum_paid_amount,
            'sum_paid_amount_usd' => $stats->sum_paid_amount_usd,
            'sum_diff_usd' => $stats->sum_diff_usd,
            'sum_diff_iqd' => $stats->sum_diff_iqd,
            'transaction_count' => $stats->transaction_count,
        ];

        return view('reports.printCurrencyConvertionReport', compact(['info', 'transactions']));
    }

    private function getCurrencyConvertionReportQuery($request)
    {
        return  JQueryBuilder::for(Transaction::class)
            ->whereIn('products.department_id', request()->user_departments)
            ->join('payments', 'payments.transaction_id', 'transactions.id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->allowedFilters($this->filters)
            ->selectRaw('
                    payments.amount as paid_amount,
                    CEIL((payments.amount / payments.currency_convertion_ratio)) as paid_amount_usd,
                    ((CEIL((payments.amount / payments.currency_convertion_ratio))) - (payments.amount / payments.currency_convertion_ratio)) as diff_usd,
                    ROUND((((CEIL((payments.amount / payments.currency_convertion_ratio))) - (payments.amount / payments.currency_convertion_ratio)) * payments.currency_convertion_ratio)) as diff_iqd,
                    payments.currency_convertion_ratio,
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

    private function getCurrencyConvertionReportStat()
    {
        return JQueryBuilder::for(Transaction::class)
            ->whereIn('products.department_id', request()->user_departments)
            ->join('payments', 'payments.transaction_id', 'transactions.id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->allowedFilters($this->filters)
            ->selectRaw('
                    sum(payments.amount) as sum_paid_amount,
                    count(transactions.id) as transaction_count,

                    SUM(
                      CEIL(payments.amount / payments.currency_convertion_ratio)
                    ) as sum_paid_amount_usd,
                    SUM(
                      CEIL( payments.amount / payments.currency_convertion_ratio)
                      - (payments.amount / payments.currency_convertion_ratio)
                    ) as sum_diff_usd,
                    ROUND(
                        SUM(
                              (
                                CEIL( payments.amount / payments.currency_convertion_ratio)
                                - ( payments.amount / payments.currency_convertion_ratio)
                              )
                              * payments.currency_convertion_ratio
                        )
                    ) as sum_diff_iqd
                ')
            ->first();
    }
}
