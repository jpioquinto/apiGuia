<?php

namespace App\Http\Clases\Pdf;

interface Documento
{
    public function encabezado($contenido);
    public function portada($contenido);
    public function pie($contenido);

    public function escribir($contenido);
    public function agregarPagina($seccion='');
    public function obtenerNumPagina();
    #public function agregarAindice($seccion);
    #public function obtenerIndice();
    public function salida();
}
