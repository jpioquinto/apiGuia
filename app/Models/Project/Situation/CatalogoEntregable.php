<?php

namespace App\Models\Project\Situation;

use Illuminate\Database\Eloquent\Model;

class CatalogoEntregable extends Model
{
    protected $connection = 'guia';
    protected $table = 'cat_entregables';
    protected $primaryKey = 'id_entregable';
}
