<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\Proyecto;
use Carbon\Carbon;

class PortadaDocPDF extends DocumentoDecorator
{
    protected $proyecto;

    protected $vertientes;

    public function __construct(Documento $doc, Proyecto $proyecto)
    {
        $this->vertientes = ['1'=>'Catastral', '2'=>'Registral', '1,2'=>'Integral'];

        $this->proyecto = $proyecto;

        parent::__construct($doc);

        Carbon::setLocale('es');

        $this->setPortada($this->config());

        parent::agregarPagina();
    }

    protected function config()
    {
        return [
            'fondo'=>$this->proyecto->getAnio() < 2019 ? '#ba0c2f' : '#9D3349',
            'unidadSEDATU'=>$this->proyecto->getAnio() <= 2019 ? 'COORDINACIÓN GENERAL DE MODERNIZACIÓN Y VINCULACIÓN <br>REGISTRAL Y CATASTRAL' : 'DIRECCIÓN GENERAL DE INVENTARIOS Y MODERNIZACIÓN<br>REGISTRAL Y CATASTRAL',
            'descProyecto'=>"Proyecto Ejecutivo ".($this->vertientes[$this->proyecto->getVertiente()] ?: ''),
            'entidad'=>$this->proyecto->organizacion->getEstado().' '.$this->proyecto->getAnio(),
            'imgPortada'=>"images/portada/{$this->proyecto->organizacion->getEstadoISO()}.jpg",
            'creacion'=>$this->describirFecha($this->proyecto->getFechaCreacion()),
            'ultimaModificacion'=>$this->describirFecha($this->proyecto->getFechaUltimaModifcacion()),
            'emision'=>$this->describirFecha(date('Y-m-d')),
            'version'=>$this->proyecto->getUltimaVersion() . (!$this->proyecto->estaFirmado() ? " ({$this->proyecto->getDescripcionEstatus()})" : ''),
        ];
    }

    protected function setPortada($datos = [])
    {
        return parent::escribir(view("reports/project/portada", $datos));
    }

    protected function describirFecha($fecha)
    {
        return Carbon::parse($fecha)->translatedFormat('d \d\e F \d\e\l Y');

    }
}
