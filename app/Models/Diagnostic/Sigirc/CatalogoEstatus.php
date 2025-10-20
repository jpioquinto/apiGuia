<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class CatalogoEstatus extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'cat_estatus';
    protected $primaryKey = 'id_estatus';
}
