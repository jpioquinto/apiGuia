<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_anexos';
    protected $primaryKey = 'id_anexo';

    public $timestamps = false;

    protected $guarded = [];

    public function getUrlAttribute()
    {
        return '';
    }
}
