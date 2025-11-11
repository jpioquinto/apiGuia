<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\Proyecto;
use \Mpdf\Mpdf as PDF;

class DocPDF implements Documento
{
    public $shortVertiente;
    protected $proyecto;
    protected $mpdf;

    public function __construct(Proyecto $proyecto, $config = [])
    {
        $this->proyecto = $proyecto;
        $this->mpdf     = new PDF($config);

        if (isset($config['css']['source'])) {
            $this->aplicarEstilos($config['css']['source'], $config['css']['mode'] ?? 1);
        }
    }

    public function aplicarEstilos($css, int $mode = \Mpdf\HTMLParserMode::HEADER_CSS)
    {
        $this->agregarCSS($css, $mode);
    }

    public function agregarCSS($pathCSS, int $mode = \Mpdf\HTMLParserMode::HEADER_CSS)
    {
        $css = file_get_contents($pathCSS);
        $this->mpdf->WriteHTML($css, $mode);
    }

    public function encabezado($contenido = '')
    {
        $this->mpdf->SetHTMLHeader($contenido);
    }

    public function portada($contenido = '')
    {
        $this->escribir($contenido);
    }

    public function pie($contenido = '')
    {
        $this->mpdf->SetHTMLFooter($contenido);
    }

    public function escribir($contenido = '')
    {
        $this->mpdf->WriteHTML($contenido);
    }

    public function agregarPagina($seccion='')
    {
        $this->mpdf->AddPage();
    }

    public function obtenerNumPagina()
    {
        return $this->mpdf->PageNo();
    }

    public function salida()
    {
        return  $this->mpdf->OutputBinaryData();
    }
}
