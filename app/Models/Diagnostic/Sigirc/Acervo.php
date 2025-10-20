<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Acervo extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_detalle_conservacion_4t';
    protected $primaryKey = 'id_conservacion';
}
