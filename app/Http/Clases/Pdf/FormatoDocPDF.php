<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\Proyecto;
use Carbon\Carbon;

class FormatoDocPDF extends DocumentoDecorator
{
    protected $proyecto;

    public function __construct(Documento $doc, Proyecto $proyecto)
    {
        $this->setProyecto($proyecto);

        parent::__construct($doc);

        parent::agregarCSS($this->getFormatDoc(), 1);
    }

    public function setProyecto(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    }

    public function getFormatDoc()
    {
        if (in_array($this->proyecto->getEstatus(), [3, 5, 6, 11])===FALSE) {
            return public_path('css/project/preeliminarDoc.css');
        }

        if ($this->proyecto->getAnio() <= 2018) {
            return public_path('css/project/fondoDocV1.css');
        }

        if ($this->proyecto->getAnio() > 2018 && $this->proyecto->getAnio() < 2025) {
            return public_path('css/project/fondoDocV2.css');
        }

        return public_path('css/project/fondoDocV3.css');
    }
}
