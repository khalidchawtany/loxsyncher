<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;

use App\Models\IncinerationPayment;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Http\Controllers\Reports\BaseReportController;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\BetweenFilter;

class IncinerationReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showIncinerationsReport()
    {
        return view('reports.incinerations');
    }

    public function listIncinerationsReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('incineration_payments.date');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('incineration_payments.date');
        if (!$this->valiateDaterange('view_incinerations_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $jsonJPaginate = $this->getIncinerationsReportQuery($request)
            ->jsonJPaginate();

        $stats = $this->getIncinerationsReportStats();

        //TODO: if more than one trucks enters a transactions then the sum will be
        //misleading
        $stats = [
            [
                'name' => 'Incineration Count',
                'value' => $stats->incineration_count,
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

    public function printIncinerationsReport(Request $request)
    {
        $dateRange = FilterRulesHelper::get('incineration_payments.date');
        if ($dateRange == null || !str_contains($dateRange, ',')) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('incineration_payments.date');
        if (!$this->valiateDaterange('view_incinerations_report', $dateRange)) {
            return ezReturnErrorMessage('Invalid date range');
        }

        $incinerations = $this->getIncinerationsReportQuery($request)
            ->get();

        $stats = $this->getIncinerationsReportStats();

        $info = [
            'amount_sum' => $stats->amount_sum,
            'incineration_count' => $stats->incineration_count,
        ];

        return view('reports.printIncinerationsReport', compact(['info', 'incinerations']));
    }

    private function getIncinerationsReportQuery($request)
    {
        return JQueryBuilder::for(IncinerationPayment::class)
            ->leftJoin('incinerations', 'incinerations.id', 'incineration_payments.incineration_id')
            ->leftJoin('transactions', 'transactions.id', 'incinerations.transaction_id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->allowedFilters(
                AllowedFilter::custom('incineration_payments.date', new BetweenFilter),
                'incineration_payments.amount',
                'offices.name',
                'transactions.product_type',
                'products.kurdish_name',
                'incineration_payments.id'
            )
            ->selectRaw('
                    incineration_payments.date as invoice_date,
                    incineration_payments.amount as paid_amount,
                    offices.name as office_name,
                    transactions.product_type as product_type,
                    products.kurdish_name as product_name,
                    incineration_payments.id as invoice_number
                ')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                /* return $query->orderBy($request->sort, $request->order); */
                return $query->orderBy('incineration_payments.id', 'desc');
            });
    }

    private function getIncinerationsReportStats()
    {
        return JQueryBuilder::for(IncinerationPayment::class)
            ->leftJoin('incinerations', 'incinerations.id', 'incineration_payments.incineration_id')
            ->leftJoin('transactions', 'transactions.id', 'incinerations.transaction_id')
            ->leftJoin('products', 'products.id', 'transactions.product_id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->leftJoin('offices', 'offices.id', 'transactions.office_id')
            ->allowedFilters(
                AllowedFilter::custom('incineration_payments.date', new BetweenFilter),
                'incineration_payments.amount',
                'offices.name',
                'transactions.product_type',
                'products.kurdish_name',
                'incineration_payments.id'
            )
            ->selectRaw('
                    sum(incineration_payments.amount) as amount_sum,
                    count(incineration_payments.id) as incineration_count
                ')
            ->first();
    }
}
