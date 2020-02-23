<?php

use App\Entity\Nutrition;
use Illuminate\Database\Seeder;
use App\Entity\Dish;
use App\Entity\DishContent;
use Illuminate\Support\Facades\DB;
use Faker\Generator;

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
        $price=[50,55];
        $start_date="2020-03-02";
        $date=strtotime($start_date);
        $times=2;
        $faker=Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));
        for($n=0;$n<$times;$n++){
            for($d=0;$d<5;$d++){
                for($f=0;$f<3;$f++){
                    for($i=0;$i<2;$i++){
                        $nid=Nutrition::created(['calories'=>rand(750,850),'protein'=>rand(40,50),'fat'=>rand(16,18),'carbohydrate'=>rand(115,125)]);
                        $did=Dish::created(['name'=>$name=$faker->foodName(),'manufacturer_id'=>$f,'nutrition_id'=>$nid,'price'=>$price[$i],'photo'=>date('YmdHis',time()).'_'.substr(md5($name),0,6)]);
                        for($c=0;$c<rand(3,7);$c++){
                            $r=rand(0,2);
                            DishContent::created(['dish_id'=>$did,'name'=>$r==0?$faker->vegetableName():($r==1?$faker->meatName():$faker->sauceName())]);
                        }
                    }
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
