<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'super-admin',
            'user',
            'user.index',
            'user.show',
            'user.destroy',
            'user.destroy.user',
            'user.destroy.users',
            'user.validate',
            'user.assign-role',
            'user.permission',
            'user.permission.add',
            'user.permission.remove',
            'user.permission.change',
            'user.create',

            'company',
            'company.index',
            'company.projects',
            'company.edit',
            'company.cerate',
            'company.destroy',
            'company.validate',
            'company.document',
            'company.show',

            'project',
            'project.index',
            'project.edit',
            'project.destroy',
            'project.create',
            'project.validate',
            'project.document',
            'project.show',

            'state',
            'state.index',
            'state.create',
            'state.destroy',
            'state.show',

            'city',
            'city.index',
            'city.create',
            'city.destroy',
            'city.show',

            'category',
            'category.index',
            'category.create',
            'category.destroy',
            'category.show',

            'sun-category',
            'sun-category.index',
            'sun-category.create',
            'sun-category.destroy',
            'sun-category.show',

            'ticket',
            'ticket.index',
            'ticket.create',
            'ticket.destroy',
            'ticket.show',

            'permission',
            'permission.index',
            'permission.create',
            'permission.destroy',
            'permission.destroy.all',
            'permission.sync-roles',
            'permission.remove-roles',

            'role',
            'role.index',
            'role.show',
            'role.create',
            'role.destroy',
            'role.destroy.all',
            'role.sync-permissions',
            'role.change-role',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }
        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'admin']);
        $role->givePermissionTo(Permission::where('guard_name', 'admin')->get());
        $user = User::make();
        $user->id = '00000000-0000-0000-0000-000000000000';
        $user->phone_number = '00000000000';
        $user->password = Hash::make('pass');
        $user->validated = 1;
        $user->first_name = 'super-admin';
        $user->last_name = 'super-admin';
        $user->is_admin = true;
        $user->save();
        $user->assignRole($role);
    }
}
