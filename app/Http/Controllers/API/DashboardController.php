<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project\{Proyecto, ProjectQueryBuilder as ProjectQuery};
use App\Models\Diagnostic\Sigirc\Estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function listarAnios(Request $request)
    {
        return response(['solicitud'=>true, 'message'=>'Listado de Años.', 'anios'=>ProjectQuery::listYearProjects()], 200);
    }

    public function listarEntidades(Request $request)
    {
        return response([
            'solicitud'=>true,
            'message'=>'Listado de Entidades.',
            'entidades'=>$this->procesarListado($this->consultarProyectoPorAnio($request->anio), $request->anio)
        ], 200);
    }

    public function listarProyectos(Request $request)
    {
        return response([
            'solicitud'=>true,
            'message'=>'Listado de Proyectos.',
            'proyectos'=>$this->prepararRegistros($this->consultarProyectosPorEntidad($request->anio, $request->edoId)),
        ], 200);
    }

    protected function prepararRegistros(object $proyectos)
    {
        $listado = collect();
        $proyectos->each(function ($value, $key) use (&$listado) {
            $listado->push([
                'id'=>$value->id_proyecto,
                'vertiente'=>$value->vertiente,
                'diagnosticoId'=>$value->id_diagnostico,
                'appDiag'=>$value->id_app_diag,
                'millar'=>0,
                'porcFed'=>0,
                'porcEst'=>0,
                'descVertiente'=>(!$value->esEstatal() ? "{$value->municipio} " : "") . $value->desc_vertiente . " - ({$value->status->descripcion})",
                'anio'=>$value->anio

            ]);
        });

        return $listado;
    }

    protected function procesarListado(object $entidades, int $anio): array
    {
        $listado = $this->listarRegistros($entidades);
        if (in_array($anio, [2016, 2017])) {
            $listado->merge($this->consultarProyectoPorAnio($anio, 'sigcm'));
        }

        $listadoOrdenado = $listado->sortBy('entidad');

        return $listadoOrdenado->values()->all();
    }

    protected function listarRegistros(object $entidades, string $organizacion = 'organizacion'): object
    {
        $listado = collect();
        $entidades->each(function($value, $key) use (&$listado, $organizacion) {
            if (!isset($value[$organizacion])) {
                return true;
            }
            if ($value[$organizacion]->estado && !$listado->contains('id', $value[$organizacion]->estado->estados_id)) {
                $listado->push([
                    'id'=>$value[$organizacion]->estado->estados_id,
                    'entidad'=>$value[$organizacion]->estado->estado,
                    'escudo'=>$value[$organizacion]->estado->escudo,
                    'edoIso'=>$value[$organizacion]->estado->estado_iso,
                    'poblacion'=>$value[$organizacion]->estado->poblacion,
                    'extTerritorial'=>$value[$organizacion]->estado->extension_territorial,
                    'distUrbana'=>$value[$organizacion]->estado->distribucion_urbana,
                    'distRural'=>$value[$organizacion]->estado->distribucion_rural,
                    'abreviatura'=>$value[$organizacion]->estado->abreviatura
                ]);
            }
        });
        return $listado;
    }

    protected function consultarProyectoPorAnio(int $anio, string $organizacion = 'organizacion'): object
    {
        return Proyecto::with([$organizacion, "{$organizacion}.estado"])
            ->whereRaw("year(fecha) = {$anio}")->get();
    }

    protected function consultarProyectosPorEntidad(int $anio, int $edoId): object
    {
        return Proyecto::whereRaw("year(fecha) = {$anio}")->get();
    }
}
