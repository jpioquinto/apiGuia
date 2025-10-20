<?php

namespace App\Models\Project\Situation;

use Illuminate\Database\Eloquent\Model;

class CatalogoSubActividad extends Model
{
    protected $connection = 'guia';
    protected $table = 'cat_subactividades';
    protected $primaryKey = 'id_subactividad';
}
