<?php

namespace App\Http\Clases;

use App\Models\Diagnostic\Sigirc\Sigirc;
use App\Models\Diagnostic\Sigcm\Sigcm;

class Diagnostico
{
    protected $componente;

    protected $modelo;

    protected $me;

    public function __construct($id, $app = 1)
    {
        $this->setModelo($app == 1 ? new Sigirc : new Sigcm);

        $this->setDiagnostico( $this->consultarDiagnostico($id) );

        $this->componente = new Componente( $this->modelo::obtenerModeloComponentes() );
    }

    public function getAnio()
    {
        return $this->me->ano_proyecto;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function setComponente($componente)
    {
        $this->componente = $componente;
    }

    public function setDiagnostico($diagnostico)
    {
        $this->me = $diagnostico;
    }

    public function getCarpeta()
    {
        return $this->me->organizacion->carpeta_documentos ?? '';
    }

    public function getComponentes($vertiente = 1)
    {
        return $this->componente->getComponentes($vertiente);
    }

    protected function consultarDiagnostico($idDiagnostico)
    {
        return $this->modelo::with(['detalles','detalles.tabla', 'organizacion'])
            ->where('diagnosticos_id',$idDiagnostico)
            ->first();
    }
}
