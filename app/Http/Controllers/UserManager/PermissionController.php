<?php

namespace App\Http\Controllers\UserManager;

use App\Models\User;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Models\AllPermissionsView;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use App\Traits\DownloadsExcelFileTrait;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use DownloadsExcelFileTrait;

    public function __construct()
    {
        $this->middleware(['auth', 'user.id']); // allow creating through admin only
    }

    public function index(Request $request)
    {
        return view('user_manager.permissions');
    }

    public function listUsers(Request $request)
    {
        if (Auth::user()->hasrole('Super')) {
            return User::select(['id', 'kurdish_name'])->get();
        }

        return User::where('name', '<>', 'Super')
            ->select(['id', 'kurdish_name'])
            ->get();
    }

    public function listUserPermissions(Request $request)
    {
        $user = User::findOrFail($request->userId);
        $userOldPermissions = collect($user->permissions()->pluck('name')->all());

        return view('user_manager.user_permissions')->with('permissions', $userOldPermissions)
            ->with('seleced_user', $user);
    }

    public function savePermissions(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $permissions = $request->except(['_token']);

        if (count($permissions) == 0) {
            return ezReturnErrorMessage('Please assign some permissions');
        }

        collect($permissions)->keys()->each(function ($permission, $key) {
            Permission::firstOrCreate(['name' => $permission]);
        });

        $user->syncPermissions(collect($permissions)->keys()->all());

        return ezReturnSuccessMessage('Updated successfully!');
    }

    public function reviewPermissions()
    {
        return view('user_manager.review_permissions');
    }

    public function downloadPermissions(Request $request)
    {
        $permissions = JQueryBuilder::for(AllPermissionsView::class)
            ->join('users', 'users.id', 'all_permissions_view.user_id')
            ->join('permissions', 'permissions.id', 'all_permissions_view.permission_id')
            ->selectRaw('
                  users.id as user_id,
                  users.kurdish_name as user_name,
                  users.job_description as job_description,
                  users.is_staff as is_staff,
                  permissions.name as permission_name,
                  all_permissions_view.role_name as role_name
            ')
            ->allowedSorts([
                    'users.id',
                    'users.kurdish_name',
                    'users.job_description',
                    'users.is_staff',
                    'permissions.name',
                    'all_permissions_view.role_name'
            ])
            ->allowedFilters([
                AllowedFilter::exact('users.id'),
                'users.kurdish_name',
                'permissions.name',
                'all_permissions_view.role_name',
                'users.job_description',
                'users.is_staff',
            ])
            ->orderBy('users.id')
            ->get();

        $columns = ['user_id', 'user_name', 'job_description', 'is_staff', 'permission_name', 'role_name'];

        $this->createExcel($permissions->toArray(), $columns, 'permissions.xlsx');
    }

    public function listAllPermissions()
    {
        return JQueryBuilder::for(AllPermissionsView::class)
            ->join('users', 'users.id', 'all_permissions_view.user_id')
            ->join('permissions', 'permissions.id', 'all_permissions_view.permission_id')
            ->selectRaw('
                  users.id as user_id,
                  users.kurdish_name as user_name,
                  users.job_description as job_description,
                  users.is_staff as is_staff,
                  permissions.name as permission_name,
                  all_permissions_view.role_name as role_name
            ')
            ->allowedSorts([
                    'users.id',
                    'users.kurdish_name',
                    'users.job_description',
                    'users.is_staff',
                    'permissions.name',
                    'all_permissions_view.role_name'
            ])
            ->allowedFilters([
                AllowedFilter::exact('users.id'),
                'users.kurdish_name',
                'permissions.name',
                'all_permissions_view.role_name',
                'users.job_description',
                'users.is_staff',
            ])
            ->jsonJPaginate();
    }
}
