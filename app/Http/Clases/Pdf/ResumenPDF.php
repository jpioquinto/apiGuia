<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\{Proyecto, ResumenFinanciero};

class ResumenPDF
{
    protected $resumen;
    protected $proyecto;

    public function __construct(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;

        $this->resumen = new ResumenFinanciero(
                                $proyecto,
                                $proyecto->getVersion()->desarrollo ?? collect([]),
                                $proyecto->diagnostico->getComponentes($proyecto->getVertiente())
                            );

    }

    public function vista()
    {
        return view(
            "reports.project.resumen.tabla_componentes",
            [
                'anio'=>$this->proyecto->getAnio(),
                'tfootTotales'=>$this->generarTfootTotales(),
                'filasComponente'=>$this->generarFilasComponente(),
                'observaciones'=>$this->proyecto->seguimiento->getObservaResumen(),
                'tabla_aportaciones'=>view(
                    "reports.project.resumen.tabla_aportaciones",
                    array_merge(
                        $this->formatearDatos($this->resumen->aportacion->getDistribucion()),
                        ['porcFactura'=>$this->resumen->aportacion->getPorcFactura()]
                    )
                )
            ]
        );
    }

    protected function generarFilasComponente()
    {
        $filas = '';
        $this->resumen->getComponentes()->each(function ($componente, $index) use (&$filas) {
            $componente['aporteFederal'] = number_format($componente['aporteFederal'], $this->resumen->getNumDec());
            $componente['aporteEstatal'] = number_format($componente['aporteEstatal'], $this->resumen->getNumDec());
            $componente['total']         = number_format($componente['total'], $this->resumen->getNumDec());
            $filas .= view("reports.project.partial.fila_componente", $componente);
        });

        return $filas;
    }

    protected function generarTfootTotales()
    {
        if ($this->proyecto->getAnio()>=2021) {
            return view("reports.project.resumen._fila_totales", $this->formatoMoneda($this->resumen->getTotalComponentes()));
        }

        $filas = '';

        foreach ($this->resumen->aportacion->getDistribucion() as $key=>$distribucion) {
            $distribucion['fila'] = $key;

            $filas .= $this->proyecto->getAnio() < 2018
            ?  view("reports.project.resumen._filas_totales_v1", $distribucion)
            :  view("reports.project.resumen._filas_totales_v2", $distribucion);
        }

        return $filas;
    }

    protected function formatearDatos($distribucion)
    {
        foreach ($distribucion as $key=>$value) {
            if ($key==='porcentaje') {
                continue;
            }
            $distribucion[$key] = $this->formatoMoneda($value);
        }

        return $distribucion;
    }

    protected function formatoMoneda($datos)
    {
        $datos['federal'] = is_numeric($datos['federal']) ? number_format($datos['federal'], 2) : $datos['federal'];
        $datos['estatal'] = is_numeric($datos['estatal']) ? number_format($datos['estatal'], 2) : $datos['estatal'];
        $datos['total']   = is_numeric($datos['total'])   ? number_format($datos['total'], 2)   : $datos['total'];

        return $datos;
    }

}
