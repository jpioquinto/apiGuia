<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project\Proyecto;
use Illuminate\Http\Request;

class FiscalizacionController extends Controller
{
    public function index(Proyecto $proyecto)
    {
        abort_if(
            ($ultimaVersion = $proyecto->versiones->where('version',$proyecto->versiones->max('version'))->first())==null,
            400,
            'No se encontró la versión para este proyecto.'
        );
        $aporteFederal = $this->obtenerAportacionFederal($ultimaVersion->desarrollo);
        return [
            'millar'=>round($this->calcularFiscalizacion($aporteFederal, $aporteFederal/1000), 2)
        ];
    }

    protected function obtenerAportacionFederal($desarrollo)
    {#dd($desarrollo);exit;
        $aportacion = 0;
        $desarrollo->each(function($value, $index) use (&$aportacion) {
            $aportacion += is_numeric($value['aportacion_federal']) ? $value['aportacion_federal'] : 0;
        });
        return $aportacion;
    }

    protected function calcularFiscalizacion($montoFederal, $fiscalizacion)
    {
        $nuevaFiscalizacion = ($montoFederal + $fiscalizacion)/1000;
        if (($nuevaFiscalizacion-$fiscalizacion)<0.001) {
          return $nuevaFiscalizacion;
        }
        return $this->calcularFiscalizacion($montoFederal,$nuevaFiscalizacion);
    }
}
