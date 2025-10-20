<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $connection = 'guia';
    protected $table = 'tbl_permisos';
    protected $primaryKey = 'id_permiso';
}
