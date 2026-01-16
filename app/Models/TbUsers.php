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

    /**
     * Override createToken to generate a longer token string.
     */
    public function createToken(string $name, array $abilities = ['*'])
    {
        $plainTextToken = \Illuminate\Support\Str::random(80);
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
        ]);
        return new \Laravel\Sanctum\NewAccessToken($token, $plainTextToken);
    }
}
