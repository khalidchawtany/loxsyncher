<?php

namespace App\Http\Controllers\Reports\Activities;

use App\Http\Controllers\Reports\BaseReportController;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Models\Swab;
use Illuminate\Support\Facades\DB;

class SarsCov2ActivitiesReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showSarsCov2Report()
    {
        return view('reports.sars_cov2');
    }

    public function listSarsCov2Report(Request $request)
    {
        if (getFilterRule('swabs.created_at') == null) {
            return ezReturnErrorMessage('Select a payment date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('swabs.created_at');
        if ($this->dateRangeWiderThanNumberOfDays($dateRange, 2)) {
            if (!auth()->user()->can('view_sars_cov2_report_for_any_date_range')) {
                return ezReturnErrorMessage('Date range is too big!');
            }
        }

        $jsonJPaginate = $this->getSarsCov2ReportQuery()
            ->jsonJPaginate();

        $stats = $this->getSarsCov2ReportStats();

        $stats = [
            [
                'name' => 'Total Patient Count',
                'value' => $stats->total_patient_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Total Amount',
                'value' => number_format($stats->total_amount),
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    public function printSarsCov2Report(Request $request)
    {
        if (getFilterRule('swabs.created_at') == null) {
            return ezReturnErrorMessage('Select a payment date range');
        }

        $dateRange = $this->getDateRangeFromFilterRules('swabs.created_at');
        if ($this->dateRangeWiderThanNumberOfDays($dateRange, 2)) {
            if (!auth()->user()->can('view_sars_cov2_report_for_any_date_range')) {
                return ezReturnErrorMessage('Date range is too big!');
            }
        }

        $swabs = $this->getSarsCov2ReportQuery()->get();

        $stats = $this->getSarsCov2ReportStats();

        $info = [
            'total_patient_count' => $stats->total_patient_count,
            'total_amount' => $stats->total_amount,
        ];

        return view('reports.printSarsCov2Report', compact(['info', 'swabs']));
    }

    public function printSarsCov2SpeedReport(Request $request)
    {
        if (getFilterRule('swabs.created_at') == null) {
            return ezReturnErrorMessage('Select a payment date range');
        }

        $resultFilter = getFilterRule('swabs.result');

        $resultDateRange = null;
        if (getFilterRule('swabs.date') != null) {
            $resultDateRange = $this->getDateRangeFromFilterRules('swabs.date');
        }

        $dateRange = $this->getDateRangeFromFilterRules('swabs.created_at');

        $daysInDateRange = $this->getDatesInDateRange($dateRange);

        $dateFrom = $dateRange->from->format('Y-m-d');

        $dateTo = $dateRange->to->format('Y-m-d');

        app()->request['filterRules'] = json_encode([]);

        $data = JQueryBuilder::for(Swab::class)
            ->allowedFilters(
                'swabs.date',
                'swabs.result',
                'swabs.created_at'
            )
            ->selectRaw('
                    DATE(swabs.created_at) AS payment_date,
                    COUNT(swabs.id) AS payment_count,
                    SUM(swabs.paid_amount) AS paid_amount
                ')
            ->whereRaw(" DATE(swabs.created_at) BETWEEN '$dateFrom' AND '$dateTo'")
            ->when($resultFilter !== null, function ($query) use ($resultFilter) {
                return $query->whereRaw(" swabs.result = '$resultFilter'");
            })
            ->when($resultDateRange != null, function ($query) use ($resultDateRange) {
                $resultDateFrom = $resultDateRange->from->format('Y-m-d');

                $resultDateTo = $resultDateRange->to->format('Y-m-d');

                return $query->whereRaw(" DATE(swabs.date)  BETWEEN '$resultDateFrom' AND '$resultDateTo'");
            })
            ->groupBy(DB::raw('date(swabs.created_at)'))
            ->orderBy(DB::raw('date(swabs.created_at)'), 'ASC')
            ->get()
            ->groupBy('payment_date');

        return view('reports.printSarsCov2SpeedReport', compact([
            'data',
            'dateFrom',
            'dateTo',
        ]));
    }

    private function getSarsCov2ReportQuery()
    {
        $filteredByResult = getFilterRule('swabs.result') !== null;
        $result = getFilterRule('swabs.result');
        $result = $result == '' ? null : $result;

        $filters = app()->request['filterRules'];

        $filters = collect(json_decode($filters))
            ->filter(function ($filter) {
                return $filter->field != 'swabs.result';
            });

        $tempFilters = app()->request['filterRules'];
        app()->request['filterRules'] = json_encode($filters);

        $queryBuilder = JQueryBuilder::for(Swab::class)
            ->join('people', 'people.id', '=', 'swabs.person_id', 'LEFT OUTER')
            ->allowedFilters(
                'people.name',
                'people.passport_number',
                'swabs.date',
                'swabs.result',
                'swabs.created_at',
                'swabs.paid_amount'
            )
            ->selectRaw('
                    swabs.id as id,
                    people.name as person_name,
                    people.passport_number as passport_number,
                    date(swabs.created_at) as payment_date,
                    swabs.date as result_date,
                    swabs.result as result,
                    swabs.paid_amount as paid_amount

                ')
            ->when($filteredByResult, function ($query) use ($result) {
                return $query->where('swabs.result', $result);
            })
            ->orderBy('swabs.created_at', 'DESC');

        app()->request['filterRules'] = $tempFilters;

        return $queryBuilder;
    }

    private function getSarsCov2ReportStats()
    {
        $sars_cov2Stats = $this->getSarsCov2ReportQuery()->get();

        return  (object) [
            'total_patient_count' => $sars_cov2Stats->count('id'),
            'total_amount' => $sars_cov2Stats->sum('paid_amount'),
        ];
    }
}
