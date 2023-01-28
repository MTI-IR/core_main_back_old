<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermitionSeeder extends Seeder
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
        Permission::create(['name' => 'base', 'priority' => 0]);
        Permission::create(['name' => 'bronze', 'priority' => 10]);
        Permission::create(['name' => 'silver', 'priority' => 20]);
        Permission::create(['name' => 'gold', 'priority' => 30]);


        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'super_admin']);
        $role->givePermissionTo(Permission::all());

        // or may be done by chaining
        $role = Role::create(['name' => 'base'])
            ->givePermissionTo(['base']);
    }
}
