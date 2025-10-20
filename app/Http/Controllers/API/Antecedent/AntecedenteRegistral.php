<?php

namespace App\Http\Controllers\API\Antecedent;

use App\Models\Diagnostic\Diagnostico AS ModelDiagnostic;
use App\Models\Project\Proyecto;

class AntecedenteRegistral extends Antecedentes
{
    public function __construct(Proyecto $proyecto, ModelDiagnostic $diagnostico=null)
    {
        parent::__construct($proyecto, 2);
        $this->inicializaDiagnostico($diagnostico);
        $this->inicializaConfiguracion(2);
    }
    public function antecedenteRegistral()
    {
        return [
            'ingreso'=>$this->generarTablaPresupuesto($this->obtenerPresupuestoAnual()),
            'oficinas'=>$this->generarDatosTablaOficina($this->obtenerOficinas()),
            'acervo'=>$this->generarDatosTablaAcervo($this->obtenerSituacionAcervo()),
            'personal'=>$this->obtenerTablaPersonal()
        ];
    }
    protected function obtenerSituacionAcervo()
    {
        $detalle = $this->obtenerDetalleDiagnostico('acervo');

        if ($detalle==null) {
            return;
        }

        $config=$this->obtenerConfig('acervo');

        return $this->procesarSituacionAcervo($detalle, json_decode($config->extra, true));
    }
    protected function obtenerPresupuestoAnual()
    {
        $detalle = $this->obtenerDetalleDiagnostico('presupuesto');
        if ($detalle==null) {
            return null;
        }
        return $detalle->first();
    }
    protected function obtenerIngresoAnual()
    {
        $detalle = $this->obtenerDetalleDiagnostico('ingreso');
        if ($detalle==null) {
            return null;
        }
        return $detalle->first();
    }
    protected function procesarSituacionAcervo($detalle, $config)
    {
        $captura = [];
        $detalle->each(function ($value, $key) use(&$captura, $config) {
            foreach ($config as $index => $val) {
                if (!isset($captura[$index])) {
                    $captura[$index] = 0;
                }
                if ($value['parametros_id']==$val) {
                    $captura[$index] = number_format($value['cantidad']);
                }
            }
        });
        return $captura;
    }
    protected function generarDatosTablaOficina($oficinas)
    {
        return [
            ["concepto"=>"Número de oficinas registrales centralizadas", "cantidad"=>$oficinas['centralizadas']],
            ["concepto"=>"Número de oficinas regionales", "cantidad"=>$oficinas['regionales']],
            ["concepto"=>"Total de oficinas registrales",  "cantidad"=>$oficinas['totalOficinas']]
        ];
    }
    protected function generarDatosTablaAcervo($acervo)
    {
        if ($acervo==null) {
            return;
        }
        $acervo['digitalizacion'] = trim($acervo['digitalizacion'])!='' ? $acervo['digitalizacion'].'%' : $acervo['digitalizacion'];
        return [
            ["concepto"=>"Número de inmuebles y/o predios", "cantidad"=>$acervo['inmuebles']],
            ["concepto"=>"Número de predios vinculados con el catastro estatal", "cantidad"=>$acervo['vinculados']],
            ["concepto"=>"Porcentaje de digitalización",  "cantidad"=>$acervo['digitalizacion']]
        ];
    }
    protected function generarTablaPresupuesto($presupuesto)
    {
        $ingreso = $this->obtenerIngresoAnual();
        $cantidadIngreso = $ingreso!=null ? $ingreso->cantidad : '';
        $cantidadPresup  = $presupuesto!=null ? $presupuesto->cantidad : '';
        $cantidadIngreso = is_numeric($cantidadIngreso) ? '$'.number_format($cantidadIngreso, 2) : '';
        $cantidadPresup = is_numeric($cantidadPresup) ? '$'.number_format($cantidadPresup, 2) : '';
        $colspan = $this->obtenerAnioDiagnostico()<2019 ? "colspan='3'" : "";

        return "<tr class='has-background-grey-dark has-text-white-ter has-text-weight-semibold'>
            <td>Presupuesto del ejercicio fiscal ".$this->obtenerAnioDiagnostico()."</td>".
            "<td {$colspan}>".$cantidadPresup."</td></tr>".
            "<tr><td>Ingreso Anual del Registro Público de la Propiedad</td><td {$colspan}>".$this->obtenerAnioDiagnostico()."</td></tr>".
            "<tr><td>Por concepto de Derechos</td><td {$colspan}>".$cantidadIngreso."</td></tr>";
    }
}
