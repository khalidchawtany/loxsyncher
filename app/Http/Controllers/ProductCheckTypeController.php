<?php

namespace App\Http\Controllers;

use App\Models\CheckType;
use App\Models\Product;
use App\Models\ProductCheckType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Adapters\JQueryBuilder;

class ProductCheckTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_product_check_type', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_product_check_type', ['only' => ['create']]);
        $this->middleware('permission:update_product_check_type', ['only' => ['update']]);
        $this->middleware('permission:destroy_product_check_type', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        return view('products.check_types')->with('product', $product);
    }

    public function list(Request $request)
    {
        return ProductCheckType::where(['product_id' => $request->product_id])
            ->join('check_types', 'product_check_type.check_type_id', 'check_types.id')
            ->selectRaw('
                check_types.*,
                product_check_type.check_methods,
                product_check_type.check_limits,
                product_check_type.check_normal_range,
                product_check_type.active,
                product_check_type.order,
                product_check_type.note
            ')->get();
    }

    public function listCheckTypes(Request $request)
    {
        $query = JQueryBuilder::for(CheckType::class)
            ->where('disabled', false)
            ->selectRaw('id,category,subcategory,price')
            ->selectRaw('CASE WHEN subcategory IS NOT NULL THEN CONCAT(category, " - ", subcategory) ELSE category END AS check_type');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('category', 'like', "{$request->q}%")
                    ->orWhere('subcategory', 'like', "{$request->q}%");
            });
        }

        if ($request->has('product_id')) {
            // exclude the check types that this product already have
            $query->whereNotIn('id', function ($q) use ($request) {
                $q->select('check_type_id')
                    ->from('product_check_type')
                    ->where('product_id', $request->product_id);
            });
        }

        return $query->jsonJPaginate();
    }

    protected function create(Request $request)
    {
        $productCheckTypeCount = ProductCheckType::where([
            'product_id' => $request->product_id,
            'check_type_id' => $request->id,
        ])->count();

        if ($productCheckTypeCount != 0) {
            return ezReturnErrorMessage('Product already has the selected test!');
        }

        $product = Product::findOrFail($request->product_id);
        $checkType = CheckType::findOrFail($request->check_type_id);

        $product->checkTypes()->attach($checkType->id);

        return ezReturnSuccessMessage('Test added successfully!');
    }

    public function update(Request $request)
    {
        $productCheckType = ProductCheckType::query()
            ->where([
                    'product_id' => $request->product_id,
                    'check_type_id' => $request->id,
            ])
            ->firstOrFail();

        $productCheckType->update([
                'check_methods' => $request->check_methods,
                'check_limits' => $request->check_limits,
                'check_normal_range' => $request->check_normal_range,
                'active' => $request->active,
                'order' => $request->order,
                'note' => $request->note,
        ]);

        return ezReturnSuccessMessage('Product test updated successfully!');
    }

    public function destroy(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $product->checkTypes()->detach($request->id);

        return ezReturnSuccessMessage('Product removed successfully!');
    }
}
