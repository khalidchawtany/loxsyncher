<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Products\StoreProduct;
use App\Http\Requests\Products\UpdateProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_product', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_product', ['only' => ['create']]);
        $this->middleware('permission:update_product', ['only' => ['update']]);
        $this->middleware('permission:destroy_product', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('products.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(Product::class)
            ->with('checkTypes')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->allowedFilters(
                'products.id',
                'products.name',
                'products.kurdish_name',
                'products.alternative_names',
                'products.arabic_name',
                'products.customs_name',
                'products.disabled',
                'products.blended',
                'products.hide_regapedan',
                'products.skip_payment',
                'products.delay_results',
                'products.invoice_copies',
                'categories.name',
                'date_limit',
                'coc',
                'amount_limit',
                'requires_truck_limit',
                'is_paid_individually',
                'fee_if_less',
                'fee_limit',
                'fee_if_more'
            )
            ->selectRaw('products.*')
            ->selectRaw('categories.name as `categories.name`')
            ->orderBy('products.name')
            ->jsonJPaginate();
    }


    public function listCategories()
    {
        return Category::selectRaw('name')->get();
    }

    public function jsonList(Request $request)
    {
        return Product::query()
            ->where('disabled', '<>', true)
            ->when($request->filled('q'), function ($q) use ($request) {
                return $q->where('alternative_names', 'like', "{$request->q}%")
                    ->orWhere('kurdish_name', 'like', "{$request->q}%");
            })
            ->take(10)->get();
    }

    /**
     * Create a new product instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Product
     */
    protected function create(StoreProduct $request)
    {

        $category = Category::where('name', $request->categories_name)
            ->firstOrFail();

        $product = Product::create(
            array_merge(
                $request->except(['departments_name', 'categories_name']),
                [
                    'category_id' => $category->id,
                ]
            )
        );

        return ezReturnSuccessMessage('Product created successfully!', $product);
    }

    public function update(UpdateProduct $request)
    {
        $product = Product::findOrFail($request->id);

        $category = Category::where('name', $request->categories_name)
            ->first();

        $product->update(
            array_merge(
                $request->except(['departments_name', 'categories_name', 'extra']),
                [
                    'category_id' => optional($category)->id,
                ]
            )
        );

        return ezReturnSuccessMessage('Product updated successfully!');
    }

    public function destroy(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $product->delete();

        return ezReturnSuccessMessage('Product removed successfully!');
    }
}
