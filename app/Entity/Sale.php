<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];
}
