<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Relations\{HasOne, HasMany, BelongsTo};
use App\Models\Diagnostic\Sigcm\{Organizacion as OrgMunicipal};
use App\Models\Diagnostic\Sigirc\{Organizacion as OrgEstatal};
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

#use Illuminate\Support\Facades\Log;

class Proyecto extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_proyectos';
    protected $primaryKey = 'id_proyecto';

    public $timestamps = false;

    protected $guarded = [];

    public function versiones(): HasMany
    {
        return $this->hasMany(Version::class,'id_proyecto','id_proyecto');
    }

    public function status(): HasOne
    {
        return $this->hasOne(Estatus::class, 'id_estatus', 'estatus');
    }

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(OrgEstatal::class, 'id_organizacion', 'id_organizacion');
    }

    public function sigcm(): BelongsTo
    {
        return $this->belongsTo(OrgMunicipal::class, 'id_organizacion', 'id_organizacion');
    }

    public function esEstatal(): bool
    {
        return $this->id_app_diag==1 && $this->organizacion->tipo != 3;
    }

    public function getAnioAttribute()
    {
        if (!$this->fecha) {
            return date('Y');
        }
        return (int)(new Carbon($this->fecha))->format('Y');
    }

    public function getMunicipioAttribute()
    {
        if ($this->id_app_diag==2) {
            return $this->sigcm->nombre_organizacion;
        }

        return $this->organizacion->municipio->municipio;
    }

    public function getDescVertienteAttribute()
    {
        $vertientes = ['1'=>'PEMC', '2'=>'PEMR', '1,2'=>'PEMI'];

        return $vertientes[$this->vertiente] ?: '';
    }
}
