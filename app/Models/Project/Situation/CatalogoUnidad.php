<?php

namespace App\Models\Project\Situation;

use Illuminate\Database\Eloquent\Model;

class CatalogoUnidad extends Model
{
    protected $connection = 'guia';
    protected $table = 'cat_unidades';
    protected $primaryKey = 'id_unidad';
}
