<?php

namespace App\Http\Controllers\UserManager;

use App\Http\Controllers\Controller;
use App\Traits\DownloadsExcelFileTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Adapters\JQueryBuilder;

class ReviewRoleController extends Controller
{
    use DownloadsExcelFileTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
    }

    public function index()
    {
        return view('user_manager.review_roles');
    }

    public function list()
    {
        return JQueryBuilder::for(Role::class)
          ->whereRaw('name <> "Super"')
          ->select(['id', 'name'])
          ->with(['permissions:id,name'])
          ->allowedFilters([
              'roles.name',
          ])
          ->jsonJPaginate();
    }

    public function downloadRoles(Request $request)
    {
        $roles = JQueryBuilder::for(Role::class)
          ->whereRaw('name <> "Super"')
          ->select(['id', 'name'])
          ->with(['permissions:id,name'])
          ->allowedFilters([
              'roles.name',
          ])
          ->get();

        $columns = ['Role', 'permission_name'];
        $roles = $roles->map(function ($role) {
            $role->perm = $role->permissions->pluck('name')->join(',
');

            return $role;
        });

        $roles = $roles->toArray();
        foreach ($roles as &$role) {
            unset($role['id']);
            unset($role['permissions']);
        }

        $this->createExcel($roles, $columns, 'roles.xlsx');
    }
}
