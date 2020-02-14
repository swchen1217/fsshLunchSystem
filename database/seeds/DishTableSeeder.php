<?php

use Illuminate\Database\Seeder;
use App\Dish;
use App\DishContent;
use Illuminate\Support\Facades\DB;

class DishTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Dish::all()->count() != 0)
            DB::table('dishes')->delete();
        if (DishContent::all()->count() != 0)
            DB::table('dish_contents')->delete();
        Dish::unguard();
        DishContent::unguard();
        $dish = factory(Dish::class, 20)->create();
        foreach ($dish as $item) {
            factory(DishContent::class, rand(3, 7))->create(['dish_id' => $item->id]);
        }
        DishContent::reguard();
        Dish::reguard();
    }
}
