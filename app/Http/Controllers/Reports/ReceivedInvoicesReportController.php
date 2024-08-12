<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;

use App\Models\ReceivedInvoice;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Http\Controllers\Reports\BaseReportController;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\BetweenFilter;

class ReceivedInvoicesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showReceivedInvoicesReport()
    {
        return view('reports.received_invoices.received_invoices');
    }

    public function listReceivedInvoicesReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('received_invoices.received_at');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('received_invoices.received_at');

        if (!$this->valiateDaterange('view_received_invoices_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $jsonJPaginate = $this->getReceivedInvoicesReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getReceivedInvoicesReportStat();

        $stats = [
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

    public function printReceivedInvoicesReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('received_invoices.received_at');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('received_invoices.received_at');

        if (!$this->valiateDaterange('view_received_invoices_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $received_invoices = $this->getReceivedInvoicesReportQuery($request)
            ->get();

        $stats = $this->getReceivedInvoicesReportStat();

        $info = [
            'received_invoices_amount' => $stats->received_invoices_amount,
            'received_invoices_count' => $stats->received_invoices_count,
        ];

        return view('reports.received_invoices.print_received_invoices_report', compact(['info', 'received_invoices']));
    }

    public function downloadReceivedInvoicesReports(Request $request)
    {
        if (FilterRulesHelper::get('received_invoices.received_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $ReceivedInvoices = $this->getReceivedInvoicesReportQuery($request)
            ->get();

        $this->createExcel($ReceivedInvoices->toArray(), [
            'Payment #',
            'Paid Amount',
            'Office',
            'Received By',
            'Received At',
        ], 'ReceivedInvoicesReports' . now() . '.xlsx');
    }

    private function getReceivedInvoicesReportQuery($request)
    {
        return  JQueryBuilder::for(ReceivedInvoice::class)
            ->join('payments', 'payments.id', 'received_invoices.payment_id')
            ->join('transactions', 'transactions.id', 'payments.transaction_id')
            ->join('offices', 'offices.id', 'transactions.office_id')
            ->join('users', 'users.id', '=', 'received_invoices.received_by', 'LEFT OUTER')
            ->allowedFilters([
                'offices.name',
                'users.kurdish_name',
                AllowedFilter::custom('received_invoices.received_at', new BetweenFilter),
                'payment_id',
                'payments.amount',
            ])
            ->selectRaw('
            received_invoices.payment_id,
            payments.amount as payment_amount,
            offices.name as office_name,
            users.kurdish_name as received_by_name,
            received_invoices.received_at
      ')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('received_invoices.id', 'desc');
            });
    }

    private function getReceivedInvoicesReportStat()
    {
        return  JQueryBuilder::for(ReceivedInvoice::class)
            ->join('payments', 'payments.id', 'received_invoices.payment_id')
            ->join('transactions', 'transactions.id', 'payments.transaction_id')
            ->join('offices', 'offices.id', 'transactions.office_id')
            ->join('users', 'users.id', '=', 'received_invoices.received_by', 'LEFT OUTER')
            ->allowedFilters([
                'offices.name',
                'users.kurdish_name',
                AllowedFilter::custom('received_invoices.received_at', new BetweenFilter),
                'payment_id',
                'payments.amount',
            ])
            ->selectRaw('
                    sum(payments.amount) as received_invoices_amount,
                    count(received_invoices.id) as received_invoices_count
                ')
            ->first();
    }
}
