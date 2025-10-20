<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\Proyecto;

class EncabezadoDocPDF extends DocumentoDecorator
{
    protected $anio;

    public function __construct(Documento $doc, int $anio=0)
    {
        parent::__construct($doc);

        $this->anio = $anio;

        $this->encabezado( $this->vista() );
    }

    public function encabezado($vista = 'header4T')
    {
        return parent::encabezado(view("reports/project/{$vista}/header"));
    }

    protected function vista()
    {
        if ($this->anio<2019) {
            return 'header3T';
        }

        return 'header4T';
    }

}
