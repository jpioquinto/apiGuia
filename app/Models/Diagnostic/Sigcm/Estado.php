<?php

namespace App\Models\Diagnostic\Sigcm;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'tbl_entidad';
    protected $primaryKey = 'estados_id';

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'cvegeoedo', 'estados_id');
    }
}
