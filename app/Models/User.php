<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password'];

    public $timestamps = false;

    protected $connection = 'sigirc';
    protected $table = 'tbl_usuarios';
    protected $primaryKey = 'usuarios_id';
    protected const SIGIRC = 1;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            #'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function origen()
    {
        return self::SIGIRC;
    }

    public function directorio()
    {
        return $this->hasOne(Directorio::class, 'directorio_id', 'directorio_id');
    }
}
