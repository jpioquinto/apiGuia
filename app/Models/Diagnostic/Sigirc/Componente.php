<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasOne, HasMany};

class Componente extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_componentes';
    protected $primaryKey = 'componentes_id';

    public function ponderacion(): HasOne
    {
        return $this->hasOne(Ponderacion::class,'componentes_id','componentes_id');
    }
    public function ponderacionanterior(): HasOne
    {
        return $this->hasOne(PonderacionAnterior::class,'componentes_id','componentes_id');
    }
    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class,'id_componente','componentes_id');
    }
}
