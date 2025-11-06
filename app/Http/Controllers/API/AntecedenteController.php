<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Antecedent\{Antecedentes, AntecedenteRegistral, AntecedenteCatastral};
use App\Models\Diagnostic\Diagnostico;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project\Proyecto;
use Illuminate\Support\Facades\Log;
class AntecedenteController extends Controller
{
    protected $proyecto;

    public function index(Request $request)
    {
        $this->setProyecto(Proyecto::where('id_proyecto', $request->id)->first() ?? new Proyecto());

        $antecedente = new Antecedentes($this->proyecto);
        (int)$request->idDiagnostico > 0 ? $antecedente->inicializaDiagnostico($antecedente->diagnosticoEntidad((int)$request->idDiagnostico)) : $antecedente->inicializaDiagnostico();

        if (strcmp($antecedente->obtenerVertiente(),'1,2')==0) {
            return [
                'anioDiagnostico'=>$antecedente->obtenerAnioDiagnostico(),
                'idDiagnostico'=>$antecedente->obtenerIdDiagnostico(),
                'pec'=>$this->antecedenteCatastral($this->proyecto),
                'pem'=>$this->antecedenteRegistral($this->proyecto)
            ];
        }

        $respuesta['anioDiagnostico'] = $antecedente->obtenerAnioDiagnostico();
        $respuesta['idDiagnostico'] = $antecedente->obtenerIdDiagnostico();
        #Log::info('API request data:', ['vertiente' => $antecedente->obtenerVertiente(), 'diagnosticoId'=>$request->idDiagnostico, 'respuesta'=>$respuesta]);
        (intval($antecedente->obtenerVertiente())==1)
        ? $respuesta['pec'] = $this->antecedenteCatastral($this->proyecto, $antecedente->obtenerDiagnostico())
        : $respuesta['pem'] = $this->antecedenteRegistral($this->proyecto, $antecedente->obtenerDiagnostico());

        return $respuesta;
    }

    public function setProyecto(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    }

    protected function antecedenteCatastral(Proyecto $proyecto, Diagnostico $diagnostico=null)
    {
        $catastro = new AntecedenteCatastral($proyecto, $diagnostico);
        return $catastro->antecedenteCatastral();
    }

    protected function antecedenteRegistral(Proyecto $proyecto, Diagnostico $diagnostico=null)
    {
        $registro = new AntecedenteRegistral($proyecto, $diagnostico);
        return $registro->antecedenteRegistral();
    }
}
