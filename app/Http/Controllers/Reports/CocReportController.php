<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;
use App\Http\Controllers\Reports\BaseReportController;

use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class CocReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showCocReport()
    {
        return view('reports.coc');
    }

    public function listCocReport(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getCocReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getCocReportStats();

        //TODO: if more than one trucks enters a transactions then the sum will be
        //misleading
        $stats = [
            [
                'name' => 'Total Inspected Trucks',
                'value' => $stats->transaction_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Total Amount',
                'value' => number_format($stats->amount_paid_sum) . ' IQD',
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    public function printCocReport(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $transactions = $this->getCocReportQuery($request)
            ->get();

        $stats = $this->getCocReportStats();

        $info = [
            'transaction_count' => $stats->transaction_count,
            'amount_paid_sum' => $stats->amount_paid_sum,
        ];

        return view('reports.printCocReport', compact(['info', 'transactions']));
    }

    public function printCocTransactions(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        // Only take in date filter for coc transactions
        $transactions = JQueryBuilder::for(Transaction::class)
            ->with(['product.department', 'truck', 'payments.user', 'inspector', 'merchant', 'office', 'user', 'checkStatusViews'])
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->join('payments', 'payments.transaction_id', '=', 'transactions.id', 'LEFT OUTER')
            ->allowedFilters(
                AllowedFilter::custom('transactions.date_time', new BetweenFilter)
            )
            ->selectRaw(' transactions.* ')
            ->where('transactions.deleted_at', null)
            ->whereRaw('(products.coc = 1 OR transactions.is_coc = 1)')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('transactions.id', 'desc');
            })->get();

        $print_payments_after_transaction = true;
        $grap_destination_from_transaction = true;
        $print_stamp = true;

        return view('transactions.printTransaction', compact([
            'transactions',
            'grap_destination_from_transaction',
            'print_stamp',
            'print_payments_after_transaction',
        ]));
    }

    private function getCocReportQuery($request)
    {
        $borderName = \App\Models\AppSetting::where(['name' => 'site_name'])->first()->value;

        return JQueryBuilder::for(Transaction::class)
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('trucks', 'trucks.id', 'transactions.truck_id')
            ->join('payments', 'payments.transaction_id', '=', 'transactions.id', 'LEFT OUTER')
            ->allowedFilters(
                AllowedFilter::custom('transactions.date_time', new BetweenFilter),
                'transactions.id',
                'trucks.plate',
                'products.name',
                'transactions.amount',
                'transactions.unit',
                'payments.id',
                'payments.date_time',
                'payments.amount'
            )
            ->selectRaw("
                    DATE(transactions.date_time) as transaction_date,
                    transactions.id as transaction_id,
                    '' as exporter_name,
                    '' as importer_name,
                    '$borderName' as border_name,
                    trucks.plate as truck_plate,
                    products.name as product_name,
                    transactions.amount as amount,
                    transactions.unit as unit,
                    payments.id as payment_id,
                    DATE(payments.date_time) as payment_date_time,
                    payments.amount as paid_amount
                    ")
            ->where('transactions.deleted_at', null)
            ->whereRaw('(products.coc = 1 OR transactions.is_coc = 1)')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                /* return $query->orderBy($request->sort, $request->order); */
                return $query->orderBy('transactions.id', 'desc');
            });
    }

    private function getCocReportStats()
    {
        return JQueryBuilder::for(Transaction::class)
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('trucks', 'trucks.id', 'transactions.truck_id')
            ->join('payments', 'payments.transaction_id', '=', 'transactions.id', 'LEFT OUTER')
            ->allowedFilters(
                AllowedFilter::custom('transactions.date_time', new BetweenFilter),
                'transaction_checks_view.status',
                'transactions.unit',
                'transactions.amount',
                'trucks.plate',
                'payments.amount',
                'payments.date_time',
                'transactions.product_type',
                'products.name',
                'transactions.date_time',
                'transactions.id',
                'payments.id'
            )
            ->whereRaw('(products.coc = 1 OR transactions.is_coc = 1)')
            ->selectRaw('
                    sum(transactions.amount) as amount_sum,
                    sum(payments.amount) as amount_paid_sum,
                    count(transactions.id) as transaction_count
                ')
            ->first();
    }

    public function downloadCocReport(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $transactions = $this->getCocReportQuery($request)
            ->get();

        $stats = $this->getCocReportStats();

        $info = [
            'transaction_count' => $stats->transaction_count,
            'amount_paid_sum' => $stats->amount_paid_sum,
        ];

        return collect([
            [
                'Inspection Date',
                'RD#',
                'Exporter Name',
                'Importer Name',
                'Border Name',
                'Truck Plate',
                'Goods Description',
                'Quantity',
                'Type of Package',
                'Invoice Number',
                'Invoice Date',
                'Invoice Amount (IQD)',
            ],
            $transactions->toArray(),
        ])->downloadExcel('coc.html', null, false);
    }
}
