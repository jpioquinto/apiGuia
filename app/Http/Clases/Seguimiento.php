<?php

namespace App\Http\Clases;

use App\Models\Project\Proyecto AS Modelo;

class Seguimiento
{
    protected $ultimaVersion;

    public function __construct($ultimaVersion = null)
    {
        $this->ultimaVersion = $ultimaVersion;
    }

    public function getIntroduccion()
    {
        return $this->ultimaVersion->seguimiento->introduccion ?: '';
    }

    public function getSituacion()
    {
        return $this->ultimaVersion->seguimiento->situacion_gral ?: '';
    }

    public function getLogro()
    {
        return $this->ultimaVersion->seguimiento->logros_aplicacion ?: '';
    }

    public function getObjetivo()
    {
        return $this->ultimaVersion->seguimiento->objetivo_gral ?: '';
    }

    public function getObservaResumen()
    {
        return $this->ultimaVersion->seguimiento->observaciones_resumen ?: '';
    }

    public function getMeta()
    {
        return $this->ultimaVersion->seguimiento->metas_globales ?: '';
    }

}
