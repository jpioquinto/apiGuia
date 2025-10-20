<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    protected $connection = 'guia';
    protected $table = 'cat_estatus_proyecto';
    protected $primaryKey = 'id_estatus';
}
