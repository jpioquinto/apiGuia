<?php
namespace App\Http\Clases\Pdf;

class IndicePDF
{
    protected $indice;

    protected $totalSecciones;

    public function __construct($indice = [])
    {
        $this->indice = $indice;

        $this->setTotalSecciones($this->obtenerTotalSecciones($indice));
    }

    public function setTotalSecciones($total)
    {
        $this->totalSecciones = $total;
    }

    public function getTotalSecciones()
    {
        return $this->totalSecciones;
    }

    public function vista()
    {
        $lineas = $this->getTotalSecciones() - 33;
        $pag    = ($lineas>0 ? 1 : 0) + intval($lineas / 40);

        $filas = $this->generarFilas($this->indice, $pag);

        return trim($filas) != '' ? view('reports.project.partial.tabla_indice', ['filas'=>$filas]) : '';
    }

    protected function generarFilas($indice, $inicia, $nivel = 1)
    {
        $filas = '';
        foreach ($indice as $elemento) {
            $elemento['num'] += $inicia;
            $filas .= view(
                'reports.project.partial.fila_indice',
                array_merge($elemento, ['nivel'=>$nivel])
            );
            $filas = trim($filas);
            if (isset($elemento['hijos'])) {
                $filas .= $this->generarFilas($elemento['hijos'], $inicia, $nivel + 1);
            }
        }

        return $filas;
    }

    protected function obtenerTotalSecciones($elementos)
    {
        $total = 0;

        foreach ($elementos as $value) {
            $total++;

            $total += isset($value['hijos']) ? $this->obtenerTotalSecciones($value['hijos']) : 0;
        }

        return $total;
    }
}
