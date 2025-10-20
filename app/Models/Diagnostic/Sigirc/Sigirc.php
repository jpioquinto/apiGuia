<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;
use App\Models\Diagnostic\Diagnostico;

class Sigirc extends Model implements Diagnostico
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_diagnosticos';
    protected $primaryKey = 'diagnosticos_id';

    public static function obtenerDiagnostico($idDiagnostico)
    {
        return Sigirc::where('diagnosticos_id', $idDiagnostico);
    }
    public static function obtenerDiagnosticoActual($idOrganizacion)
    {
        return Sigirc::where('status_id', 13)
                       ->where('id_organizacion', $idOrganizacion)
                       ->max('diagnosticos_id');
    }
    public static function obtenerModeloComponentes()
    {
        return new Componente;
    }
    public function detalles()
    {
        return $this->hasMany(Detalle::class, 'diagnosticos_id', 'diagnosticos_id');
    }
    public function organizacion()
    {
        return $this->hasOne(Organizacion::class, 'id_organizacion', 'id_organizacion');
    }
}
