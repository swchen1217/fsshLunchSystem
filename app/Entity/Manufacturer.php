<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];
}
