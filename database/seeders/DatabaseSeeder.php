<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::where(['name' => 'Admin'])->first();
        $superRole = Role::where(['name' => 'Super'])->first();

        $controlCenterPermission = Permission::create(['name' => 'view_control_center']);
        $rolesPermission = Permission::create(['name' => 'view_roles_and_permissions']);
        $assignPermissionToRole = Permission::create(['name' => 'assign_permission_to_role']);
        $assignPermissionToUser = Permission::create(['name' => 'assign_permission_to_user']);

        $adminRole->givePermissionTo(
            $controlCenterPermission->name,
            $rolesPermission->name,
            $assignPermissionToRole->name,
            $assignPermissionToUser->name
        );

        $superRole->givePermissionTo(
            $controlCenterPermission->name,
            $rolesPermission->name,
            $assignPermissionToRole->name,
            $assignPermissionToUser->name
        );

        $superUser = User::find(1);
        $superUser->password = Hash::make('super');
        $superUser->save();
        $superUser->assignRole($adminRole);
        $superUser->assignRole($superRole);

        $adminUser = User::factory()->create([
            'name' => 'Admin',
            'kurdish_name' => 'ئەدمین',
            'email' => 'admin@knights.test',
            'password' => Hash::make('admin'),
        ]);

        $adminUser->assignRole($adminRole);
    }
}
