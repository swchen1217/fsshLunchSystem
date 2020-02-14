<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DishContent;
use Faker\Generator as Faker;

$factory->define(DishContent::class, function (Faker $faker) {
    $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));
    $r=rand(0,2);
    return [
        'content'=>$r==0?$faker->vegetableName():($r==1?$faker->meatName():$faker->sauceName())
    ];
});
