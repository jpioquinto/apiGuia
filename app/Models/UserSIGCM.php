<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSIGCM extends Model
{

    protected $fillable = [];
    protected $hidden = [];
    protected $casts = [];

    protected $connection = 'sigcm';
    protected $table = 'tbl_usuarios';
    protected $primaryKey = 'usuarios_id';
    protected const SIGCM = 2;

    public function origen()
    {
        return self::SIGCM;
    }

    public function directorio()
    {
        return $this->hasOne(DirectorioCM::class, 'directorio_id', 'directorio_id');
    }
}
