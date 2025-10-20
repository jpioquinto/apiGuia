<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_detalle_personal';
    protected $primaryKey = 'detalle_personal_id';
}
