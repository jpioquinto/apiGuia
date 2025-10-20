<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_objetivos_desarrollo';
    protected $primaryKey = 'id_objetivo_des';

    public $timestamps = false;

    protected $guarded = [];
}
