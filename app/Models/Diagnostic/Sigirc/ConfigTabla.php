<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class ConfigTabla extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_config_captura_parametros';
    protected $primaryKey = 'id_config';
}
