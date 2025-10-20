<?php

namespace App\Http\Clases\Store;

use App\Http\Clases\Validations\SolicitudProyecto;

use App\Models\Project\Proyecto AS ModelProyecto;

class Proyecto extends SolicitudProyecto
{
    protected $proyecto;

    protected $version;

    protected $desarrollo;

    public function __construct(array $datos)
    {
        !isset($datos['id'])    ? $campos['id_diagnostico']  = $datos['diagnosticoId'] : null;
        !isset($datos['id'])    ? $campos['vertiente']       = auth()->user()->directorio->organizacion->vertiente_id : null;
        !isset($datos['id'])    ? $campos['id_organizacion'] = auth()->user()->directorio->id_organizacion : null;
        !isset($datos['id'])    ? $campos['creador']         = auth()->user()->usuarios_id : null;

        isset($datos['mEst'])   ? $campos['monto_est'] = $datos['mEst']      : null;
        isset($datos['mFed'])   ? $campos['monto_fed'] = $datos['mFed']      : null;
        isset($datos['mTotal']) ? $campos['monto_total'] = $datos['mTotal']  : null;
        isset($datos['millar']) ? $campos['millar'] = $datos['millar']       : null;

        parent::__construct($datos);

        $this->setProyecto(ModelProyecto::updateOrCreate(['id_proyecto'=>$datos['id'] ?? 0], $campos));

        $this->crearVersion(isset($datos['presupuesto']) && is_numeric($datos['presupuesto']) ? $datos['presupuesto'] : 0, $datos);
    }

    public function setProyecto(ModelProyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    }

    public function getId()
    {
        return $this->proyecto->id_proyecto ?? 0;
    }

    public function getUltimaVersion()
    {
        return $this->version->version ?? 0;
    }

    protected function crearVersion($presupuesto=0, array $datos = [])
    {
        $this->version = $this->proyecto->versiones()->create([
            'version'=>is_numeric($this->proyecto->versiones->max('version')) ? $this->proyecto->versiones->max('version') + 1 : 1,
            'id_proyecto'=>$this->proyecto->id_proyecto,
            'id_creador'=> auth()->user()->usuarios_id,
            'presupuesto_estatal'=>$presupuesto,
        ]);

        $this->crearSeguimiento($datos['seguimiento'] ?? []);

        $this->crearDesarrollo($datos['desarrollo'] ?? []);
    }

    protected function crearSeguimiento(array $datos = [])
    {
        new Seguimiento($this->version->seguimiento(), $datos);
    }

    protected function crearDesarrollo(array $desarrollo = [])
    {
        foreach ($desarrollo as $componente) {
            new Desarrollo($this->version->desarrollo(), $componente);
        }
    }
}
