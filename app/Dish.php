<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    //id,saleDate,factory,name,price,status,contents,calories,protein,fat,carbohydrate,stars,note,photo

    protected $guarded = ['id', 'photo'];

    public function contents()
    {
        return $this->hasMany('App\DishContent');
    }

    public static function getDish()
    {
        $dish = Dish::all()->toArray();
        foreach ($dish as $key => $value){
            $dish[$key]=array_merge($value,array('contents'=>Dish::find($value['id'])->contents->toArray()));
        }
        return $dish;
    }

    public static function getDishById($id)
    {
        $dish = Dish::find($id)->toArray();
        $tmp=array_merge($dish,array('contents'=>Dish::find($id)->contents->toArray()));
        return $tmp;
    }
}
