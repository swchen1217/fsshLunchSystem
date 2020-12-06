<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Dish;
use Faker\Generator as Faker;

$factory->define(Dish::class, function (Faker $faker) {
    $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));
    $factoryArr=['正園','御饌坊','彩鶴'];
    $priceArr=['50','55'];
    return [
        'saleDate'=>$date=$faker->dateTimeBetween($startDate = 'now', $endDate = '+30 days')->format('Y-m-d H:i:s'),
        'factory'=>$factoryArr[array_rand($factoryArr)],
        'name'=>$name=$faker->foodName(),
        'price'=>$priceArr[array_rand($priceArr)],
        'status'=>'S',
        'calories'=>$faker->numberBetween($min = 10, $max = 1000),
        'protein'=>$faker->numberBetween($min = 10, $max = 1000),
        'fat'=>$faker->numberBetween($min = 10, $max = 1000),
        'carbohydrate'=>$faker->numberBetween($min = 10, $max = 1000),
        'stars'=>$faker->numberBetween($min = 1, $max = 5),
        'note'=>'',
        'photo'=>date('YmdHis',strtotime($date)).'_'.substr(md5($name),0,6),
    ];
});
