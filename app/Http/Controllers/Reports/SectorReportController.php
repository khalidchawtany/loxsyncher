<?php

namespace App\Http\Controllers\Reports;
use App\Http\Controllers\Reports\BaseReportController;

use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use App\Filters\ExcludeFilter;
use App\Models\Department;
use App\Models\Incineration;
use App\Models\IncinerationPayment;
use App\Models\Payment;
use App\Utils\FilterRulesHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class SectorReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showSectorReport()
    {
        return view('reports.sector');
    }

    public function listSectorReport()
    {
        if (FilterRulesHelper::get('payment_date') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getSectorReportQuery()
            ->jsonJPaginate();

        $stats = $this->getSectorReportStats();

        $transactionStats = [
            [
                'name' => 'Total Invoice Count',
                'value' => $stats->total_payment_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Total Invoice Amount',
                'value' => number_format($stats->total_paid_amount) . ' IQD',
                'group' => 'Statics',
            ],
            [
                'name' => 'Sector Invoice Amount',
                'value' => number_format($stats->total_paid_amount_8_percent) . ' IQD',
                'group' => 'Statics',
            ],
        ];

        $incinerationStats = [
            [
                'name' => 'Total Invoice Count',
                'value' => $stats->total_payemnt_count_incineration,
                'group' => 'Incineration Statics',
            ],
            [
                'name' => 'Total Invoice Amount',
                'value' => number_format($stats->total_paid_amount_incineration) . ' IQD',
                'group' => 'Incineration Statics',
            ],
        ];

        $totalStats = [
            [
                'name' => 'All Invoices Count',
                'value' => $stats->total_payemnt_count_incineration + $stats->total_payment_count,
                'group' => 'Total Statics',
            ],
            [
                'name' => 'All Invoices Amount',
                'value' => number_format($stats->total_paid_amount_incineration + $stats->total_paid_amount) . ' IQD',
                'group' => 'Total Statics',
            ],
        ];

        return array_merge($jsonJPaginate, [
            'stat_rows' => $transactionStats,
            'incineration_stats' => $incinerationStats,
            'total_stats' => $totalStats,
        ]);
    }

    public function printSectorReport(Request $request)
    {
        if (FilterRulesHelper::get('payment_date') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $dailyPaymentSums = $this->getSectorReportQuery()->get();

        $stats = $this->getSectorReportStats();

        $info = [
            'total_payment_count' => $stats->total_payment_count,
            'total_paid_amount' => $stats->total_paid_amount,
            'total_paid_amount_8_percent' => $stats->total_paid_amount * 0.08,
        ];

        return view('reports.printSectorReport', compact(['info', 'dailyPaymentSums']));
    }

    public function printSectorSpeedReport(Request $request)
    {
        if (FilterRulesHelper::get('payment_date') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $hideRevokedPayments = FilterRulesHelper::get('hideRevokedPayments') != null;

        $dateRange = $this->getDateRangeFromFilterRules('payment_date');

        $daysInDateRange = $this->getDatesInDateRange($dateRange);

        $dateFrom = $dateRange->from->format('Y-m-d');

        $dateTo = $dateRange->to->addDay()->format('Y-m-d');

        $sectorsSpeedReportDays = JQueryBuilder::for(Payment::class)
            ->join('transactions', 'transactions.id', 'payments.transaction_id')
            ->join('products', 'products.id', 'transactions.product_id')
            ->join('departments', 'departments.id', 'products.department_id')
            ->allowedFilters(
                'department_id',
                'department_name',
                'payments.date_time',
                AllowedFilter::custom('payment_date', new ExcludeFilter),
                'payment_count',
                'paid_amount',
                'hideRevokedPayments'
            )
            ->selectRaw('
                    departments.name AS department_name,
                    DATE(payments.date_time) AS payment_date,
                    COUNT(payments.id) AS payment_count,
                    SUM(payments.amount) AS paid_amount,
                    SUM(CEIL((payments.amount / payments.currency_convertion_ratio))) AS paid_amount_usd
                ')
            ->whereRaw(" DATE(payments.date_time) BETWEEN '$dateFrom' AND '$dateTo'")
            ->when($hideRevokedPayments, function ($query) {
                return $query->whereRaw('payments.revoked_on IS NULL');
            })
            ->where('departments.include_in_speed_report', true)
            ->groupBy(DB::raw('departments.id, date(payments.date_time)'))
            ->orderBy('departments.name', 'ASC')
            ->orderBy('payment_date', 'ASC')
            ->get()
            ->groupBy('payment_date');

        $departmentNames = Department::query()
            ->where('include_in_speed_report', true)
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $incinerationDepartmentName = Incineration::$DEPARTMENT_NAME;
        $departmentNames[] = $incinerationDepartmentName;

        $incineratorSpeedReportDays = JQueryBuilder::for(IncinerationPayment::class)
            ->whereRaw(" DATE(incineration_payments.date) BETWEEN '$dateFrom' AND '$dateTo'")
            ->selectRaw("
                    '$incinerationDepartmentName' AS department_name,
                    DATE(incineration_payments.date) AS payment_date,
                    COUNT(incineration_payments.id) AS payment_count,
                    SUM(incineration_payments.amount) AS paid_amount
                ")
            /* ->groupBy(DB::raw('date(incinerations.date)')) */
            ->groupBy('payment_date')
            ->orderBy('payment_date', 'ASC')
            ->get()
            ->groupBy('payment_date');

        foreach ($daysInDateRange as $date) {
            if (!isset($incineratorSpeedReportDays[$date]) || !isset($incineratorSpeedReportDays[$date][0])) {
                continue;
            }
            $sectorsSpeedReportDays[$date][] = $incineratorSpeedReportDays[$date][0];
        }

        $data = $this->tabulate($sectorsSpeedReportDays, $departmentNames, $daysInDateRange);

        return view('reports.printSectorSpeedReport', compact([
            'data',
            'departmentNames',
            'dateFrom',
            'dateTo',
        ]));
    }

    private function getSectorReportQuery()
    {
        /* select departments.name as department, */
        /* 	date(payments.date_time) as payment_date, */
        /*     count(payments.id) as payment_count, */
        /*     sum(payments.amount) as paid_amount */
        /* from `payments` */
        /* inner join transactions on payments.transaction_id= transactions.id */
        /* inner join products on products.id= transactions.product_id */
        /* inner join departments on  departments.id = products.department_id */
        /* where date(payments.date_time) in ('2019-06-23', '2019-06-24', '2019-06-25') */
        /* 		AND departments.id in (1,2,3,4,5,6) */
        /* group by departments.id, */
        /* 		date(payments.date_time) */

        $dateRange = $this->getDateRangeFromFilterRules('payment_date');
        FilterRulesHelper::pop('payment_date');

        $daysInDateRange = collect($this->getDatesInDateRange($dateRange))
            ->map(function ($item) {
                return "'$item'";
            })
            ->join(',');

        $hideRevokedPayments = FilterRulesHelper::get('hideRevokedPayments') != null;

        $departmentId = FilterRulesHelper::get('department_id');
        $departmentFilter = '';
        if ($departmentId != null) {
            $departmentFilter = " AND departments.id = $departmentId";
        } else {
            $userDepartmentIds = collect(request()->user_departments)->join(',');
            $departmentFilter = " AND departments.id IN ($userDepartmentIds) ";
        }


        $queryBuilder = JQueryBuilder::for(Payment::class)
            ->join('transactions', 'transactions.id', 'payments.transaction_id')
            ->join('products', 'products.id', 'transactions.product_id')
            ->join('departments', 'departments.id', 'products.department_id')
            ->allowedFilters(
                [
                    'department_id',
                    'department_name',
                    'payments.date_time',
                    AllowedFilter::custom('payment_date', new ExcludeFilter),
                    'payment_count',
                    'paid_amount',
                    'hideRevokedPayments',
                ]
            )
            ->selectRaw('
                    departments.name AS department_name,
                    DATE(payments.date_time) AS payment_date,
                    COUNT(payments.id) AS payment_count,
                    SUM(payments.amount) AS paid_amount,
                    sum(transactions.batch_count) as batch_count,
                    sum(transactions.amount) as amount_sum
                ')
            ->whereRaw(" date(payments.date_time) in ($daysInDateRange) $departmentFilter")
            ->when($hideRevokedPayments, function ($query) {
                return $query->whereRaw('payments.revoked_on IS NULL');
            })
            ->groupBy(DB::raw('departments.id, date(payments.date_time)'))
            ->orderBy('departments.name', 'ASC')
            ->orderBy('payment_date', 'ASC');

        /* dd($queryBuilder->toSql()); */

        return $queryBuilder;
    }

    private function getSectorReportStats()
    {
        $dailyPaymentSums = $this->getSectorReportQuery()->get();

        $paid_amount_sum = $dailyPaymentSums->sum('paid_amount');

        $dateRange = $this->getDateRangeFromFilterRules('payment_date');
        FilterRulesHelper::pop('payment_date');

        $dateFrom = $dateRange->from->format('Y-m-d');

        $dateTo = $dateRange->to->format('Y-m-d');

        $incineratorSpeedReportDays = JQueryBuilder::for(IncinerationPayment::class)
            ->whereRaw(" date(incineration_payments.date) BETWEEN '$dateFrom' AND '$dateTo'")
            ->selectRaw(' SUM(incineration_payments.amount) AS paid_amount, count(incineration_payments.date) as payment_count ')
            ->first();

        return (object) [
            'total_payment_count' => $dailyPaymentSums->sum('payment_count'),
            'total_paid_amount' => $paid_amount_sum,
            'total_paid_amount_8_percent' => $paid_amount_sum * 0.08,
            'total_paid_amount_incineration' => $incineratorSpeedReportDays->paid_amount,
            'total_payemnt_count_incineration' => $incineratorSpeedReportDays->payment_count,
        ];
    }

    private function tabulate($sectorsSpeedReportDays, $departmentNames, $daysInDateRange)
    {
        $data = [];
        for ($i = 0; $i < count($daysInDateRange); $i++) {
            for ($j = 0; $j < count($departmentNames); $j++) {
                $data[$i][$j] = $this->getData($sectorsSpeedReportDays, $departmentNames[$j], $daysInDateRange[$i]);
            }
        }

        return $data;
    }

    private function getData($sectorsSpeedReportDays, $departmentName, $day)
    {
        $depsDataForDay = [
            'payment_count' => 0,
            'paid_amount' => 0,
            'department_name' => $departmentName,
            'payment_date' => $day,
        ];

        if (array_key_exists($day, $sectorsSpeedReportDays->toArray())) {
            $allDepsDataForDay = $sectorsSpeedReportDays[$day];

            foreach ($allDepsDataForDay as $depsData) {
                if ($depsData['department_name'] == $departmentName) {
                    return $depsData;
                }
            }
        }

        return $depsDataForDay;
    }
}
