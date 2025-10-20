<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_seguimientos';
    protected $primaryKey = 'id_seguimiento';

    public $timestamps = false;

    protected $guarded = [];
}
