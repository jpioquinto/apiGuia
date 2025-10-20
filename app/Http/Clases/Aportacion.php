<?php

namespace App\Http\Clases;

class Aportacion
{
    protected $distribucionV1 = [
        'total'=> ['leyenda'=>'Subtotales', 'federal'=>0, 'estatal'=>0, 'total'=>0],
        'millar'=>['leyenda'=>'Más Uno al Millar para la Fiscalización', 'federal'=>0, 'estatal'=>'', 'total'=>0],
        'gTotal'=> ['leyenda'=>'Total incluido Uno al Millar', 'federal'=>0, 'estatal'=>0, 'total'=>0],
        'porcentaje'=> ['leyenda'=>'Porcentajes', 'federal'=>0, 'estatal'=>0, 'total'=>100]
    ];

    protected $distribucionV2 = [
        'total'=> ['leyenda'=>'Total', 'federal'=>0, 'estatal'=>0, 'total'=>0],
        'porcentaje'=> ['leyenda'=>'Porcentajes', 'federal'=>0, 'estatal'=>0, 'total'=>100],
        'millar'=>['leyenda'=>'Más Uno al Millar para la Fiscalización', 'federal'=>0, 'estatal'=>'', 'total'=>0],
        'gTotal'=> ['leyenda'=>'Total incluido Uno al Millar', 'federal'=>0, 'estatal'=>0, 'total'=>0]
    ];

    protected $porcFactura = ['estatal'=>0, 'federal'=>0];

    protected $distribucion;

    protected $proyecto;

    protected $porcDecimales;

    public function __construct(Proyecto $proyecto, $subtotales)
    {
        $this->proyecto = $proyecto;

        $this->setNumDecPorc($proyecto->getAnio()==2022 ? 2 : 8);

        $this->setDistribucion($proyecto->getAnio()<2018 ? $this->distribucionV1 : $this->distribucionV2);

        $this->inicializar($subtotales);
    }

    public function setDistribucion($distribucion)
    {
        $this->distribucion = $distribucion;
    }

    public function getDistribucion()
    {
        return $this->distribucion;
    }

    public function getPorcFactura()
    {
        return $this->porcFactura;
    }

    public function setNumDecPorc($porcDecimales)
    {
        $this->porcDecimales = $porcDecimales;
    }

    public function getNumDecPorc()
    {
        return $this->porcDecimales;
    }

    protected function inicializar($subtotales)
    {
        $this->distribucion['total'] = $subtotales;

        $this->distribucion['millar']['federal'] = $this->distribucion['millar']['total'] = $this->obtenerAlMillar();

        $this->distribucion['gTotal']['federal'] = $this->distribucion['millar']['federal'] + $this->distribucion['total']['federal'];
        $this->distribucion['gTotal']['estatal'] = $this->distribucion['total']['estatal'];

        $this->distribucion['gTotal']['total']   = $this->distribucion['gTotal']['federal'] + $this->distribucion['gTotal']['estatal'];

        $this->distribucion['porcentaje']['federal'] = $this->asignarPorcentaje('federal');

        $this->distribucion['porcentaje']['estatal'] = $this->asignarPorcentaje('estatal');

        $this->porcFactura['estatal'] = $this->asignarPorcentajeFactura('estatal');

        $this->porcFactura['federal'] = $this->asignarPorcentajeFactura('federal');

    }

    protected function obtenerAlMillar()
    {
        if ($this->proyecto->getMillar()>0) {
            return $this->proyecto->getMillar();
        }

        return $this->calcularFiscalizacion(
            $this->distribucion['total']['federal'], ($this->distribucion['total']['federal']*$this->proyecto->getAlMillar())/1000
        );
    }

    protected function asignarPorcentajeFactura($campo)
    {
        return $this->procesarPorcentaje(
            $this->calcularPorcentajes($this->distribucion['total']['total'], $this->distribucion['total'][$campo])
        );
    }

    protected function asignarPorcentaje($campo)
    {
        return $this->procesarPorcentaje(
            $this->calcularPorcentajes(
                $this->seTomaGranTotal() ? $this->distribucion['gTotal']['total'] : $this->distribucion['total']['total'],
                $this->seTomaGranTotal() ? $this->distribucion['gTotal'][$campo] : $this->distribucion['total'][$campo]
            )
        );
    }

    protected function procesarPorcentaje($cocientePorcentaje)
    {
        return (float)number_format($cocientePorcentaje*100, $this->getNumDecPorc());
    }

    protected function calcularPorcentajes($total, $aporte)
    {
        $total  = (!$total || !is_numeric($total)) ? 1 : $total;
        $aporte = (!$aporte || !is_numeric($aporte)) ? 0 : $aporte;

        return $aporte/$total;
    }

    protected function seTomaGranTotal()
    {

        return ($this->proyecto->getAnio()<2018 || $this->proyecto->getAnio()>2019);
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
