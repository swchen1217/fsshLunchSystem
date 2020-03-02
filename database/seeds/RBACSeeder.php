<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        //Role::findByName("")->syncPermissions($permissions);
        //Role::findByName("")->givePermissionTo($permission);
        //Role::findByName("")->revokePermissionTo($permission);
        //Role::findByName(<父>)->syncPermissions(Role::findByName(<子>)->getAllPermissions());

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->init();
        $roles = ['student', 'student_LM', 'clerk', 'store_mgr', 'system_mgr', 'Super Admin'];
        foreach ($roles as $role)
            Role::findByName($role)->syncPermissions([]);

        //student
        Role::findByName("student")->givePermissionTo("dish.read");
        Role::findByName("student")->givePermissionTo("balance.read.self");
        Role::findByName("student")->givePermissionTo("order.read.self");
        Role::findByName("student")->givePermissionTo("order.read.class.today");
        Role::findByName("student")->givePermissionTo("order.modify.create.self");
        Role::findByName("student")->givePermissionTo("order.modify.update.self");
        Role::findByName("student")->givePermissionTo("order.modify.delete.self");

        //student_LM
        Role::findByName("student_LM")->syncPermissions(Role::findByName("student")->getAllPermissions());
        Role::findByName("student")->givePermissionTo("order.read.class");
        Role::findByName("student")->givePermissionTo("order.modify.delete.class");

        //clerk
        Role::findByName("clerk")->givePermissionTo("balance.read");
        Role::findByName("clerk")->givePermissionTo("balance.modify");

        //store_mgr
        Role::findByName("store_mgr")->givePermissionTo("dish");
        Role::findByName("store_mgr")->givePermissionTo("sale");
        Role::findByName("store_mgr")->givePermissionTo("balance");
        Role::findByName("store_mgr")->givePermissionTo("order");
        Role::findByName("store_mgr")->givePermissionTo("manufacturer");
        Role::findByName("store_mgr")->givePermissionTo("rating");

        //system_mgr
        Role::findByName("system_mgr")->syncPermissions(Role::findByName("store_mgr")->getAllPermissions());
        Role::findByName("system_mgr")->givePermissionTo("user");

        //Super Admin
        Role::findByName("Super Admin")->givePermissionTo("admin");
    }

    private function init()
    {

        DB::table('roles')->delete();
        DB::table('permissions')->delete();

        Role::create(['name' => 'student']);
        Role::create(['name' => 'student_LM']);
        Role::create(['name' => 'clerk']);
        Role::create(['name' => 'store_mgr']);
        Role::create(['name' => 'system_mgr']);
        Role::create(['name' => 'Super Admin']);

        Permission::create(['name' => 'dish']);
        Permission::create(['name' => 'dish.read']);
        Permission::create(['name' => 'dish.modify']);
        Permission::create(['name' => 'dish.modify.create']);
        Permission::create(['name' => 'dish.modify.update']);
        Permission::create(['name' => 'dish.modify.delete']);
        Permission::create(['name' => 'sale']);
        Permission::create(['name' => 'sale.modify']);
        Permission::create(['name' => 'sale.modify.create']);
        Permission::create(['name' => 'sale.modify.update']);
        Permission::create(['name' => 'sale.modify.delete']);
        Permission::create(['name' => 'balance']);
        Permission::create(['name' => 'balance.read']);
        Permission::create(['name' => 'balance.read.money']);
        Permission::create(['name' => 'balance.read.log']);
        Permission::create(['name' => 'balance.read.self']);
        Permission::create(['name' => 'balance.read.self.money']);
        Permission::create(['name' => 'balance.read.self.log']);
        Permission::create(['name' => 'balance.modify']);
        Permission::create(['name' => 'balance.modify.topup']);
        Permission::create(['name' => 'balance.modify.refund']);
        Permission::create(['name' => 'user']);
        Permission::create(['name' => 'user.read']);
        Permission::create(['name' => 'user.modify']);
        Permission::create(['name' => 'user.modify.create']);
        Permission::create(['name' => 'user.modify.update']);
        Permission::create(['name' => 'user.modify.delete']);
        Permission::create(['name' => 'order']);
        Permission::create(['name' => 'order.read']);
        Permission::create(['name' => 'order.read.self']);
        Permission::create(['name' => 'order.read.class']);
        Permission::create(['name' => 'order.read.class.today']);
        Permission::create(['name' => 'order.modify']);
        Permission::create(['name' => 'order.modify.create']);
        Permission::create(['name' => 'order.modify.create.self']);
        Permission::create(['name' => 'order.modify.update']);
        Permission::create(['name' => 'order.modify.update.self']);
        Permission::create(['name' => 'order.modify.delete']);
        Permission::create(['name' => 'order.modify.delete.self']);
        Permission::create(['name' => 'order.modify.delete.class']);
        Permission::create(['name' => 'manufacturer']);
        Permission::create(['name' => 'manufacturer.modify']);
        Permission::create(['name' => 'manufacturer.modify.create']);
        Permission::create(['name' => 'manufacturer.modify.update']);
        Permission::create(['name' => 'manufacturer.modify.delete']);
        Permission::create(['name' => 'rating']);
        Permission::create(['name' => 'rating.create']);
        Permission::create(['name' => 'admin']);
        Permission::create(['name' => 'admin.hide']);
    }
}
