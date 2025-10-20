<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_municipios';
    protected $primaryKey = 'municipio_id';
}
