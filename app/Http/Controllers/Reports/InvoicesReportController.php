<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Http\Controllers\Reports\BaseReportController;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\BetweenFilter;

class InvoicesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showInvoicesReport()
    {
        return view('reports.invoices.invoices');
    }

    public function listInvoicesReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('payments.date_time');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('payments.date_time');

        if (!$this->valiateDaterange('view_invoices_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $jsonJPaginate = $this->getInvoicesReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getInvoicesReportStat();

        $stats = [
            [
                'name' => ' Invoice Count',
                'value' => $stats->invoices_count,
                'group' => 'Statics',
            ],
            [
                'name' => ' Invoice Amount',
                'value' => number_format($stats->invoices_amount) . ' IQD',
                'group' => 'Statics',
            ],

            [
                'name' => 'Received Invoice Count',
                'value' => $stats->received_invoices_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Received Invoice Amount',
                'value' => number_format($stats->received_invoices_amount) . ' IQD',
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate->toArray(), ['stat_rows' => $stats]);
    }

    public function printInvoicesReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('payments.date_time');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('payments.date_time');

        if (!$this->valiateDaterange('view_invoices_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $invoices = $this->getInvoicesReportQuery($request)
            ->get();

        $stats = $this->getInvoicesReportStat();

        $info = [
            'invoices_amount' => $stats->invoices_amount,
            'invoices_count' => $stats->invoices_count,
            'received_invoices_amount' => $stats->received_invoices_amount,
            'received_invoices_count' => $stats->received_invoices_count,
        ];

        return view('reports.invoices.print_invoices_report', compact(['info', 'invoices']));
    }

    public function downloadInvoicesReports(Request $request)
    {
        if (FilterRulesHelper::get('payments.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $Invoices = $this->getInvoicesReportQuery($request)
            ->get();

        $this->createExcel($Invoices->toArray(), [
            'Payment #',
            'Paid Amount',
            'Payment Date',
            'Office',
            'Received By',
            'Received At',
        ], 'InvoicesReports' . now() . '.xlsx');
    }

    private function getInvoicesReportQuery($request)
    {
        return  JQueryBuilder::for(Payment::class)
            ->join('transactions', 'transactions.id', 'payments.transaction_id')
            ->join('offices', 'offices.id', 'transactions.office_id')
            ->join('received_invoices', 'received_invoices.payment_id', '=', 'payments.id', 'LEFT OUTER')
            ->join('users', 'users.id', '=', 'received_invoices.received_by', 'LEFT OUTER')
            ->allowedFilters([
                'offices.name',
                'users.kurdish_name',
                AllowedFilter::custom('payments.date_time', new BetweenFilter),
                'payments.id',
                'payments.amount',
                'received_invoices.received_at',
            ])
            ->selectRaw('
            payments.id as payment_id,
            payments.amount as payment_amount,
            payments.date_time as payment_date,
            offices.name as office_name,
            users.kurdish_name as received_by_name,
            received_invoices.received_at
      ')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('payments.id', 'desc');
            });
    }

    private function getInvoicesReportStat()
    {
        return  JQueryBuilder::for(Payment::class)
            ->join('transactions', 'transactions.id', 'payments.transaction_id')
            ->join('offices', 'offices.id', 'transactions.office_id')
            ->join('received_invoices', 'received_invoices.payment_id', '=', 'payments.id', 'LEFT OUTER')
            ->join('users', 'users.id', '=', 'received_invoices.received_by', 'LEFT OUTER')
            ->allowedFilters([
                'offices.name',
                'users.kurdish_name',
                AllowedFilter::custom('payments.date_time', new BetweenFilter),
                'payments.id',
                'payments.amount',
                'received_invoices.received_at',
            ])
            ->selectRaw('
                    sum(payments.amount) as invoices_amount,
                    count(payments.id) as invoices_count,
                    count(received_invoices.id) as received_invoices_count,
                    SUM(IF(received_invoices.id > 0, payments.amount, 0)) as received_invoices_amount
                ')
            ->first();
    }
}
