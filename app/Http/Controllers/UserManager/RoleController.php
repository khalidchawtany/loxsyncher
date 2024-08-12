<?php

namespace App\Http\Controllers\UserManager;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
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
        return view('user_manager.roles');
    }

    public function permissions(Request $request)
    {
        $role = Role::where('name', $request->role_name)->firstOrFail();
        $roleOldPermissions = collect($role->permissions()->pluck('name')->all());

        return view('user_manager.role_permissions')->with('permissions', $roleOldPermissions)
            ->with('role', $role);
    }

    public function savePermissions(Request $request, $role_id)
    {
        $role = Role::findOrFail($role_id);

        $permissions = $request->except(['_token', 'user_id', 'user_departments']);

        if (count($permissions) == 0) {
            return ezReturnErrorMessage('Please assign some permissions');
        }

        $permissionsFromDb = Permission::whereIn('name', array_keys($permissions))->get();

        collect($permissions)->keys()->each(function ($permission, $key) use ($permissionsFromDb) {
            if ($permissionsFromDb->contains('name', $permission)) {
                return;
            }

            $permissionsFromDb->push(Permission::create(['name' => $permission]));
        });

        // delete role_has_permissions that are missing from the request
        $permissionIds = $permissionsFromDb->pluck('id')->implode(',');
        DB::statement("DELETE FROM role_has_permissions where role_id = {$role->id} AND permission_id NOT IN ({$permissionIds})");

        // fetch new role permissions
        $rolePermissions = $role->permissions->pluck('name');

        // create missing role_has_permissions
        $rows = [];
        $permissionsFromDb->each(function ($permission) use ($rolePermissions, $role, &$rows) {
            if ($rolePermissions->contains($permission->name)) {
                return;
            }

            $rows[] = "({$permission->id}, {$role->id})";
        });

        if (count($rows) > 0) {
            DB::statement('INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) values ' . implode(',', $rows));
        }

        // $role->syncPermissions(collect($permissions)->keys()->all());
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return ezReturnSuccessMessage('Updated successfully!');
    }

    public function list()
    {
        if (\Auth::user()->hasrole('Super')) {
            return Role::all();
        }

        return Role::where('name', '<>', 'Super')->get();
    }

    public function insert(Request $request)
    {
        Role::create(['name' => $request->name]);

        return ezReturnSuccessMessage('Role inserted successfully!');
    }

    public function update(Request $request)
    {
        $role = Role::findOrFail($request->id);
        $role->name = $request->name;

        $role->save();

        return ezReturnSuccessMessage('Role updated successfully!');
    }

    public function destroy(Request $request)
    {
        $role = Role::findOrFail($request->id);
        $role->delete();

        return ezReturnSuccessMessage('Role deleted successfully!');
    }

    //
    // Permission related actions
    //

    public function showPermissions(Request $request)
    {
        return view('roles.role_permissions')->with('role_id', $request->role_id);
    }

    public function listRolePermissions(Request $request)
    {
        $role = Role::findOrFail($request->role_id);
        $roleOldPermissions = collect($role->permissions()->pluck('name')->all());

        if ($roleOldPermissions->isNotEmpty()) {
            return Permission::all()->map(function ($permission, $key) use ($roleOldPermissions) {
                $permission->status = $roleOldPermissions->contains($permission->name);

                return $permission;
            });
        }

        return Permission::all();
    }

    public function setPermission(Request $request)
    {
        $role = Role::findOrFail($request->role_id);
        $permission = Permission::findOrFail($request->permission_id);

        ($role->hasPermissionTo($permission->name)) ? $role->revokePermissionTo($permission) : $role->givePermissionTo($permission->name);

        return ezReturnSuccessMessage('Permission successfully granted to role!');
    }
}
