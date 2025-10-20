<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Oficina extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_detalle_oficinas';
    protected $primaryKey = 'detalle_oficinas_id';

}
