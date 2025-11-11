<?php

namespace App\Http\Clases\Pdf;

class DocumentoDecorator implements Documento
{
    protected $shortVertiente;

    protected $doc;

    public function __construct(Documento $doc)
    {
        $this->shortVertiente = ['1'=>'PEMC', '2'=>'PEMR', '1,2'=>'PEMI'];
        $this->doc = $doc;
    }

    public function agregarCSS($pathCSS, int $mode = \Mpdf\HTMLParserMode::HEADER_CSS)
    {
        return $this->doc->agregarCSS($pathCSS, $mode);
    }

    public function encabezado($contenido = '')
    {
        return $this->doc->encabezado($contenido);
    }

    public function portada($contenido = '')
    {
        return $this->doc->portada($contenido);
    }

    public function pie($contenido = '')
    {
        return $this->doc->pie($contenido);
    }

    public function escribir($contenido = '')
    {
        return $this->doc->escribir($contenido);
    }

    public function agregarPagina($seccion = '')
    {
        return  $this->doc->agregarPagina($seccion);
    }

    public function obtenerNumPagina()
    {
        return $this->doc->obtenerNumPagina();
    }

    public function salida()
    {
        return  $this->doc->salida();
    }
}
