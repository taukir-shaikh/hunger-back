<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class TbRestaurants extends Model
{
    use HasApiTokens;

    protected $table = 'tb_restaurants';

protected $fillable = [
    'name',
    'description',
    'address',
    'latitude',
    'longitude',
    'open_time',
    'close_time',
    'is_active',
    'is_approved',
];

    // Define relationships, accessors, or other model methods as needed
}