<?php

namespace App\Http\Clases\Store;

use App\Http\Clases\Validations\ValidaDesarrollo;
use Illuminate\Database\Eloquent\Relations\{Relation, HasMany};
use App\Models\Project\Desarrollo AS ModelDesarrollo;
use Illuminate\Support\Facades\Log;

class ReplicaVerDesarrollo
{
    protected $desarrollo;

    public function __construct(Relation $modelDesarrollo, object $componente)
    {
        $this->crear($modelDesarrollo, $componente);
    }

    public function setDesarrollo(ModelDesarrollo $desarrollo)
    {
        $this->desarrollo = $desarrollo;
    }

    public function crear(Relation $modelDesarrollo, object $componente)
    {
        $campos = [
            'estrategia_desarrollo'=>$componente->estrategia_desarrollo ?? '',
            'aportacion_federal'=>$componente->aportacion_federal ?? 0,
            'aportacion_estatal'=>$componente->aportacion_estatal ?? 0,
            'situacion_actual'=>$componente->situacion_actual ?? '',
            'id_componente'=>$componente->id_componente,
            'tipo_recurso'=>$componente->tipo_recurso ?? 0,
            'nomb_comp'=>$componente->nomb_comp,
            'indice'=>$componente->indice,
        ];

        $this->setDesarrollo($modelDesarrollo->create($campos));

        $this->crearObjetivo($componente->objetivos ?? collect([]));
        $this->crearPrograma($componente->programas ?? collect([]));
        $this->crearActividad($componente->actividades ?? collect([]));

        if (isset($componente->acervo['oficinas'])) {
            $this->crearOficinaRPP($componente->acervo['oficinas']  ?? collect([]));
        }

        return isset($this->desarrollo->id_desarrollo);
    }

    public function crearObjetivo($objetivos)
    {
        $objetivos->each(function ($value, $index) {
            new Objetivo(
                $this->desarrollo->objetivos(),
                [
                    'objetivo'=>$value['objetivo_especifico'] ?? '',
                    'alcance'=>$value['alcance'] ?? '',
                    'indice'=>$value['indice'] ?? '',
                ]
            );
        });
    }

    public function crearPrograma($programas)
    {
        $programas->each(function ($value, $index) {
                new Programa(
                    $this->desarrollo->programas(),
                    ['indice'=>$value['indice'] ?? 0, 'meses'=>$value['meses'] ?? '']
                );
        });
    }

    public function crearOficinaRPP($oficinas)
    {
        $oficinas->each(function ($value, $index) {
            $this->registrarAcervo($value['acervo']);
        });
    }

    protected function registrarAcervo($acervo)
    {
        $acervo->each(function ($value, $index) {
            new OficinaRPP(
                $this->desarrollo->oficinas(),
                [
                    'oficina'=>$value['oficina'] ?? 0,
                    'concepto'=>$value['concepto'] ?? '',
                    'acervo_existe'=>$value['acervo_existe'] ?? 0,
                    'acervo_digitalizado'=>$value['acervo_digitalizado'] ?? 0,
                    'porc_digitalizado'=>$value['porc_digitalizado'] ?? 0,
                    'libros_legajos'=>$value['libros_legajos'] ?? 0,
                    'num_imagenes'=>$value['num_imagenes'] ?? 0,
                ]
            );
        });
    }

    public function crearActividad($actividades)
    {
        $actividades->each(function ($value, $index) {
            $campos = [
                    'anexos'=>'',//quitar despues de pruebas
                    'subcomp'=>$value['id_subcomponente'] ?? 0,
                    'act'=>$value['id_cat_actividad'] ?? 0,
                    'desc'=>$value['descripcion'] ?? '',
                    'entregable'=>$value['id_entregable'] ?? 0,
                    'unidad'=>$value['id_unidad'] ?? 0,
                    'cantidad'=>$value['cantidad'] ?? 0,
                    'costo'=>$value['costo_unitario'] ?? 0,
                    'iva'=>$value['iva'] ?? 0,
                    'total'=>$value['total'] ?? 0,
                    'munpios'=>explode(',', $value['municipios'] ?? ''),
            ];

            isset($value['programa_ejecucion']) ? $campos['ejecucion'] = $value['programa_ejecucion'] : null;
            isset($value['id_sub_actividad'])   ? $campos['subact']    = $value['id_sub_actividad']   : null;
            isset($value['tipo_recurso'])    ? $campos['tipoRecurso']  = $value['tipo_recurso']       : null;
            isset($value['monto_estatal'])   ? $campos['mest']   = $value['monto_estatal'] : null;
            isset($value['monto_federal'])   ? $campos['mfed']   = $value['monto_federal'] : null;
            isset($value['comentarios']) ? $campos['comentarios'] = $value['comentarios'] : null;
            isset($value['historial'])   ? $campos['historial']   = $value['historial']   : null;

            new Actividad(
                $this->desarrollo->actividades(),
                $campos
            );
        });
    }
}
