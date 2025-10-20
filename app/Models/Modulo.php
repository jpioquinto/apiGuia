<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $connection = 'guia';
    protected $table = 'tbl_modulos';
    protected $primaryKey = 'id_modulos';

    public $timestamps = false;

    protected $guarded = [];
}
