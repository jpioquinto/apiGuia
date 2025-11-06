<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasOne, HasMany};

class Detalle extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_detalle_diagnosticos';
    protected $primaryKey = 'detalle_diagnosticos_id';

    public function conservacionAcervo(): HasMany
    {
        return $this->hasMany(Acervo::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }

    public function oficinas(): HasMany
    {
        return $this->hasMany(Oficina::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }

    public function personal(): HasMany
    {
        return $this->hasMany(Personal::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }

    public function tabla(): HasOne
    {
        return $this->hasOne(Tabla::class, 'detalle_diagnosticos_id', 'detalle_diagnosticos_id');
    }
}
