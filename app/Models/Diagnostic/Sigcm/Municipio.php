<?php

namespace App\Models\Diagnostic\Sigcm;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'cat_munpios_superficie';
    protected $primaryKey = 'id_cat_munpio';
}
