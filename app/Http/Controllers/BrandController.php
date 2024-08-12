<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\Brands\StoreBrand;
use App\Http\Requests\Brands\UpdateBrand;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class BrandController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_brand', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_brand', ['only' => ['create']]);
        $this->middleware('permission:update_brand', ['only' => ['update']]);
        $this->middleware('permission:destroy_brand', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('brands.index');
    }

    public function showBrandDialog(Request $request)
    {
        if ($request->has('id')) {
            $brand = Brand::findOrFail($request->id);

            return view('brands.brand_dialog', compact('brand'));
        }

        return view('brands.brand_dialog');
    }

    public function list()
    {
        return JQueryBuilder::for(Brand::class)
            ->join('products', 'products.id', '=', 'brands.product_id')
            ->selectRaw('
                brands.*,
                products.kurdish_name as brand_product
                ')
            ->allowedFilters([
                'id',
                'name',
                'company',
                'products.kurdish_name',
            ])
            ->jsonJPaginate();
    }

    public function jsonList(Request $request)
    {
        if (!$request->anyFilled(['product_id', 'product_name'])) {
            return null;
        }

        return Brand::query()
            ->join('products', 'products.id', '=', 'brands.product_id')
            ->selectRaw('
                brands.id as brand_id,
                brands.name as brand_name,
                brands.company as brand_company,
                products.kurdish_name as brand_product
                ')
            ->when($request->filled('product_id'), function ($query) use ($request) {
                return $query->where('products.id', $request->product_id);
            })
            ->when(!$request->filled('product_id')
                && $request->filled('product_name'), function ($query) use ($request) {
                return $query->where('products.kurdish_name', $request->product_name);
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                return $query->whereRaw("
                        brands.name like '{$request->q}%'
                        OR brands.company like '{$request->q}%'
                        OR products.kurdish_name like '{$request->q}%'
                    ");
            })
            ->take(10)->get();
    }

    /**
     * Create a new brand instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Brand
     */
    protected function create(StoreBrand $request)
    {
        $brand = Brand::create($request->input());

        return ezReturnSuccessMessage('Brand created successfully!', $brand);
    }

    public function update(UpdateBrand $request)
    {
        $brand = Brand::findOrFail($request->id);

        $brand->update($request->input());

        return ezReturnSuccessMessage('Brand updated successfully!', $brand);
    }

    public function destroy(Request $request)
    {
        $brand = Brand::findOrFail($request->id);

        $brand->delete();

        return ezReturnSuccessMessage('Brand removed successfully!');
    }
}
