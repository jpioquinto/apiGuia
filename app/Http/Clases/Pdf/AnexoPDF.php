<?php
namespace App\Http\Clases\Pdf;

use App\Http\Clases\Desarrollo;

class AnexoPDF
{
    protected $desarrollo;

    protected $numFila;

    public function __construct(Desarrollo $desarrollo)
    {
        $this->desarrollo = $desarrollo;

        $this->numFila = 0;
    }

    public function vista()
    {
        return view(
            "reports.project.partial.tabla_anexos",
            ['filas'=> $this->iterarComponentes($this->desarrollo->obtenerComponentes())]
        );
    }

    protected function iterarComponentes($componentes)
    {
        $filas = '';
        $componentes->each(function ($componente, $index) use (&$filas) {
            $filas .= $this->iterarActividades($componente['actividades']);
        });

        return $filas;
    }

    protected function iterarActividades($activiidades)
    {
        $filas = '';
        $activiidades->each(function ($activiidad, $index) use (&$filas) {
            $filas .= $this->generarFilaAnexo($activiidad['anexos']);
        });

        return $filas;
    }

    protected function generarFilaAnexo($anexos)
    {
        $filas = '';
        $anexos->each(function ($anexo, $index) use (&$filas) {
            $filas .= view(
                "reports.project.partial.fila_anexo",
                [
                    'nombre_anterior'=>$anexo->nombre_anterior,
                    'descripcion'=>$anexo->descripcion,
                    'numFila'=>++$this->numFila,
                    'url'=>$anexo->url ?? ''
                ]
            );
        });

        return $filas;
    }
}
