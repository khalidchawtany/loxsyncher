<?php

namespace App\Http\Controllers\Reports\Charts;

use App\Helpers\FilterHelper;
use App\Http\Controllers\Reports\Charts\BaseChartsController;
use Illuminate\Support\Facades\DB;

class MonthlyIncomeChartsController extends BaseChartsController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view_monthly_income_charts', ['only' => ['index']]);
    }

    public function getMonthlyIncomeChartData()
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

        $monthly_income = collect(
            DB::select("
                        select CONCAT( DATE_FORMAT(payments.date_time, '%m'), '/', YEAR(payments.date_time)) as label, COUNT(*) as total, sum(payments.amount) as sum
                        from payments
                          JOIN transactions on transactions.id = payments.transaction_id
                          JOIN products on transactions.product_id = products.id
                            {$filters->getFilterRules(true)}
                            group by CONCAT(DATE_FORMAT(payments.date_time, '%m'), '/', YEAR(payments.date_time))
                            order by CONCAT(DATE_FORMAT(payments.date_time, '%m'), '/', YEAR(payments.date_time))
                        ")
        );

        $monthly_income_chart_labels = $monthly_income->pluck('label')->toJson();

        $monthly_income_chart_data = $monthly_income->pluck('sum')->toJson();

        return  compact(
            'monthly_income_chart_labels',
            'monthly_income_chart_data'
        );
    }

    public function index()
    {
        return view('reports.charts.income.monthly_income_charts', $this->getMonthlyIncomeChartData());
    }
}
