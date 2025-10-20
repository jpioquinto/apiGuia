<?php

namespace App\Http\Controllers\API\Antecedent;

use App\Models\Diagnostic\Diagnostico AS ModelDiagnostic;
use App\Models\Diagnostic\Sigirc\{ConfigTabla};
use App\Models\Project\Proyecto;

class AntecedenteCatastral extends Antecedentes
{
    protected $ConfigTabla;
    public function __construct(Proyecto $proyecto, ModelDiagnostic $diagnostico=null)
    {
        parent::__construct($proyecto, 1);
        $this->inicializaDiagnostico($diagnostico);
        $this->inicializaConfiguracion(1);
        $this->ConfigTabla = ConfigTabla::where('status_id', 1)->orderBy('orden','ASC')->get();
    }
    public function antecedenteCatastral()
    {
        return [
            'cartografia'=>$this->obtenerCoberturaCartografica(),
            'oficinas'=>$this->generarDatosTablaOficina($this->obtenerOficinas()),
            'predios'=>$this->obtenerPredios(),
            'personal'=>$this->obtenerTablaPersonal()
        ];
    }
    protected function obtenerCoberturaCartografica()
    {
        $detalle = $this->obtenerDetalleDiagnostico('cartografia')->first();

        if ($detalle==null) {
            return [];
        }
        #dd($detalle->detalle_diagnosticos_id);exit;
        $config = $this->obtenerConfig('cartografia');

        return $this->generarDatosTablaCartografia(
            $this->procesarDatosPredios($detalle->tabla, $config),
            json_decode($config['extra'])
        );
    }
    protected function obtenerPredios()
    {
        $detalle = $this->obtenerDetalleDiagnostico('predios')->first();

        if ($detalle==null) {
            return [];
        }

        $config = $this->obtenerConfig('predios');#dd($detalle);exit;
#dd($this->procesarDatosPredios($detalle->tabla, $config));exit;
        return $this->generarDatosTablaPredios(
            $this->procesarDatosPredios($detalle->tabla, $config),
            json_decode($config['extra']),
            $this->procesarPrediosVinculados('p_vinc_rpp')
        );
    }
    protected function procesarDatosPredios($captura, $config)
    {
        $columnas   = $this->ConfigTabla->filter(function ($value, $key) use ($config) {
            return $value['parametros_id'] == $config['parametro_id'];
        });
        if (!isset($captura->datos)) {
            return [];
        }

        #dd($this->iterarMunicipios($columnas, json_decode($captura->datos)));exit;
        return $this->iterarMunicipios($columnas, json_decode($captura->datos));
    }
    protected function procesarPrediosVinculados($antecedente)
    {
        $captura = $this->obtenerDetalleDiagnostico($antecedente)->first();
        if ($captura==null) {
            return;
        }
        return $captura->cantidad ?? null;
    }
    protected function iterarMunicipios($columnas, $captura)
    {
        $totales = [];
        self::obtenerMunicipios($this->obtenerDiagnostico()->organizacion->estados_id)->each(function ($value, $key) use ($columnas, &$totales, $captura) {
            $columnas->each(function ($val, $index) use ($value, &$totales, $captura) {
                if ($val['tipo_dato'] != 1) {
                    return true;
                }
                if (!isset($value['municipio_id']) || !isset($captura[$val['id_config']][$value['municipio_id']])) {
                    return true;
                }
                if (!isset($totales[$val['id_config']])) {
                    $totales[$val['id_config']] = 0;
                }
                $totales[$val['id_config']] += is_numeric($captura[$val['id_config']][$value['municipio_id']])
                                           ? $captura[$val['id_config']][$value['municipio_id']] : 0;
            });
        });
        return $totales;
    }
    protected function generarDatosTablaCartografia($cartografia, $config)
    {
        return [
            [
                "concepto"=>"Vuelo fotogramétrico",
                "cantidad"=>isset($cartografia[$config->vuelo]) ? number_format($cartografia[$config->vuelo], 2) : 0
            ],
            [
                "concepto"=>"Ortofotos",
                "cantidad"=>isset($cartografia[$config->ortofoto]) ? number_format($cartografia[$config->ortofoto], 2) : 0
            ],
            [
                "concepto"=>"Restitución gráfica lineal",
                "cantidad"=>isset($cartografia[$config->restitucion]) ? number_format($cartografia[$config->restitucion], 2) : 0
            ]
        ];

    }
    protected function generarDatosTablaOficina($oficinas)
    {
        return [
            ["concepto"=>"Oficina centralizada", "cantidad"=>$oficinas['centralizadas']],
            ["concepto"=>"Número de oficinas catastrales", "cantidad"=>$oficinas['regionales']],
            ["concepto"=>"Total de oficinas catastrales",  "cantidad"=>$oficinas['totalOficinas']]
        ];
    }
    protected function generarDatosTablaPredios($predios, $config, $vinculadosRPP)
    {
        return [
            [
                "concepto"=>"Predios urbanos",
                "cantidad"=>isset($predios[$config->p_urbano]) ? number_format($predios[$config->p_urbano], 0) : 0
            ],
            [
                "concepto"=>"Predios rústicos",
                "cantidad"=>isset($predios[$config->p_rustico]) ? number_format($predios[$config->p_rustico], 0) : 0
            ],
            [
                "concepto"=>"Predios vinculados con Registro Público de la Propiedad",
                "cantidad"=>is_numeric($vinculadosRPP) ? number_format($vinculadosRPP, 0) : null
            ]
        ];
    }
}
