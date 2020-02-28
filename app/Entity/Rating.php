<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public $timestamps = false;

    protected $guarded=['id'];
}
