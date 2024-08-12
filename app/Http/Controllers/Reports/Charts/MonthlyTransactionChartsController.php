<?php

namespace App\Http\Controllers\Reports\Charts;

use App\Helpers\FilterHelper;
use DB;

class MonthlyTransactionChartsController extends BaseChartsController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view_monthly_transaction_charts', ['only' => ['index']]);
    }

    public function getMonthlyTransactionChartData()
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

        $monthly_transaction = collect(
            DB::select("
                        select CONCAT( DATE_FORMAT(transactions.date_time, '%m'), '/', YEAR(transactions.date_time)) as label, COUNT(*) as total
                        from transactions
                          JOIN products on transactions.product_id = products.id
                            {$filters->getFilterRules(true)}
                            group by CONCAT(DATE_FORMAT(transactions.date_time, '%m'), '/', YEAR(transactions.date_time))
                            order by CONCAT(DATE_FORMAT(transactions.date_time, '%m'), '/', YEAR(transactions.date_time))
                        ")
        );

        $monthly_transaction_chart_labels = $monthly_transaction->pluck('label')->toJson();

        $monthly_transaction_chart_data = $monthly_transaction->pluck('total')->toJson();

        return  compact(
            'monthly_transaction_chart_labels',
            'monthly_transaction_chart_data'
        );
    }

    public function index()
    {
        return view('reports.charts.transaction.monthly_transaction_charts', $this->getMonthlyTransactionChartData());
    }
}
