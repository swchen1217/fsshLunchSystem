<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Exception;

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
        try{
            $dish = Dish::findOrFail($id);
            $tmp=array_merge($dish->toArray(),array('contents'=>Dish::find($id)->contents->toArray()));
            return $tmp;
        }catch (Exception $e){
            throw $e;
        }
    }
}
