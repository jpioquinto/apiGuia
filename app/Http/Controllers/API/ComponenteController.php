<?php

namespace App\Http\Controllers\API;

use App\Http\Traits\TraitDiagnostico;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project\Proyecto;

class ComponenteController extends Controller
{
    use TraitDiagnostico;

    public function index(Request $request)
    {
        return [
            'vertiente'=>is_numeric($request->vertiente) ? (int)$request->vertiente : $request->vertiente,
            'datos'=>$this->procesarListado( $this->obtenerComponentes($request->idProyecto, $request->vertiente) )
        ];
    }

    protected function procesarListado($componentes)
    {
        #$desarrollo = $this->obtenerComponentesDesarrollo();
        $componentes->each(function($value, $key) use (&$componentes) {
            $componentes[$key]['deshabilitado'] = false;#$desarrollo->contains('id_componente', $value['componentes_id']);
        });

        return $componentes;
    }

    protected function obtenerComponentes($idProyecto, $vertiente)
    {
        return $this->obtenerTipoModelo($idProyecto)::obtenerModeloComponentes()
        ->query()
        ->select(['componentes_id','modelos_id','nombre','nombre_corto','orden'])
        ->whereIn('modelos_id', explode(',', $vertiente))
        ->whereNotIn('componentes_id', [17,18])
        ->orderBy('orden','ASC')
        ->get();
    }

    protected function obtenerTipoModelo($idProyecto)
    {
        $this->proyecto = Proyecto::where('id_proyecto', $idProyecto)->first();
        return $this->obtenerModeloDiagnostico($idProyecto);
    }

    protected function obtenerComponentesDesarrollo()
    {
        if ($this->proyecto==null || ($ultimaVersion = $this->proyecto->versiones->where('version',$this->proyecto->versiones->max('version'))->first())==null) {
            return collect();
        }
        return $ultimaVersion->desarrollo;
    }
}
