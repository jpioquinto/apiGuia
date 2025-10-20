<?php

namespace App\Models\Diagnostic\Sigcm;

use Illuminate\Database\Eloquent\Model;

class Sigcm extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'tbl_diagnosticos';
    protected $primaryKey = 'diagnosticos_id';
}
