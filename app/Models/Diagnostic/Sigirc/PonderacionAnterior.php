<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class PonderacionAnterior extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_criterios_ponderacion_old';
    protected $primaryKey = 'criterios_ponderacion_id';
}
