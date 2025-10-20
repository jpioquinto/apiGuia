<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_detalle_diagnosticos';
    protected $primaryKey = 'detalle_diagnosticos_id';

    public function conservacionAcervo()
    {
        return $this->hasMany(Acervo::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }

    public function oficinas()
    {
        return $this->hasMany(Oficina::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }

    public function personal()
    {
        return $this->hasMany(Personal::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }

    public function tabla()
    {
        return $this->hasOne(Tabla::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }
}
