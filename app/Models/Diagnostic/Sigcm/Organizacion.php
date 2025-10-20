<?php

namespace App\Models\Diagnostic\Sigcm;

use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'cat_dir_organizacion';
    protected $primaryKey = 'id_organizacion';

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estados_id', 'estados_id');
    }
}
