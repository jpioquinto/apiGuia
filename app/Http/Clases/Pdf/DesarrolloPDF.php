<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\Pdf\DocumentoDecorator;
use App\Http\Clases\{Proyecto, Desarrollo};

class DesarrolloPDF
{
    protected $proyecto;

    public $desarrollo;

    protected $seccion;

    protected $fontSize;

    protected $docPdf;

    protected $indice;

    public function __construct(Proyecto $proyecto, $fontSize = '', $seccion = 5)
    {
        $this->proyecto = $proyecto;

        $this->seccion  = $seccion;

        $this->fontSize = $fontSize;

        $this->indice   = [];

        $this->desarrollo = new Desarrollo(
                                    $proyecto->getVersion()->desarrollo ?? collect([]),
                                    $proyecto->diagnostico->getComponentes($proyecto->getVertiente()),
                                    $proyecto->getIdDiagnostico(),
                                    $proyecto->getCarpeta()
                            );

    }

    public function getIndice()
    {
        return $this->indice;
    }

    public function generarDesarrollo(DocumentoDecorator $docPdf = null)
    {

        $this->docPdf = $docPdf;

        return $this->vistaComponente();
    }

    public function vistaComponente()
    {
        $vista = '';
        $this->desarrollo->obtenerComponentes()->each(function ($componente, $index) use (&$vista) {

            $this->proyecto->esIntegral() && in_array($componente['id'], [1,3,6,7]) ? $componente['nombre'] .= ' Catastral' : null;
            $this->proyecto->esIntegral() && in_array($componente['id'], [8,10,11,13]) ? $componente['nombre'] .= ' Registral' : null;

            $index > 0 ? $this->docPdf->agregarPagina() : null;

            $indice = ['num'=>$this->docPdf->obtenerNumPagina(), 'seccion'=>$this->seccion .'.'. $componente['orden'] . ' ' . $componente['nombre'], 'hijos'=>[]];

            $this->docPdf->escribir("<h3>{$this->seccion}.{$componente['orden']} Componente: {$componente['nombre']}</h3>");

            $indice['hijos'][] = ['num'=>$this->docPdf->obtenerNumPagina(), 'seccion'=>$this->seccion .'.'. $componente['orden'] . '.1 Situación actual'];

            $this->docPdf->escribir("<h4>{$indice['hijos'][0]['seccion']}</h4>");

            $this->docPdf->escribir("<div class='text' style='{$this->fontSize}'>{$componente['situacion']}</div>");

            $indice['hijos'][] = ['num'=>$this->docPdf->obtenerNumPagina(), 'seccion'=>$this->seccion .'.'. $componente['orden'] . '.2 Objetivos y alcances'];

            $this->docPdf->escribir("<h4>{$indice['hijos'][1]['seccion']}</h4>");

            $this->docPdf->escribir($this->generarVistaObjetivos($componente['objetivos'], $componente['orden']));

            $indice['hijos'][] = ['num'=>$this->docPdf->obtenerNumPagina(), 'seccion'=>$this->seccion .'.'. $componente['orden'] . '.3 Actividades a realizar en '.$this->proyecto->getAnio()];

            $this->docPdf->escribir("<h4>{$indice['hijos'][2]['seccion']}</h4>");

            $this->docPdf->escribir(
                view(
                    'reports.project.partial.actividades',
                    [
                        'filas'=>$this->generarVistaActividades($componente['actividades']),
                        'total'=>number_format($componente['total'], 2, '.', ',')
                    ]
                )
            );

            isset($componente['acervo']['oficinas'])
            ? $this->docPdf->escribir( $this->generarVistaOficinasRPP($componente['acervo']['oficinas']) ) : null;

            $indice['hijos'][] = ['num'=>$this->docPdf->obtenerNumPagina(), 'seccion'=>$this->seccion .'.'. $componente['orden'] . '.4 Estrategia de desarrollo'];

            $this->docPdf->escribir("<h4>{$indice['hijos'][3]['seccion']}</h4>");

            $this->docPdf->escribir("<div class='text' style='{$this->fontSize}'>{$componente['estrategia']}</div>");

            $this->indice[] = $indice;
            /*$vista .= view(
                "reports.project.component.componente",
                array_merge(
                    $componente,
                    [
                        'indice'=>$this->seccion,
                        'fontSize'=>$this->fontSize,
                        'anio'=>$this->proyecto->getAnio(),
                        'vistaTablaActividades'=>view(
                            'reports.project.partial.actividades',
                            [
                                'filas'=>$this->generarVistaActividades($componente['actividades']),
                                'total'=>number_format($componente['total'], 2, '.', ',')
                            ]
                        ),
                        'vistaOficinasRPP'=>isset($componente['acervo']['oficinas']) ? $this->generarVistaOficinasRPP($componente['acervo']['oficinas']) : null,
                    ]
                )
            );*/
        });

        return $vista;
    }

    public function generarVistaObjetivos($objetivos, $orden)
    {
        $vista = '';
        $objetivos->each(function ($objetivo, $index) use (&$vista, $orden) {
            $vista .= "<h5>{$this->seccion}.{$orden}.2.{$objetivo['orden']} {$objetivo['objetivo']}</h5>";
            $vista .= $objetivo['alcance'];
        });

        return $vista;
    }

    public function generarVistaActividades($actividades)
    {
        $vista = '';
        $actividades->each(function ($actividad, $index) use (&$vista) {
            if (!empty($actividad['descSubAct'])) {
                $actividad['descAct'] .= ' / ' . $actividad['descSubAct'];
            }
            $actividad['cantidad'] = number_format($actividad['cantidad']);
            $actividad['costo'] = number_format($actividad['costo'], 2, '.', ',');
            $actividad['iva'] = number_format($actividad['iva'], 2, '.', ',');
            $actividad['total'] = number_format($actividad['total'], 2, '.', ',');
            $vista .= view(
                "reports.project.partial.fila_actividad",
                $actividad
            );
        });

        return $vista;
    }

    protected function generarVistaOficinasRPP($oficinas)
    {
        $vista = '';

        foreach ($oficinas as $oficina) {
            $vista .= view(
                "reports.project.partial.oficinarpp",
                $oficina
            );
        };

        return $vista;
    }
}
