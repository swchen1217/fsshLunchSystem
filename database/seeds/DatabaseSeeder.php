<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(RBACSeeder::class);

        $this->call(ManufacturerSeeder::class);
        $this->call(DishTableSeeder::class);
    }
}
