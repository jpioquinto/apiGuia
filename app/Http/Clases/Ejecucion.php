<?php

namespace App\Http\Clases;

class Ejecucion extends Desarrollo
{
    protected $programa;

    public function __construct($desarrollo, $componentesModelo, $idDiagnostico, $vertiente, $carpeta = '_documentos')
    {

        parent::__construct($desarrollo, $componentesModelo, $idDiagnostico, $carpeta);

        $this->inicializarPrograma($this->obtenerComponentes($vertiente), $vertiente);
    }

    public function setPrograma($programa)
    {
        $this->programa = $programa;
    }

    public function getPrograma()
    {
        return $this->programa;
    }

    protected function inicializarPrograma($componentes, $vertiente)
    {
        if (strcmp($vertiente, '1,2')==0) {
            $this->setPrograma( $this->homologarComponentes($componentes) ); return;
        }

        $this->setPrograma( $this->agruparActividadesComponente($componentes) );
    }

    protected function agruparActividades($actividades)
    {
        $listado = [];
        foreach ($actividades as $actividad) {
            if (!isset($listado["subcomp_{$actividad['subcomp']}"])) {
                $listado["subcomp_{$actividad['subcomp']}"] = [];
            }

            $listado["subcomp_{$actividad['subcomp']}"][] = $actividad;
        }

        return $listado;
    }

    protected function agruparActividadesComponente($componentes)
    {
        $listado = [];
        foreach ($componentes as $componente) {
            $listado[$componente['id']] = [
                'vertiente'=>$componente['vertiente'],
                'nombre'=>$componente['nombre'],
                'actividades'=>$this->agruparActividades($componente['actividades']),
                'programa'=>$componente['programa'],
            ];
        }

        return $listado;
    }

    protected function homologarComponentes($componentes)
    {
        $listado  = [];
        $integral = [];

        foreach ($componentes as $componente) {
            $listado[$componente['id']] = $componente;
        }

        foreach ($listado as $componente) {
            if (isset($integral[$componente['id']])) {
                continue;
            }

            $idHomologo =  $this->obtenerIdHomologo($componente['id']);
            if ($this->estaHomologado($componente['id']) && ($idHomologo>0 && $integral[$idHomologo])) {
                $integral[$idHomologo]['nombre'] = $this->homologos[$idHomologo]['nombre'];
                continue;
            }

            $integral[$componente['id']] = $componente;
            if (isset($this->homologos[$componente['id']]) && isset($listado[$this->homologos[$componente['id']]['id']])) {
                $integral[$componente['id']]['actividades'] = $integral[$componente['id']]['actividades']->merge($listado[$componente['id']]['actividades']);
            }

            $integral[$componente['id']]['actividades'] = $this->agruparActividades($integral[$componente['id']]['actividades']);
        }

        return $integral;
    }

    protected function obtenerIdHomologo($idComponente)
    {
        $id = $idComponente;
        foreach ($this->homologos  as $idComp=>$componente) {
            if ($this->homologos[$idComp]['id']==$idComponente) {
                $id = $idComp; break;
            }
        }
        return $id;
    }
}
