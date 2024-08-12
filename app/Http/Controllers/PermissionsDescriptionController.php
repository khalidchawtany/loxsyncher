<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionsDescriptions\StorePermissionsDescription;
use App\Http\Requests\PermissionsDescriptions\UpdatePermissionsDescription;
use App\Models\PermissionsDescription;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class PermissionsDescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_permissions_description', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_permissions_description', ['only' => ['create']]);
        $this->middleware('permission:update_permissions_description', ['only' => ['update']]);
        $this->middleware('permission:destroy_permissions_description', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('permissions_descriptions.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(PermissionsDescription::class)
            ->allowedFilters([
                    AllowedFilter::exact('id'),
                    'permission_name',
                    'description',
                    'note',
                    'user_id'
            ])
            ->jsonJPaginate();
    }

    protected function create(StorePermissionsDescription $request)
    {
        $permissions_description = PermissionsDescription::create($request->input());

        return ezReturnSuccessMessage('Permissions Description created successfully!', $permissions_description->id);
    }

    public function update(UpdatePermissionsDescription $request)
    {
        $permissions_description = PermissionsDescription::findOrFail($request->id);

        $permissions_description->update($request->input());

        return ezReturnSuccessMessage('Permissions Description updated successfully!');
    }

    public function destroy(Request $request)
    {
        $permissions_description = PermissionsDescription::findOrFail($request->id);

        $permissions_description->delete();

        return ezReturnSuccessMessage('Permissions Description removed successfully!');
    }
}

