<?php

namespace App\Http\Controllers\API\Antecedent;

use App\Models\Project\Config\Antecedente;
use App\Models\Diagnostic\Sigirc\{Municipio,EstructuraPersonal,ConcentradoPersonal};
use App\Http\Traits\TraitDiagnostico;
#use App\Diagnostic\Diagnostico;
use Illuminate\Support\Arr;
use App\Models\Project\Proyecto;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class Antecedentes
{
    use TraitDiagnostico;
    public static $municipios;
    protected $config;

    public function __construct(Proyecto $proyecto, $vertiente=null)
    {
        $this->proyecto =$proyecto;

        $this->modeloDiagnostico = $this->obtenerModeloDiagnostico($proyecto->id_proyecto ?: 0);

        $this->inicializaVertiente( $vertiente ?? $this->obtenerVertienteInicial() );

    }
    public function inicializaConfiguracion($vertiente)
    {
        $this->config = Antecedente::where('vertiente', $vertiente)
                                    ->where('estatus', $this->obtenerEstatus($this->obtenerAnioDiagnostico()))
                                    ->get()->toArray();
    }
    public function obtenerAnioProyecto()
    {
        if ($this->proyecto!=null) {
            return (int)(new Carbon($this->proyecto->fecha))->format('Y');
        }
        return date('Y');
    }
    public static function obtenerMunicipios($idEstado)
    {
        if (self::$municipios==null) {

            self::$municipios = Municipio::where('estado_id', $idEstado)->get();
        }
        return self::$municipios;
    }
    protected function obtenerConfig($antecedente)
    {#Log::info('API request data:', ['antecedente:' => $antecedente, 'config:'=> $this->config]);print_r($this->config);
        $config = Arr::first($this->config, function ($value, $key) use ($antecedente) {
            return $value['antecedente'] === $antecedente;
        });
        return $config;
    }
    protected function obtenerDetalleDiagnostico($antecedente)
    {##dd($this->obtenerConfig($antecedente='predios'));exit;
        if ( ($config=$this->obtenerConfig($antecedente)) == null ) {
            return;
        }
        #dd($this->diagnostico->detalles->tabla());exit(0);
        $parametros = explode(',',$config['parametro_id']);

        return $this->diagnostico->detalles->filter(function ($value, $key) use ($parametros) {
            return in_array($value['parametros_id'], $parametros)===TRUE;
        });
        #return $this->diagnostico->detalles()->whereIn('parametros_id', explode(',',$config->parametro_id));
    }
    protected function obtenerOficinas()
    {
        $detalle = $this->obtenerDetalleDiagnostico('oficinas')->first();

        if ($detalle->oficinas->isEmpty()) {
            return;
        }

        return $this->procesarDatosOficina($detalle->oficinas);
    }
    protected function obtenerPersonal()
    {
        $detalle = $this->obtenerDetalleDiagnostico('personal')->first();

        if ($detalle==null || $detalle->personal->isEmpty()) {
            return;
        }

        return $detalle->personal->sortBy('categoria');
    }
    protected function procesarDatosOficina($oficinas)
    {
        $municipios = self::obtenerMunicipios($this->obtenerDiagnostico()->organizacion->estados_id);
        if ($municipios->isEmpty()) {
            return;
        }
        $cantidad = ["centralizadas"=>0, "regionales"=>0, "totalOficinas"=>$oficinas->count()];
        $oficinas->each(function ($value, $key) use ($municipios, &$cantidad) {
            $cobertura = explode(',', $value['municipios']);
            $cantidad['centralizadas'] += count($cobertura) == $municipios->count() ? 1 : 0;
            $cantidad['regionales']    += count($cobertura)>=1 && count($cobertura)<$municipios->count() ? 1 : 0;
        });
        return $cantidad;
    }
    protected function procesarDatosPersonal($personal)
    {
        $tipoPersonal = [
            1=>new EstructuraPersonal('Directivo'),
            2=>new EstructuraPersonal('Mando medio'),
            3=>new EstructuraPersonal('Operativo'),
            4=>new EstructuraPersonal('Otro'),
            5=>new EstructuraPersonal('Total')
        ];
        $personal->each(function ($value, $key) use (&$tipoPersonal) {
            if (!isset($tipoPersonal[$value['categoria']])) {
                return true;
            }
            $tipoPersonal[$value['categoria']]->actualizaDatos($value);
        });

        foreach ($tipoPersonal as $key => $value) {
            if ($key==5) {
                break;
            }
            $tipoPersonal[5]->administracion += $value->administracion;
            $tipoPersonal[5]->comunicacion   += $value->comunicacion;
            $tipoPersonal[5]->contabilidad   += $value->contabilidad;
            $tipoPersonal[5]->derecho        += $value->derecho;
            $tipoPersonal[5]->ingenieria     += $value->ingenieria;
            $tipoPersonal[5]->logistica      += $value->logistica;
            $tipoPersonal[5]->mercadotecnia  += $value->mercadotecnia;
            $tipoPersonal[5]->recursos       += $value->recursos;
            $tipoPersonal[5]->tecnologias    += $value->tecnologias;
        }
        return $tipoPersonal;
    }
    protected function procesarConcentradoPersonal($personal)
    {
        $tipoPersonal = [
            1=>new ConcentradoPersonal('Directivo'),
            4=>new ConcentradoPersonal('Mando medio'),
            2=>new ConcentradoPersonal('Administrativo'),
            3=>new ConcentradoPersonal('Operativo'),
            5=>new ConcentradoPersonal('Total')
        ];
        $concentrado  = [];
        $personal->each(function ($value, $key) use (&$tipoPersonal, &$concentrado) {
            if (!isset($tipoPersonal[$value['categoria']])) {
                return true;
            }
            $tipoPersonal[$value['categoria']]->actualizarDatos($value);
            /*$objPersonal = new ConcentradoPersonal($tipoPersonal[$value['categoria']]);
            $objPersonal->actualizarDatos($value);
            $concentrado[$value['categoria']][] = $objPersonal;*/
        });
        /*$totalPersonal = new ConcentradoPersonal('Total');
        foreach ($concentrado as $key => $registros) {
            foreach ($registros as $registro) {
                $totalPersonal->cantidad += $registro->cantidad;
            }
        }
        $concentrado[5][] = $totalPersonal;
        return $concentrado;*/
        foreach ($tipoPersonal as $key => $value) {
            if ($key==5) {
                break;
            }
            $tipoPersonal[5]->cantidad += $value->cantidad;
            $tipoPersonal[5]->confianza += $value->confianza;
            $tipoPersonal[5]->honorarios += $value->honorarios;
        }#dd($tipoPersonal);exit;
        return $tipoPersonal;
    }
    protected function generarTablaPersonal($personal)
    {
        $filas = [];
        $estructura = json_decode(json_encode($personal), true);
        foreach ($estructura as $value) {
            $filas[] = $value;
        }
        return $filas;
    }
    protected function generarTablaConcentradoPersonal($personal)
    {
        $estructura = collect();
        foreach ($personal as $value) {
            $estructura->push($value);
        }
        return $estructura;
    }
    protected function obtenerTablaPersonal()
    {
        if ($this->obtenerPersonal() == null) {
            return [];
        }
        if ($this->obtenerAnioDiagnostico()>2019) {
            return $this->generarTablaConcentradoPersonal(
                $this->procesarConcentradoPersonal($this->obtenerPersonal())
            );
        }
        return $this->generarTablaPersonal(
            $this->procesarDatosPersonal($this->obtenerPersonal())
        );
    }
}
