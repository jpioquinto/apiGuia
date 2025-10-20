<?php

namespace App\Http\Clases;

use App\Http\Controllers\API\Situation\Situacion;

class SituacionActual
{
    protected $proyecto;

    protected $situacion;

    protected $diagnostico;


    public function __construct(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;

        $this->situacion = new Situacion($proyecto->getId(), $proyecto->getIdDiagnostico());

        $this->diagnostico = new Diagnostico($proyecto->getIdDiagnostico(), $proyecto->getAppDiagnostico());
    }

    public function vista()
    {
        if (strcmp($this->proyecto->getVertiente(),'1,2')==0) {
            return $this->vistaSituacionCatastral() . $this->vistaSituacionRegistral(2);
        }

        return (strcmp($this->proyecto->getVertiente(),'1')==0)
        ? $this->vistaSituacionCatastral()
        : $this->vistaSituacionRegistral();
    }

    protected function vistaSituacionCatastral($datos = [])
    {
        $situacion = $this->situacion->obtenerSituacion();

        return view(
            "reports/project/situation/catastro",
            [
                'anio'=>$this->diagnostico->getAnio(),
                'anioProyecto'=>$this->proyecto->getAnio(),
                'filas'=>$situacion['pec']['tabla'] ?? '',
                'totales'=>$situacion['pec']['totales'] ?? '',
            ]
        );
    }

    protected function vistaSituacionRegistral($datos = [])
    {
        $situacion = $this->situacion->obtenerSituacion();

        return view(
            "reports/project/situation/registro",
            [
                'anio'=>$this->diagnostico->getAnio(),
                'anioProyecto'=>$this->proyecto->getAnio(),
                'filas'=>$situacion['pem']['tabla'] ?? '',
                'totales'=>$situacion['pem']['totales'] ?? '',
            ]
        );
    }
}
