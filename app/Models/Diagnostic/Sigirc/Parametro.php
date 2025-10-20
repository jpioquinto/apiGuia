<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_parametros';
    protected $primaryKey = 'parametros_id';
}
