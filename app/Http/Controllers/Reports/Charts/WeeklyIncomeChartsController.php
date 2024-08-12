<?php

namespace App\Http\Controllers\Reports\Charts;

use App\Helpers\FilterHelper;
use DB;

class WeeklyIncomeChartsController extends BaseChartsController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view_weekly_income_charts', ['only' => ['index']]);
    }

    public function getWeeklyIncomeChartData()
    {
        $filters = new FilterHelper();

        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $dateLimitCondition = ($dateFrom != null && $dateTo != null)
            ? " date(payments.date_time) between '$dateFrom' and '$dateTo' "
            : '';

        $filters->applyFilter($dateLimitCondition);

        $department_id = request('department_id');
        $department_condition = $department_id ? ' products.department_id = ' . $department_id : '';

        $filters->applyFilter($department_condition);

        $weekly_income = collect(
            DB::select("
                        SELECT FROM_DAYS(TO_DAYS(payments.date_time) -MOD(TO_DAYS(payments.date_time) -0, 7)) as label, COUNT(*) as total, SUM(payments.amount) as sum
                        FROM payments
                        JOIN transactions on transactions.id = payments.transaction_id
                        JOIN products on transactions.product_id = products.id
                            {$filters->getFilterRules(true)}
                        GROUP BY FROM_DAYS(TO_DAYS(payments.date_time) -MOD(TO_DAYS(payments.date_time) -0, 7))
                        ORDER BY FROM_DAYS(TO_DAYS(payments.date_time) -MOD(TO_DAYS(payments.date_time) -0, 7));
                        ")
        );

        $weekly_income_chart_labels = $weekly_income->pluck('label')->toJson();

        $weekly_income_chart_data = $weekly_income->pluck('sum')->toJson();

        return  compact(
            'weekly_income_chart_labels',
            'weekly_income_chart_data'
        );
    }

    public function index()
    {
        return view('reports.charts.income.weekly_income_charts', $this->getWeeklyIncomeChartData());
    }
}
