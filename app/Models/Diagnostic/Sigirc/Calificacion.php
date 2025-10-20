<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_calificaciones';
    protected $primaryKey = 'id_calificacion';
}
