<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\Departments\StoreDepartment;
use App\Http\Requests\Departments\UpdateDepartment;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class DepartmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_department', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_department', ['only' => ['create']]);
        $this->middleware('permission:update_department', ['only' => ['update']]);
        $this->middleware('permission:destroy_department', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('departments.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(Department::class)
            ->allowedFilters([
                'name',
                'kurdish_name',
                'manager_name',
                'to',
                'to_arabic',
                'sample_count',
                'is_third_party',
                'needs_inspections_approved',
                'delays_results',
                'permit_copies',
                'transaction_copies',
                'failed_transaction_copies',
                'invoice_copies',
            ])
            ->jsonJPaginate();
    }

    /**
     * Create a new department instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Department
     */
    protected function create(StoreDepartment $request)
    {
        $department = Department::create($request->input());

        return ezReturnSuccessMessage('Department created successfully!', $department);
    }

    public function update(UpdateDepartment $request)
    {
        $department = Department::findOrFail($request->id);

        $department->update($request->input());

        return ezReturnSuccessMessage('Department updated successfully!');
    }

    public function destroy(Request $request)
    {
        $department = Department::findOrFail($request->id);

        $department->delete();

        return ezReturnSuccessMessage('Department removed successfully!');
    }
}
