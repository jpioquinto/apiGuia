<?php

namespace App\Http\Traits;

use App\Http\Clases\Store\ReplicaVerDesarrollo;
use Illuminate\Support\Facades\Log;

trait TraitVersionProyecto
{
    public function prepararCamposSeguimiento(array $datos = [])
    {
        if (!isset($this->versionPrev->version)) {
            return $datos;
        }

        !isset($datos['intro'])      ? $datos['intro'] = $this->versionPrev->seguimiento->introduccion      : null;
        !isset($datos['situacion'])  ? $datos['situacion'] = $this->versionPrev->seguimiento->situacion_gral : null;
        !isset($datos['logros'])     ? $datos['logros'] = $this->versionPrev->seguimiento->logros_aplicacion : null;
        !isset($datos['objetivo'])   ? $datos['objetivo'] = $this->versionPrev->seguimiento->objetivo_gral   : null;
        !isset($datos['metas'])      ? $datos['metas'] = $this->versionPrev->seguimiento->metas_globales     : null;
        !isset($datos['obsResumen']) ? $datos['obsResumen'] = $this->versionPrev->seguimiento->observaciones_resumen : null;

        return $datos;
    }

    public function replicarDesarrollo(array $excluidos = [])
    {
        if (!isset($this->versionPrev->version)) {
            return;
        }

        #Log::info('Versión actual...', ['model'=>$this->version->desarrollo()]);
        $modelDesarrollo = $this->version->desarrollo();
        $this->versionPrev->desarrollo->each(function($value, $index) use ($excluidos, $modelDesarrollo) {
            if (in_array($value['id_componente'], $excluidos)) {
                return true;
            }
            #Log::info('componente:', ['type'=>gettype($value)]);
            $registro = new ReplicaVerDesarrollo($this->version->desarrollo(), $value);
        });
    }
}
