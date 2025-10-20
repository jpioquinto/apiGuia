<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\{Proyecto, Ejecucion};

class EjecucionPDF
{
    protected $imgCheck;

    protected $ejecucion;

    protected $meses;

    public function __construct(Proyecto $proyecto, $meses = [1,2,3,4,5,6,7,8,9,10,11,12], $imgCheck = "<img src='images/iconos/png/32x32/check.png' style='width:16px;height:16px;margin:0px auto;'>")
    {

        $this->imgCheck    = $imgCheck;

        $this->meses       = $meses;

        $this->ejecucion    = new Ejecucion(
                                $proyecto->getVersion()->desarrollo ?? collect([]),
                                $proyecto->diagnostico->getComponentes($proyecto->getVertiente()),
                                $proyecto->getIdDiagnostico(),
                                $proyecto->getVertiente(),
                                $proyecto->getCarpeta()
                            );

    }

    public function getImagen()
    {
        return $this->imgCheck;
    }

    public function vista()
    {
        return view(
                "reports.project.partial.programa_ejecucion",
                [
                    'meses'=>'',
                    'filas'=>$this->generarFilasComponente(),
                    'totalMeses'=>12
                ]
            );
    }

    protected function generaAtributoRowspan($actividades)
    {
        if (count($actividades) <= 1) {
            return '';
        }
        return "rowspan='" . count($actividades) . "'";
    }

    protected function generarColumnasMeses($idComponente, $idSubcomp, $programados, $vertiente)
    {
        $columnas = '';
        foreach ($this->meses as $mes) {
            $columnas .= sprintf(
                "<td style='width:20px;text-align:center!important;font-size:12px!important;'>%s</td>",
                in_array(str_pad($mes, 1, '0', STR_PAD_LEFT), $programados) ? $this->getImagen() : ''
            );
        }

        return $columnas;
    }

    protected function generaFilasActividad($actividades, $programa, $idComponente, $vertiente)
    {
        $inicial = true;
        $filas   = '';
        foreach ($actividades as $index => $actividad) {
            if (!isset($programa[$this->obtenerKey('subcomp_', $index)])) {
                $programa[$this->obtenerKey('subcomp_', $index)] = [];
            }

            $filas .=  $inicial ? '' : "<tr class='fila_ejecucion'>";

            $filas .= sprintf("<td>%s</td>", $actividad[0]['descSubcomp']);

            $filas .= $this->generarColumnasMeses(
                        $idComponente, $this->obtenerKey('subcomp_', $index), $programa[$this->obtenerKey('subcomp_', $index)], $vertiente
                    );

            $filas .= "</tr>";

            $inicial = false;
        }

        return $filas;
    }

    protected function generarFilasComponente()
    {
        $filas = '';
        foreach ($this->ejecucion->getPrograma() as $idComponente => $componente) {
            $filas .= sprintf("<tr><td %s>%s</td>", $this->generaAtributoRowspan($componente['actividades']), $componente['nombre']);
            $filas .= $this->generaFilasActividad($componente['actividades'], $componente['programa'], $idComponente, $componente['vertiente']);
        }
        return $filas;
    }

    protected function obtenerKey($buscar, $cadena)
    {
        return str_replace($buscar, '', $cadena);
    }
}
