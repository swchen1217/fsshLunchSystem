<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Dish extends Model
{
    //id,saleDate,factory,name,price,status,contents,calories,protein,fat,carbohydrate,stars,note,photo

    protected $guarded = ['id'];

    public function contents()
    {
        return $this->hasMany('App\DishContent');
    }

    /*public function manufacturer()
    {
        return $this->hasOne('App\Entity\Manufacturer','id');
    }

    public function nutrition()
    {
        return $this->hasOne('App\Entity\Nutrition','id');
    }*/
}
