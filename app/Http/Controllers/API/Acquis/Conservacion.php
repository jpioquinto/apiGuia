<?php

namespace App\Http\Controllers\API\Acquis;

use Illuminate\Support\Arr;
use App\Models\Diagnostic\Sigirc\Sigirc;
use App\Models\Project\Config\Antecedente;
use App\Http\Traits\TraitDiagnostico;

class Conservacion
{
    use TraitDiagnostico;

    protected $config;

    public function __construct($idDiagnostico, $vertiente=2)
    {
        $this->diagnostico = $this->obtenerDiagnostico($idDiagnostico);
        $this->config = $this->obtenerConfiguracion($vertiente);
    }

    public function obtenerConfiguracion($vertiente)
    {
        return Antecedente::where('vertiente', $vertiente)
                            ->where('estatus', $this->obtenerEstatus($this->obtenerAnioDiagnostico()))
                            ->get();
    }

    public function obtenerOficinas($info = 'oficinas')
    {
        $detalle = $this->obtenerDetalleDiagnostico($info)->first();

        if (!$detalle || $detalle->{$info}->isEmpty()) {
            return collect();
        }

        return $detalle->{$info};
    }

    public function procesarListadoOficinas($listado, $capturas)
    {
        return  $listado->map(function ($value, $key) use ($capturas) {
            $value['agregada'] = $capturas->count()>0 ? $capturas->contains('oficina', $value->detalle_oficinas_id) : false;
            return $value;
        });
    }

    protected function obtenerDetalleDiagnostico($antecedente)
    {
        if ( ($config=$this->obtenerConfig($antecedente)) == null ) {
            return;
        }

        $parametros = explode(',',$config->parametro_id);

        return $this->diagnostico->detalles->filter(function ($value, $key) use ($parametros) {
            return in_array($value['parametros_id'], $parametros)===TRUE;
        });
    }

    protected function obtenerDiagnostico($idDiagnostico)
    {
        return  Sigirc::with(['detalles.conservacionAcervo', 'detalles.oficinas'])
                ->where('diagnosticos_id',$idDiagnostico)
                ->first();
    }

    protected function obtenerConfig($antecedente)
    {
        $config = Arr::first($this->config, function ($value, $key) use ($antecedente) {
            return $value->antecedente === $antecedente;
        });
        return $config;
    }
}
