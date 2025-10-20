<?php

namespace App\Models\Diagnostic;

interface Diagnostico
{
    public static function obtenerDiagnosticoActual($idOrganizacion);
    public static function obtenerDiagnostico($idDiagnostico);
    public static function obtenerModeloComponentes();
}
