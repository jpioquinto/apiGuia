<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project\Situation\{
    CatalogoSubcomponente, CatalogoActividad, CatalogoSubActividad, CatalogoEntregable, CatalogoUnidad
};

class Actividad extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_actividades_desarrollo';
    protected $primaryKey = 'id_actividad';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'con_iva'=>'boolean'
    ];

    public function subcomponente()
    {
        return $this->hasOne(CatalogoSubcomponente::class,'id_subcomponente', 'id_subcomponente');
    }

    public function catactividad()
    {
        return $this->hasOne(CatalogoActividad::class, 'id_actividad', 'id_cat_actividad');
    }

    public function catsubactividad()
    {
        return $this->hasOne(CatalogoSubActividad::class, 'id_subactividad', 'id_sub_actividad');
    }

    public function entregable()
    {
        return $this->hasOne(CatalogoEntregable::class, 'id_entregable', 'id_entregable');
    }

    public function unidad()
    {
        return $this->hasOne(CatalogoUnidad::class, 'id_unidad', 'id_unidad');
    }

    public function anexos()
    {
        return $this->hasMany(Anexo::class, 'id_actividad', 'id_actividad');
    }
}
