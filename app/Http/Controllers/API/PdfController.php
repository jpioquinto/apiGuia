<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project\Proyecto;
use Illuminate\Http\Request;

use App\Http\Clases\Proyecto AS CProyecto;
use App\Http\Clases\Pdf\{DocPDF, EncabezadoDocPDF, PieDocPDF, PortadaDocPDF, CuerpoDocPDF, FormatoDocPDF};

use \Mpdf\Mpdf as PDF;

class PdfController extends Controller
{
    public function index(Proyecto $proyecto)
    {
        return $this->crearPDF($proyecto);
    }

    protected function crearPDF(Proyecto $proyecto, $indice = [])
    {
        $mpdf = new DocPDF(
            new CProyecto($proyecto),
            [
                'format'=>'A4-L',
                'margin_left'=>20,
                'margin_right'=>20,
                'margin_top'=>34,
                'margin_bottom'=>15,
                'margin_header'=>10,
                'margin_footer'=>7,
                'css'=>['source' => public_path('css/project/configDoc.css'), 'mode' => 1],
            ]
        );

        $mpdf = new EncabezadoDocPDF($mpdf, $proyecto->anio);

        $mpdf = new PieDocPDF($mpdf, new CProyecto($proyecto));

        $mpdf = new PortadaDocPDF($mpdf, new CProyecto($proyecto));

        $mpdf = new FormatoDocPDF($mpdf, new CProyecto($proyecto));

        $mpdf = new CuerpoDocPDF($mpdf, new CProyecto($proyecto), $indice);

        return !$mpdf->existeIndice() ? $this->crearPDF($proyecto, $mpdf->obtenerIndice()) : $mpdf->salida();
    }
}
