<?php

namespace App\Models\Project\Config;

use Illuminate\Database\Eloquent\Model;

class Antecedente extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_config_antecedentes';
    protected $primaryKey = 'id';
}
