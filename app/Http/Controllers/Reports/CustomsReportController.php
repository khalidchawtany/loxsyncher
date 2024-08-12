<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Http\Controllers\Reports\BaseReportController;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\BetweenFilter;

class CustomsReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showCustomsReport()
    {
        return view('reports.customs');
    }

    public function listCustomsReport(Request $request)
    {
        if (FilterRulesHelper::get('payments.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getCustomsReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getCustomsReportStats();

        //TODO: if more than one trucks enters a transactions then the sum will be
        //misleading
        $stats = [
            [
                'name' => 'Transaction Count',
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

    public function printCustomsReport(Request $request)
    {
        if (FilterRulesHelper::get('payments.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $transactions = $this->getCustomsReportQuery($request)
            ->get();

        $stats = $this->getCustomsReportStats();

        $info = [
            'amount_sum' => $stats->amount_sum,
            'transaction_count' => $stats->transaction_count,
        ];

        return view('reports.printCustomsReport', compact(['info', 'transactions']));
    }

    private function getCustomsReportQuery($request)
    {
        return JQueryBuilder::for(Transaction::class)
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->leftJoin('trucks', 'trucks.id', 'transactions.truck_id')
            ->join('payments', 'payments.transaction_id', '=', 'transactions.id')
            ->whereIn('products.department_id', request()->user_departments)
            ->allowedFilters(
                AllowedFilter::custom('payments.date_time', new BetweenFilter),
                'payments.date_time',
                'payments.amount',
                'trucks.plate',
                'offices.name',
                'transactions.product_type',
                'products.kurdish_name',
                'payments.invoice_number'
            )
            ->selectRaw('
                    payments.amount as paid_amount,
                    trucks.plate as plate,
                    offices.name as office_name,
                    transactions.product_type as product_type,
                    products.kurdish_name as product_name,
                    payments.id as invoice_number
                ')
            ->where('transactions.deleted_at', null)
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('payments.id', 'asc');
            });
    }

    private function getCustomsReportStats()
    {
        return JQueryBuilder::for(Transaction::class)
            ->join('payments', 'payments.transaction_id', 'transactions.id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->leftJoin('trucks', 'trucks.id', 'transactions.truck_id')
            ->whereIn('products.department_id', request()->user_departments)
            ->allowedFilters(
                AllowedFilter::custom('payments.date_time', new BetweenFilter),
                'payments.amount',
                'trucks.plate',
                'offices.name',
                'transactions.product_type',
                'products.kurdish_name',
                'payments.invoice_number'
            )
            ->selectRaw('
                    sum(payments.amount) as amount_sum,
                    count(transactions.id) as transaction_count
                ')
            ->first();
    }
}
