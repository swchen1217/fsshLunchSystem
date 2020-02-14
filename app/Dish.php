<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    //id,saleDate,factory,name,price,status,contents,calories,protein,fat,carbohydrate,stars,note,photo

    protected $guarded = ['id', 'photo'];

    public function contents()
    {
        return $this->hasMany('App\DishContent','dish_id');
    }
}
