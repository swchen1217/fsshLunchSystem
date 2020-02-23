<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class DishContent extends Model
{
    public $timestamps = false;

    protected $fillable = ['name','dish_id'];
}
