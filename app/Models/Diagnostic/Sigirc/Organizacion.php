<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\Model;
use App\Models\Project\Proyecto;

class Organizacion extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'cat_dir_organizacion';
    protected $primaryKey = 'id_organizacion';

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estados_id', 'estados_id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'mpio', 'municipio_id');
    }

    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'id_organizacion', 'id_organizacion');
    }

    public function obtenerMunicipios()
    {
        if ($this->esMunicipal()) {
            return $this->estado->municipios->where('municipio_id', $this->mpio)->get();
        }
        return $this->estado->municipios;
    }

    public function esMunicipal()
    {
        return $this->tipo==3;
    }
}
