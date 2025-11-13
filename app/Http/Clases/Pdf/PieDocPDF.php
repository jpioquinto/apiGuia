<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\Proyecto;

class PieDocPDF extends DocumentoDecorator
{
    protected $proyecto;

    public function __construct(Documento $doc, Proyecto $proyecto)
    {
        parent::__construct($doc);

        $this->proyecto = $proyecto;

        $this->pie($this->vista());
    }

    public function pie($strHtml = 'www.gob.mx/sedatu')
    {
        return parent::pie($strHtml);
    }

    public function vista()
    {
        return view(
            'reports/project/partial/pie',
            [
                'codigo'=>$this->generaCodigo(),
                'imgQr'=>!$this->proyecto->estaFirmado() ? str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', \QrCode::size(35)->generate('Guía de Proyectos')) : null
            ]
        );
    }

    protected function generaCodigo()
    {
        return 'GP-'.str_replace('MX-', '', $this->proyecto->organizacion->getEstadoISO()).'-'.($this->shortVertiente[$this->proyecto->getVertiente()] ?? ' ').'-'.$this->proyecto->getUltimaVersion().'-'.$this->proyecto->getAnio();
    }
}
