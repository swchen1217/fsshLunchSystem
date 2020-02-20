<?php

use App\Entity\Nutrition;
use Illuminate\Database\Seeder;
use App\Entity\Dish;
use App\Entity\DishContent;
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
        /*if (Dish::all()->count() != 0)
            DB::table('dishes')->delete();
        if (DishContent::all()->count() != 0)
            DB::table('dish_contents')->delete();*/
        $start_date="2020-03-02";
        $date=strtotime($start_date);
        $times=2;
        for($n=0;$n<$times;$n++){
            for($d=0;$d<5;$d++){
                for($f=0;$f<3;$f++){
                    Nutrition::created();
                }
                $date= strtotime("+1 day",$date);
            }
            $date= strtotime("+2 day",$date);
        }


        $dish = factory(Dish::class, 20)->create();
        foreach ($dish as $item) {
            factory(DishContent::class, rand(3, 7))->create(['dish_id' => $item->id]);
        }
    }
}
