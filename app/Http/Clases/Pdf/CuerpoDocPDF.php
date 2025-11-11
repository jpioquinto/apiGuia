<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\{Proyecto, Antecedente, SituacionActual};

class CuerpoDocPDF extends DocumentoDecorator
{
    protected $proyecto;

    protected $sizeFont;

    protected $desarrollo;

    protected $indice;

    protected $indiceCreado;

    protected $subIndice;


    public function __construct(Documento $doc, Proyecto $proyecto, $indice = [])
    {
        $this->proyecto     = $proyecto;

        $this->indice       = $indice;

        $this->indiceCreado = false;

        parent::__construct($doc);

        $this->setSizeFont($proyecto->getAnio()>=2018 ? 'font-size:11pt !important;' : '');

        $this->generarCuerpoDoc();
    }

    public function existeIndice()
    {
        return $this->indiceCreado;
    }

    public function obtenerIndice()
    {
        return $this->indice;
    }

    public function generarCuerpoDoc()
    {
        $this->seccionIndice();

        $this->seccionIntroduccion();

        $this->seccionAntecedente();

        $this->seccionSituacion();

        $this->seccionObjetivo();

        $this->seccionDesarrollo();

        $this->seccionEjecucion();

        $this->seccionResumen();

        $this->seccionResultado();

        $this->seccionAnexo();

        $this->seccionFirma();
    }

    public function setSizeFont($sizeFont = '')
    {
        $this->sizeFont = $sizeFont;
    }

    public function getSizeFont()
    {
        return $this->sizeFont;
    }

    public function seccionIndice()
    {
        $indice = new IndicePDF($this->indice);

        parent::escribir("
            <h2>INDICE</h2>
            {$indice->vista()}
        ");

        $this->indiceCreado = count($this->indice)>0;

        parent::agregarPagina();
    }

    public function seccionIntroduccion($seccion = '1.- Introducción')
    {
        parent::escribir("
            <h2>".mb_strtoupper($seccion)."</h2>
            <div class='contenido' style='{$this->getSizeFont()}'>{$this->proyecto->seguimiento->getIntroduccion()}</div>
        ");

        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];
        parent::agregarPagina();
    }

    public function seccionAntecedente($seccion = '2.- Antecedentes')
    {

        parent::escribir("<h2>".mb_strtoupper($seccion)."</h2>");

        $indice = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion, 'hijos'=>[['num'=>parent::obtenerNumPagina(), 'seccion'=>'2.1 Situación General', 'hijos'=>[]]]];

        parent::escribir("
            <h3 class='subtitulo'>2.1 Situación General</h3>
            <div class='contenido' style='{$this->getSizeFont()}'><p>{$this->proyecto->seguimiento->getSituacion()}</p></div>
        ");

        parent::agregarPagina();

        $indice['hijos'][0]['hijos'] = $this->antecedenteVertiente();

        $indice['hijos'][] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>'2.2 Logros de la aplicación del Programa de Modernización'];

        parent::escribir("
            <h3 class='subtitulo'>2.2 Logros de la aplicación del Programa de Modernización</h3>
            <div class='contenido' style='{$this->getSizeFont()}'><p>{$this->proyecto->seguimiento->getLogro()}</p></div>
        ");

        $this->indice[] = $indice;

        parent::agregarPagina();
    }

    public function antecedenteVertiente()
    {
        $antecedente = new Antecedente($this->proyecto->getModelo());

        if (strcmp($this->proyecto->getVertiente(), '1,2') == 0) {

            $indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>'2.1.1 Catastro'];

            parent::escribir("
                <h4>2.1.1 Catastro</h4>
                {$antecedente->vistaAntecedenteCatastral()}
            ");

            parent::agregarPagina();

            $indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>'2.1.2 Registro público de la propiedad'];

            parent::escribir("
                <h4>2.1.2 Registro público de la propiedad</h4>
                {$antecedente->vistaAntecedenteRegistral(2)}
            ");

            return $indice;
        }

        parent::escribir($antecedente->vista());

        return [];
    }

    public function seccionSituacion($seccion = '3.- Situación actual')
    {
        $situacion = new SituacionActual($this->proyecto);

        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];

        parent::escribir("
            <h2>".mb_strtoupper($seccion)."</h2>
            {$situacion->vista()}
        ");

        parent::agregarPagina();
    }

    public function seccionObjetivo($seccion = '4.- Objetivos')
    {
        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];

        parent::escribir("
            <h2>".mb_strtoupper($seccion)."</h2>
            <div class='contenido' style='{$this->getSizeFont()}'>{$this->proyecto->seguimiento->getObjetivo()}</div>
        ");

        parent::agregarPagina();
    }

    public function seccionDesarrollo($seccion = '5.- Desarrollo del proyecto')
    {
        $indice = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion, 'hijos'=>[]];

        $this->desarrollo = new DesarrolloPDF($this->proyecto);

        parent::escribir("<h2>".mb_strtoupper($seccion)."</h2>");

        $this->desarrollo->generarDesarrollo($this);

        $indice['hijos'] = $this->desarrollo->getIndice();

        $this->indice[] = $indice;

        parent::agregarPagina();
    }

    public function seccionEjecucion($seccion = '6.- Programa de ejecución')
    {
        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];

        $ejecucion = new EjecucionPDF($this->proyecto);

        parent::escribir("
            <h2>".mb_strtoupper($seccion)."</h2>
            {$ejecucion->vista()}
        ");

        parent::agregarPagina();
    }

    public function seccionResumen($seccion = '7.- Resumen financiero')
    {
        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];

        $resumen = new ResumenPDF($this->proyecto);

        parent::escribir("
            <h2>".mb_strtoupper($seccion)."</h2>
            {$resumen->vista()}
        ");

        parent::agregarPagina();
    }

    public function seccionResultado($seccion = '8.- Resultados esperados')
    {
        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];

        parent::escribir("
            <h2>".mb_strtoupper($seccion)."</h2>
            <div class='contenido' style='{$this->getSizeFont()}'>{$this->proyecto->seguimiento->getMeta()}</div>
        ");

        parent::agregarPagina();
    }

    public function seccionAnexo($seccion = '9.- Anexos')
    {
        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];

        $anexo = new AnexoPDF($this->desarrollo->desarrollo);

        parent::escribir("
            <h2>".mb_strtoupper($seccion)."</h2>
            <div style='{$this->getSizeFont()}'>{$anexo->vista()}</div>
        ");

        parent::agregarPagina();
    }

    public function seccionFirma($seccion = '10.- Firma del documento')
    {
        $this->indice[] = ['num'=>parent::obtenerNumPagina(), 'seccion'=>$seccion];

        $firma = new FirmaPDF($this->proyecto);

        parent::escribir(
            sprintf("<h2>%s</h2>", $this->proyecto->getAnio()<=2024 ? $seccion : mb_strtoupper($seccion)).
            #"<div style='{$this->getSizeFont()}'>{$firma->vista()}</div>"
            ""
        );
    }
}
