<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Tabla extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_detalle_tablas';
    protected $primaryKey = 'id_detalle_tabla';
}
