<?php

namespace App\Http\Controllers\API\Situation;

use App\Http\Traits\TraitDiagnostico;
use App\Models\Project\Situation\{CatalogoSubcomponente};
use App\Models\Project\Proyecto;

class Situacion
{
    use TraitDiagnostico;
    #protected $diagnostico;
    protected $totalPonderacion = 0;
    protected $totalCalificacion = 0;
    protected $totalAvance = 0;
    protected $subComponente;
    protected $ultimaVersion;
    protected $desarrollo;
    protected $series = [];

    public function __construct($idProyecto, $idDiagnostico)
    {
        $this->proyecto = Proyecto::with(
                                    [
                                        'versiones'=> function ($query) use ($idProyecto) {
                                            $query->where('id_version', $this->obtenerUltimaVersion($idProyecto)->id_version);
                                        },
                                        'versiones.desarrollo','versiones.desarrollo.actividades'
                                    ]
                        )->where('id_proyecto', $idProyecto)->first();

        $this->inicializaVertiente( $this->obtenerVertienteInicial() );
        $this->modeloDiagnostico = $this->obtenerModeloDiagnostico($idProyecto);
        $this->diagnostico = $this->consultarDiagnostico($idDiagnostico);
        $this->subComponente = CatalogoSubcomponente::with('actividades')->where('estatus', 1)->get();
        #$this->desarrollo = $this->obtenerDesarrollo();
    }
    public function obtenerSituacion()
    {
        $respuesta = [];
        if (strcmp($this->obtenerVertiente(),'1,2')==0) {
            $respuesta['pec'] = [
                                'tabla'=>$this->generaTablaComparativa( $this->obtenerComponentes(1) ),
                                'totales'=>$this->generarFilaTotales(),
                                'series'=>$this->obtenerSerie()
                            ];
            $respuesta['pem'] = [
                                'tabla'=>$this->generaTablaComparativa( $this->obtenerComponentes(2) ),
                                'totales'=>$this->generarFilaTotales(),
                                'series'=>$this->obtenerSerie()
                            ];
            return $respuesta;

        }
        $vertiente = (strcmp($this->obtenerVertiente(),'1')==0) ? 'pec' : 'pem';

        $respuesta[$vertiente] = [
                            'tabla'=>$this->generaTablaComparativa( $this->obtenerComponentes($this->obtenerVertiente()) ),
                            'totales'=>$this->generarFilaTotales(),
                            'series'=>$this->obtenerSerie()
                        ];
        return $respuesta;
    }
    public function inicializarSerie($nombreComponente, $modelo, $puntaje, $estimacion)
    {
        $this->series[] = [
            'category'=>$nombreComponente,
            'puntaje'=>round($puntaje, 2),
            'estimacion'=>round($estimacion, 2),
            'modelo'=>$modelo
        ];
    }
    public function obtenerSerie()
    {
        return $this->series;
    }
    protected function obtenerComponentes($vertiente)
    {
        return $this->modeloDiagnostico::obtenerModeloComponentes()
                ->with(
                    ($this->diagnostico->ano_proyecto >= 2017 ? 'ponderacion' : 'ponderacionanterior'),
                    'calificaciones'
                )
                ->where('modelos_id',$vertiente)
                ->whereNotIn('componentes_id', [17,18])
                #->groupBy('componentes_id')
                ->orderBy('orden','ASC')
                ->get();

    }
    protected function generaTablaComparativa($componentes)
    {
        $this->totalPonderacion = 0;
        $this->totalCalificacion = 0;
        $this->totalAvance = 0;

        $filas = ''; $this->series = [];
        $componentes->each(function ($value, $key) use (&$filas) {

            $filas .= $this->generarFila($value);
        });
        return $filas;
    }
    protected function generarFila($componente)
    {
        $ponderacion = $this->obtenerPonderacionModelo($componente);

        $obtenida    = $this->obtenerCalificacion($componente);

        $obtenida->calificacion = $ponderacion < $obtenida->calificacion ? $ponderacion : $obtenida->calificacion;

        $avanceEstimado = $this->obtenerEstimacionAvance($componente, $obtenida->calificacion);

        $avanceEstimado = $ponderacion < $avanceEstimado ? $ponderacion : $avanceEstimado;

        $icono = $avanceEstimado > $obtenida->calificacion ? ' '.$this->iconoAvance() : '';

        $this->setearTotalPonderacion($ponderacion);

        $this->setearTotalCalificacion($obtenida->calificacion);

        $this->setearTotalAvance($avanceEstimado);

        $this->inicializarSerie($componente->nombre_corto, $ponderacion, $obtenida->calificacion, $avanceEstimado);

        $tr = sprintf("<tr><td>%s</td>", $componente->nombre);
        $tr.= sprintf("<td class='has-text-centered'>%s</td>", $ponderacion);
        $tr.= sprintf("<td class='has-text-centered'>%s</td>", number_format($obtenida->calificacion, 2));
        $tr.= sprintf("<td class='has-text-centered'>%s</td></tr>", number_format($avanceEstimado, 2).$icono);

        return $tr;
    }
    protected function generarFilaTotales()
    {
        $tr = sprintf("<tr class='has-background-light has-text-weight-medium'><td>%s</td>", 'Total');
        $tr.= sprintf("<td class='has-text-centered'>%s</td>", $this->totalPonderacion);
        $tr.= sprintf("<td class='has-text-centered'>%s</td>", $this->totalCalificacion);
        $tr.= sprintf("<td class='has-text-centered'>%s</td></tr>", $this->totalAvance);

        return $tr;
    }
    protected function obtenerPonderacionModelo($componente)
    {
        return  $this->diagnostico->ano_proyecto >= 2017
                ? $componente->ponderacion->sedatu : $componente->ponderacionanterior->sedatu;
    }
    protected function obtenerCalificacion($componente)
    {
        return $componente->calificaciones->where('id_diagnostico', $this->diagnostico->diagnosticos_id)->last();

    }
    protected function obtenerEstimacionAvance($componente, $calificacion)
    {
        $avance = $calificacion;
        if ($this->proyecto!=null) {
            $avance += $this->calcularEstimacionAvance($componente);
        }
        return $avance;
    }
    protected function calcularEstimacionAvance($componente)
    {
        $promedio            = $this->obtenerPromedioCalificacion($componente);
        $numActividades      = $this->obtenerNumeroActividades($componente->componentes_id);
        $actividadesProyecto = $this->obtenerTotalActividades($componente->componentes_id);

        return ($actividadesProyecto * $promedio) / ( $numActividades==0 ? 1 : $numActividades);
    }
    protected function obtenerPromedioCalificacion($componente, $anio=2014)
    {
        $suma = $componente->calificaciones->where('id_componente',$componente->componentes_id)
                                            ->where('anio',$anio)
                                            ->sum('calificacion');

        return (!is_numeric($suma) ? 0  : $suma)/32;
    }
    protected function obtenerNumeroActividades($idComponente)
    {
        $subComponente = $this->subComponente->filter(function ($value, $key) use ($idComponente) {
            return in_array($idComponente, explode(',', $value['componente']))===TRUE;
        });
        $total = 0;
        $subComponente->each(function ($value, $key) use (&$total) {
            $total += $value->actividades->where('estatus',1)->count();
        });

        return $total;
    }
    protected function obtenerTotalActividades($idComponente)
    {
        $desarrollo = $this->proyecto->versiones->first()->desarrollo->filter(function ($value, $key) use ($idComponente) {
            return $value['id_componente']==$idComponente;
        });

        if ($desarrollo->first()==null || !isset($desarrollo->first()->actividades)) {
            return 0;
        }
        return $desarrollo==null ? 0 : $desarrollo->first()->actividades->groupBy('id_cat_actividad')->count();
    }
    protected function obtenerUltimaVersion($idProyecto=0)
    {
        if ($this->ultimaVersion==null) {
            $proyectoVersiones = Proyecto::with('versiones')->where('id_proyecto', $idProyecto)->first();
            $this->ultimaVersion = $proyectoVersiones->versiones->where('version',$proyectoVersiones->versiones->max('version'))->first();
        }
        return $this->ultimaVersion;
    }
    protected function iconoAvance()
    {
        return '<span style="font-size:14pt;font-weight:bold;color:#0C7D02;">^</span>';
    }
    protected function setearTotalPonderacion($ponderacion)
    {
        $this->totalPonderacion += $ponderacion;
    }
    protected function setearTotalCalificacion($calificacion)
    {
        $this->totalCalificacion += round($calificacion, 2);
    }
    protected function setearTotalAvance($avance)
    {
        $this->totalAvance += round($avance, 2);
    }
    protected function obtenerDesarrollo()
    {
        $ultimaVersion = $this->obtenerUltimaVersion();
        return $ultimaVersion->desarrollo->where('id_version', $ultimaVersion->id_version);
    }
}
