<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Ponderacion extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_criterios_ponderacion';
    protected $primaryKey = 'criterios_ponderacion_id';
}
