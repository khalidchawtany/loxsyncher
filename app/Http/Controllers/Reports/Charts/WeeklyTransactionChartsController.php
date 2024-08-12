<?php

namespace App\Http\Controllers\Reports\Charts;

use App\Helpers\FilterHelper;
use DB;

class WeeklyTransactionChartsController extends BaseChartsController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view_weekly_transaction_charts', ['only' => ['index']]);
    }

    public function getWeeklyTransactionChartData()
    {
        $filters = new FilterHelper();

        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $dateLimitCondition = ($dateFrom != null && $dateTo != null)
            ? " date(transactions.date_time) between '$dateFrom' and '$dateTo' "
            : '';

        $filters->applyFilter($dateLimitCondition);

        $department_id = request('department_id');
        $department_condition = $department_id ? ' products.department_id = ' . $department_id : '';

        $filters->applyFilter($department_condition);

        $weekly_transaction = collect(
            DB::select("
                        SELECT FROM_DAYS(TO_DAYS(transactions.date_time) -MOD(TO_DAYS(transactions.date_time) -0, 7)) as label, COUNT(*) as total
                        FROM transactions
                        JOIN products on transactions.product_id = products.id
                            {$filters->getFilterRules(true)}
                        GROUP BY FROM_DAYS(TO_DAYS(transactions.date_time) -MOD(TO_DAYS(transactions.date_time) -0, 7))
                        ORDER BY FROM_DAYS(TO_DAYS(transactions.date_time) -MOD(TO_DAYS(transactions.date_time) -0, 7));
                        ")
        );

        $weekly_transaction_chart_labels = $weekly_transaction->pluck('label')->toJson();

        $weekly_transaction_chart_data = $weekly_transaction->pluck('total')->toJson();

        return  compact(
            'weekly_transaction_chart_labels',
            'weekly_transaction_chart_data'
        );
    }

    public function index()
    {
        return view('reports.charts.transaction.weekly_transaction_charts', $this->getWeeklyTransactionChartData());
    }
}
