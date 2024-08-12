<?php

namespace App\Http\Controllers;

use App\Models\CheckType;
use App\Http\Requests\CheckTypes\StoreCheckType;
use App\Http\Requests\CheckTypes\UpdateCheckType;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class CheckTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_check_type')->only(['index', 'list']);
        $this->middleware('permission:create_check_type')->only(['create']);
        $this->middleware('permission:update_check_type')->only(['update']);
        $this->middleware('permission:destroy_check_type')->only(['destroy']);
    }

    public function index()
    {
        return view('check_types.index');
    }

    public function list()
    {
        return JQueryBuilder::for(CheckType::class)
            ->allowedFilters('category', 'subcategory', 'disabled', 'price', 'reason', 'acronym')
            ->jsonJPaginate();
    }

    public function listCategories()
    {
        return CheckType::selectRaw('distinct(category) as category')
            ->get();
    }

    /**
     * Create a new check_type instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\CheckType
     */
    protected function create(StoreCheckType $request)
    {
        $check_type = CheckType::create($request->input());

        return ezReturnSuccessMessage('CheckType created successfully!', $check_type);
    }

    public function update(UpdateCheckType $request)
    {
        $check_type = CheckType::findOrFail($request->id);

        if ($check_type->reason == $request->reason && $check_type->created_at < now()->subMinutes(5)) {
            return ezReturnErrorMessage('Explain update reason');
        }

        $check_type->update($request->input());

        return ezReturnSuccessMessage('CheckType updated successfully!');
    }

    public function destroy(Request $request)
    {
        $check_type = CheckType::findOrFail($request->id);

        $check_type->delete();

        return ezReturnSuccessMessage('CheckType removed successfully!');
    }
}
