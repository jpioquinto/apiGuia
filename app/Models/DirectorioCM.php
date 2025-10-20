<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Diagnostic\Sigcm\Organizacion;

class DirectorioCM extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'apl_directorio';
    protected $primaryKey = 'directorio_id';

    public function organizacion()
    {
        return $this->hasOne(Organizacion::class, 'id_organizacion', 'id_organizacion');
    }

    public function getNombreCompletoAttribute()
    {
        $nombre = $this->nombre;
        $nombre .= trim($this->ape_paterno) != '' ? ' '.trim($this->ape_paterno) : '';
        $nombre .= trim($this->ape_materno) != '' ? ' '.trim($this->ape_materno) : '';

        return $nombre;
    }
}
