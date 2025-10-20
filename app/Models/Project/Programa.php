<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_programas_desarrollo';
    protected $primaryKey = 'id_programa';

    public $timestamps = false;

    protected $guarded = [];
}
