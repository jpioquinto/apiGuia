<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'cat_dir_organizacion';
    protected $primaryKey = 'id_organizacion';

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estados_id', 'estados_id');
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
