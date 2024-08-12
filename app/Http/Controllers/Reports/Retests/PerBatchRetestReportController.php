<?php

namespace App\Http\Controllers\Reports\Retests;

use App\Models\Batch;
use App\Http\Controllers\Reports\BaseReportController;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\BetweenFilter;

class PerBatchRetestReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('reports.retests.per_batch.index');
    }

    public function showRetestBatchInfoDialog(Request $request)
    {
        $batch_id = $request->batch_id;
        $batch = Batch::with([
            'batchProduct',
            'retests.checks.checkType',
            'checks.updatedBy',
            'checks.checkType',
        ])->findOrFail($batch_id);

        return view('reports.retests.per_batch.batch_retest_info_dialog', compact('batch'));
    }

    public function list(Request $request)
    {
        if (getFilterRule('batches.created_at') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getQuery($request)
            ->jsonJPaginate();

        $stats = [
            [
                'name' => 'Count',
                'value' => $jsonJPaginate['total'],
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    private function getQuery($request)
    {
        return JQueryBuilder::for(Batch::class)
            ->whereIn('products.department_id', request()->user_departments)
            ->leftJoin('transactions', 'transactions.id', 'batches.transaction_id')
            ->leftJoin('products', 'products.id', 'batches.batch_product_id')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('departments', 'departments.id', 'products.department_id')
            ->allowedFilters([
                'batches.id',
                'batches.retest_batch_id',
                'batches.product_type',
                AllowedFilter::custom('batches.created_at', new BetweenFilter),
                'products.category_id',
                'products.kurdish_name',
                'categories.name',
                'categories.id',
                'departments.name',
            ])
            ->selectRaw('
                    transactions.date_time as transaction_date,
                    batches.transaction_id as transaction_id,
                    batches.id as batch_id,
                    batches.retest_batch_id,
                    batches.product_type,
                    batches.created_at AS batch_date,
                    products.kurdish_name as product_name,
                    departments.name as department_name,
                    categories.name as category_name
                ')
            ->whereRaw(' batches.id in (select distinct(batches.retest_batch_id) from batches ) ')
            // ->whereRaw('batches.retest_batch_id IS NOT NULL')

            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('batches.id', 'desc');
            });
    }
}
