<?php

namespace App\Http\Clases;

class ResumenFinanciero extends Desarrollo
{
    protected $totalComponentes = ['leyenda'=>'Total', 'federal'=>0, 'estatal'=>0, 'total'=>0];
    protected $componentes;
    protected $proyecto;
    public $aportacion;

    public function __construct(Proyecto $proyecto, $desarrollo, $componentesModelo)
    {
        $this->proyecto = $proyecto;

        parent::__construct($desarrollo, $componentesModelo, $proyecto->getIdDiagnostico(), $proyecto->getCarpeta());

        $this->setComponentes($this->listarComponentes());

        $this->inicializaTotales($this->getComponentes());

        $this->aportacion = new Aportacion($proyecto, $this->getTotalComponentes());
    }

    public function setComponentes($componentes)
    {
        $this->componentes = $componentes;
    }

    public function getComponentes()
    {
        return $this->componentes;
    }

    public function getTotalComponentes()
    {
        return $this->totalComponentes;
    }

    public static function datoNumerico($dato)
    {
        return is_numeric(preg_replace('/[\s,$]*/', '', $dato)) ? (float)preg_replace('/[\s,$]*/', '', $dato) : 0.00;
    }

    protected function inicializaTotales($componentes)
    {
        $componentes->each(function ($componente, $index) {
            $this->totalComponentes['federal'] += is_numeric($componente['aporteFederal']) ? $componente['aporteFederal'] : 0;
            $this->totalComponentes['estatal'] += is_numeric($componente['aporteEstatal']) ? $componente['aporteEstatal'] : 0;
            $this->totalComponentes['total']   += is_numeric($componente['total']) ? $componente['total'] : 0;
        });

        $this->proyecto->getAnio()<2018 ? $this->totalComponentes['leyenda'] = 'Subtotales' : false;
    }

    protected function listarComponentes()
    {
        if (strcmp($this->proyecto->getVertiente(), '1,2')!=0) {
            return $this->obtenerMontosComponente($this->componentesModelo, $this->obtenerComponentes($this->proyecto->getVertiente()));
        }

        $componentesPEC = $this->homologarNombreComponente($this->tomarComponentes($this->componentesModelo));

        $componentesPEM =   $this->obtenerComponentesRestantes($this->tomarComponentes($this->componentesModelo, 2));
        if ($componentesPEM->isNotEmpty()) {
            $componentesPEC = $componentesPEC->merge($componentesPEM);
        }

        $listadoComponentes = $this->obtenerMontosComponente( $componentesPEC, $this->obtenerComponentes(1) );
        return $this->obtenerMontosComponente( $listadoComponentes, $this->obtenerComponentes(2), true );
    }

    protected function homologarNombreComponente($componentes)
    {
        return $componentes->map(function ($componente, $key) {
            if (!isset($this->homologos[$componente['componentes_id']])) {
                return $componente;
            }
            $componente['nombre'] = $this->homologos[$componente['componentes_id']]['nombre'];

            return $componente;
        });
    }

    protected function obtenerComponentesRestantes($componentes)
    {
        return $componentes->filter(function ($componente, $key) {
            return !$this->estaHomologado($componente['componentes_id']);
        });
    }

    protected function obtenerMontos($componente, $desarrollo)
    {
        if (!$this->estaHomologado($componente['componentes_id']) && $this->proyecto->getAnio()<=2020) {
            $componente['total'] = (isset($componente['total']) && is_numeric($componente['total']))
                                   ? $componente['total'] + $this->obtenerTotalComponente($desarrollo, $this->obtenerIdHomologo($componente['componentes_id']))
                                   : $this->obtenerTotalComponente($desarrollo, $this->obtenerIdHomologo($componente['componentes_id']));
            return $componente;
        }

        if (!isset($this->homologos[$componente['componentes_id']])) {
            $componente['aporteFederal'] = $this->obtenerAportacionFederal($desarrollo, $componente['componentes_id']);
            $componente['aporteEstatal'] = $this->obtenerAportacionEstatal($desarrollo, $componente['componentes_id']);
        }

        $componente['total'] = isset($componente['total'])
                            ? $componente['total'] + $this->obtenerTotalComponente($desarrollo, $this->obtenerIdHomologo($componente['componentes_id']))
                            : $this->obtenerTotalComponente($desarrollo, $this->obtenerIdHomologo($componente['componentes_id']));

        return $componente;

    }

    protected function obtenerAportacionFederal($desarrollo, $idComponente)
    {
        $componente = $desarrollo->where('id', $idComponente)->first();
        return isset($componente['aporteFederal']) ? (float)$componente['aporteFederal'] : 0;
    }

    protected function obtenerAportacionEstatal($desarrollo, $idComponente)
    {
        $componente = $desarrollo->where('id', $idComponente)->first();
        return isset($componente['aporteEstatal']) ? (float)$componente['aporteEstatal'] : 0;
    }

    protected function obtenerTotalComponente($desarrollo, $idComponente)
    {
        $componente = $desarrollo->where('id', $idComponente)->first();
        return isset($componente['total']) ? (float)$componente['total'] : 0;
    }

    protected function existeComponente($desarrollo, $idComponente)
    {
        return $desarrollo->where('id_componente', $idComponente)->first();
    }

    protected function obtenerMontosComponente($componentes, $desarrollo, $integral = false)
    {
        return $componentes->map(function ($componente, $index) use ($desarrollo, $integral) {
            if ($integral) {
                return $this->obtenerMontos($componente, $desarrollo);
            }

            $componente['aporteFederal'] = $this->obtenerAportacionFederal($desarrollo, $componente['componentes_id']);
            $componente['aporteEstatal'] = $this->obtenerAportacionEstatal($desarrollo, $componente['componentes_id']);
            $componente['total'] = $this->obtenerTotalComponente($desarrollo, $componente['componentes_id']);
            return $componente;
        });
    }

    protected function tomarComponentes($componentes, $vertiente = 1, $campo='modelos_id')
    {
        return $componentes->filter(function ($componente, $key) use ($vertiente, $campo) {
            return $componente[$campo]==$vertiente;
        });
    }
}
