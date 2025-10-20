<?php

namespace App\Models\Project\Situation;

use Illuminate\Database\Eloquent\Model;

class CatalogoActividad extends Model
{
    protected $connection = 'guia';
    protected $table = 'cat_actividades';
    protected $primaryKey = 'id_actividad';
}
