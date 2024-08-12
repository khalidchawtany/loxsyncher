<?php

namespace App\Http\Controllers;

use App\Models\CustomsProduct;
use App\Http\Requests\CustomsProducts\StoreCustomsProduct;
use App\Http\Requests\CustomsProducts\UpdateCustomsProduct;
use App\Imports\CustomsProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Adapters\JQueryBuilder;

class CustomsProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_customs_product', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_customs_product', ['only' => ['create']]);
        $this->middleware('permission:update_customs_product', ['only' => ['update']]);
        $this->middleware('permission:destroy_customs_product', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('customs_products.index');
    }

    public function list()
    {
        return JQueryBuilder::for(CustomsProduct::class)
            ->allowedFilters('id', 'name', 'custom_id')
            ->jsonJPaginate();
    }

    public function jsonList(Request $request)
    {
        if ($request->filled('q')) {
            return CustomsProduct::selectRaw('customs_products.id as customs_product_id, customs_products.name as customs_product_name')
                ->whereRaw("customs_products.name like '{$request->q}%'")
                ->take(10)->get();
        }

        return CustomsProduct::selectRaw('customs_products.id as customs_product_id, customs_products.name as customs_product_name')
            ->take(10)->get();
    }

    /**
     * Create a new customs_product instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\CustomsProduct
     */
    protected function create(StoreCustomsProduct $request)
    {
        $customs_product = CustomsProduct::create($request->input());

        return ezReturnSuccessMessage('CustomsProduct created successfully!', $customs_product);
    }

    public function update(UpdateCustomsProduct $request)
    {
        $customs_product = CustomsProduct::findOrFail($request->id);

        $customs_product->update($request->input());

        return ezReturnSuccessMessage('CustomsProduct updated successfully!');
    }

    public function destroy(Request $request)
    {
        $customs_product = CustomsProduct::findOrFail($request->id);

        $customs_product->delete();

        return ezReturnSuccessMessage('CustomsProduct removed successfully!');
    }

    public function showImportFromExcelFileDialog()
    {
        return view('customs_products.import_from_excel_file_dialog');
    }

    public function importFromExcelFile(Request $request)
    {
        if ($request->hasFile('excel_file')) {
            Excel::import(new CustomsProductImport(), request()->file('excel_file'));

            return ezReturnSuccessMessage('Cusoms products imported.');
        }

        return ezReturnErrorMessage('An excel file is required.');
    }
}
