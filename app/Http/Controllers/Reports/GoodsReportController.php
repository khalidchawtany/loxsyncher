<?php

namespace App\Http\Controllers\Reports;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use App\Filters\ExcludeFilter;
use App\Http\Controllers\Reports\BaseReportController;
use App\Utils\FilterRulesHelper;
use Spatie\QueryBuilder\AllowedFilter;

class GoodsReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showGoodsReport()
    {
        return view('reports.goods');
    }

    public function listGoodsReport()
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getGoodsReportQuery()
            ->jsonJPaginate();

        $stats = $this->getGoodsReportStats();

        $stats = [
            [
                'name' => 'Total Product Count',
                'value' => $stats->total_product_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Total Product Amount',
                'value' => number_format($stats->total_product_amount),
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    public function printGoodsReport(Request $request)
    {
        if (FilterRulesHelper::get('transactions.date_time') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $goodsSums = $this->getGoodsReportQuery()->get();

        $stats = $this->getGoodsReportStats();

        $info = [
            'total_product_count' => $stats->total_product_count,
            'total_product_amount' => $stats->total_product_amount,
        ];

        return view('reports.printGoodsReport', compact(['info', 'goodsSums']));
    }

    private function getGoodsReportQuery()
    {
        /* select departments.name as department, */
        /* 	count(products.id) as product_count, */
        /*     products.name as product_name */
        /* from `payments` */
        /* inner join transactions on payments.transaction_id= transactions.id */
        /* inner join products on products.id= transactions.product_id */
        /* inner join departments on  departments.id = products.department_id */
        /* where date(payments.date_time) in ('2019-06-23', '2019-06-24', '2019-06-25') */
        /* 		AND departments.id in (1,2,3,4,5,6) */
        /* group by departments.id, */
        /*         product_name */


        $departmentFilter = FilterRulesHelper::get('department_id');
        if ($departmentFilter != null) {
            $departmentFilter = "departments.id = $departmentFilter";
        } else {
            $userDepartmentIds = request()->user_departments->join(',');
            $departmentFilter = "departments.id IN ($userDepartmentIds)";
        }

        $statusFilter = FilterRulesHelper::get('transaction_checks_view.status');
        if ($statusFilter != null) {
            $statusFilter = " transaction_checks_view.status = '$statusFilter'  COLLATE utf8mb4_unicode_ci ";
        }

        $queryBuilder = JQueryBuilder::for(Transaction::class)
            ->when($statusFilter != null, function ($query) {
                return $query->join('transaction_checks_view', 'transaction_checks_view.transaction_id', 'transactions.id');
            })
            ->join('products', 'products.id', 'transactions.product_id')
            ->join('departments', 'departments.id', 'products.department_id')
            ->allowedFilters(
                AllowedFilter::custom('department_id', new ExcludeFilter),
                'department_name',
                'product_count',
                'product_name',
                'amount_sum',
                'amount_unit',
                AllowedFilter::custom('transactions.date_time', new BetweenFilter),
                'transaction_checks_view.status'
            )
            ->selectRaw('
                    departments.name AS department_name,
                    count(products.id) as product_count,
                    any_value(products.kurdish_name) as product_name,
                    sum(transactions.amount) as amount_sum,
                    any_value(transactions.unit) as amount_unit
                ')
            ->where('transactions.deleted_at', null)
            ->whereRaw($departmentFilter)
            ->when($statusFilter != null, function ($query) use ($statusFilter) {
                return $query->whereRaw($statusFilter);
            })
            ->groupBy(DB::raw('departments.id, products.name'))
            ->orderBy('departments.name', 'ASC');

        return $queryBuilder;
    }

    private function getGoodsReportStats()
    {
        $goodsStats = $this->getGoodsReportQuery()->get();

        return  (object) [
            'total_product_count' => $goodsStats->sum('product_count'),
            'total_product_amount' => $goodsStats->sum('amount_sum'),
        ];
    }
}
