<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RBACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Role::create(['name' => '']);
        //Permission::create(['name' => '']);
        //階層 Role::findByName(<父>)->syncPermissions(Role::findByName(<子>)->getAllPermissions());

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    }
}
