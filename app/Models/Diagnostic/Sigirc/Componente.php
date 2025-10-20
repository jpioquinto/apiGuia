<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_componentes';
    protected $primaryKey = 'componentes_id';

    public function ponderacion()
    {
        return $this->hasOne(Ponderacion::class,'componentes_id','componentes_id');
    }
    public function ponderacionanterior()
    {
        return $this->hasOne(PonderacionAnterior::class,'componentes_id','componentes_id');
    }
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class,'id_componente','componentes_id');
    }
}
