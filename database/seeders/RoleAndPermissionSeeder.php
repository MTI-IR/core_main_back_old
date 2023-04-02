<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'base', "guard_name" => 'web', 'priority' => 0]);
        Permission::create(['name' => 'bronze', "guard_name" => 'web', 'priority' => 10]);
        Permission::create(['name' => 'silver', "guard_name" => 'web', 'priority' => 20]);
        Permission::create(['name' => 'gold', "guard_name" => 'web', 'priority' => 30]);



        // or may be done by chaining
        $role = Role::create(['name' => 'base'])
            ->givePermissionTo(['base']);
    }
}
