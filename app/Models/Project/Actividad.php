<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Relations\{HasOne, HasMany};
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

    public function subcomponente(): HasOne
    {
        return $this->hasOne(CatalogoSubcomponente::class,'id_subcomponente', 'id_subcomponente');
    }

    public function catactividad(): HasOne
    {
        return $this->hasOne(CatalogoActividad::class, 'id_actividad', 'id_cat_actividad');
    }

    public function catsubactividad(): HasOne
    {
        return $this->hasOne(CatalogoSubActividad::class, 'id_subactividad', 'id_sub_actividad');
    }

    public function entregable(): HasOne
    {
        return $this->hasOne(CatalogoEntregable::class, 'id_entregable', 'id_entregable');
    }

    public function unidad(): HasOne
    {
        return $this->hasOne(CatalogoUnidad::class, 'id_unidad', 'id_unidad');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(Anexo::class, 'id_actividad', 'id_actividad');
    }
}
