<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitySetting extends Model
{
    protected $fillable = [
        'city_name',
        'city_hall_name',
        'address',
        'ibge_code',
        'state',
        'zip_code',
        'phone',
        'email',
        'mayor_name',
    ];
} 