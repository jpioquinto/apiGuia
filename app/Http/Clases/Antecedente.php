<?php

namespace App\Http\Clases;

use App\Http\Controllers\API\Antecedent\{Antecedentes, AntecedenteRegistral, AntecedenteCatastral};
use App\Models\Project\Proyecto;

class Antecedente
{
    protected $antecedente;

    protected $proyecto;

    public function __construct(Proyecto $proyecto)
    {
        $this->antecedente = new Antecedentes($this->proyecto = $proyecto, $proyecto->vertiente ?? null);
        $this->antecedente->inicializaDiagnostico();
    }

    public function vista()
    {
        if (strcmp($this->proyecto->vertiente,'1,2')==0) {
            return $this->vistaAntecedenteCatastral() . $this->vistaAntecedenteRegistral(2);
        }

        return (strcmp($this->proyecto->vertiente,'1')==0)
        ? $this->vistaAntecedenteCatastral()
        : $this->vistaAntecedenteRegistral();
    }

    public function vistaAntecedenteCatastral($subIndice = 1)
    {
        $antecedente = $this->antecedenteCatastral($this->proyecto);
        return view(
            "reports/project/antecedent/catastro",
            array_merge([
                'subind'=>$subIndice,
                'vistaPersonal'=>$this->vistaPersonal($antecedente['personal']),
                'anioDiagnostico'=>$this->antecedente->obtenerAnioDiagnostico()
            ], $antecedente)
        );
    }

    public function vistaAntecedenteRegistral($subIndice = 1)
    {
        $antecedente = $this->antecedenteRegistral($this->proyecto);
        return view(
            "reports/project/antecedent/registro",
            array_merge([
                'subind'=>$subIndice,
                'vistaPersonal'=>$this->vistaPersonal($antecedente['personal']),
                'anioDiagnostico'=>$this->antecedente->obtenerAnioDiagnostico()
            ], $antecedente)
        );
    }

    protected function vistaPersonal($datos)
    {
        return view("reports/project/antecedent/".($this->proyecto->anio>2019 ? 'personalV2' : 'personalV1'), ['datos'=>$datos]);
    }

    protected function antecedenteCatastral(Proyecto $proyecto)
    {
        $catastro = new AntecedenteCatastral($proyecto);
        return $catastro->antecedenteCatastral();
    }

    protected function antecedenteRegistral(Proyecto $proyecto)
    {
        $registro = new AntecedenteRegistral($proyecto);
        return $registro->antecedenteRegistral();
    }
}
