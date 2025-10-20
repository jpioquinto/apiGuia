<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class MenuLateral extends Model
{
    protected $connection = 'guia';
    protected $table = 'cat_puntosdesarrollo';
    protected $primaryKey = 'id_punto';

    protected $casts = [
        'editable'=>'boolean',
    ];
}
