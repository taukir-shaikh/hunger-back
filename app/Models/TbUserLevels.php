<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbUserLevels extends Model
{
    protected $table = 'tb_user_levels';
    protected $primaryKey = 'user_level_id';
    public $timestamps = false;
    protected $guarded = [];
}
