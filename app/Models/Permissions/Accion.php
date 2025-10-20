<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    use HasFactory;

    protected $connection = 'guia';
    protected $table = 'tbl_acciones';
    protected $primaryKey = 'id_acciones';
}
