<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_entidad';
    protected $primaryKey = 'estados_id';

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'estado_id', 'estados_id');
    }
}
