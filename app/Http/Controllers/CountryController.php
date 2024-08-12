<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\Countries\StoreCountry;
use App\Http\Requests\Countries\UpdateCountry;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_country', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_country', ['only' => ['create']]);
        $this->middleware('permission:update_country', ['only' => ['update']]);
        $this->middleware('permission:destroy_country', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('countries.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(Country::class)
            ->allowedFilters('name', 'user_id')
            ->jsonJPaginate();
    }

    public function jsonList(Request $request)
    {
        $query = Country::selectRaw('countries.*, countries.is_default as selected');
        if ($request->filled('q')) {
            return $query->where('name', 'like', "{$request->q}%")->get();
        }

        return $query->get();
    }

    protected function create(StoreCountry $request)
    {
        try {
            $country = Country::create(array_merge($request->input(), ['user_id' => $request->user_id]));

            return ezReturnSuccessMessage('Country created successfully!', $country);
        } catch (\Illuminate\Database\QueryException $e) {
            return ezReturnErrorMessage('ئەم وڵاتە پێشتر زیاد کراوە');
        }
    }

    public function update(UpdateCountry $request)
    {
        $country = Country::findOrFail($request->id);

        $country->update($request->input());

        return ezReturnSuccessMessage('Country updated successfully!');
    }

    public function destroy(Request $request)
    {
        $country = Country::findOrFail($request->id);

        $country->delete();

        return ezReturnSuccessMessage('Country removed successfully!');
    }

    public function setDefault(Request $request)
    {
        $country = Country::findOrFail($request->id);

        DB::statement('update countries set is_default = 0');

        $country->increment('is_default');

        return ezReturnSuccessMessage('Default country changed successfully!');
    }
}
