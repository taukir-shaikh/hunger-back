<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class TbUsers extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'tb_users';
    protected $primaryKey = 'id';
    protected $hidden = ['password'];
    public $timestamps = true;
    protected $guarded = [];
}
