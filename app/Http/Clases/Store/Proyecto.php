<?php

namespace App\Http\Clases\Store;

use App\Models\Project\{Proyecto AS ModelProyecto, Version as ModelVersion};
use App\Http\Clases\Validations\ValidaProyecto;
use App\Http\Traits\TraitVersionProyecto;

class Proyecto extends ValidaProyecto
{
    protected $proyecto;

    protected $version;

    protected $versionPrev;

    protected $desarrollo;

    use TraitVersionProyecto;

    public function __construct(array $datos)
    {
        parent::__construct($datos);
    }

    public function guardar()
    {
        if (count($campos = $this->prepararCampos())>0) {
            $this->setProyecto(ModelProyecto::updateOrCreate(['id_proyecto'=>$this->campos['id'] ?? 0], $campos));
        }

        if (isset($this->proyecto->id_proyecto) || isset($this->campos['id'])) {
            $this->setProyecto(ModelProyecto::with('versiones')->where('id_proyecto', isset($this->proyecto->id_proyecto) ? $this->proyecto->id_proyecto : $this->campos['id'])->first() ?? new ModelProyecto());
        }

        $this->crearVersion(isset($this->datos['presupuesto']) && is_numeric($this->datos['presupuesto']) ? $this->datos['presupuesto'] : 0, $this->datos);

        return $this->existsProyecto();
    }

    public function setProyecto(ModelProyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    }

    public function setVersionPrev(ModelVersion $version)
    {
        $this->versionPrev = $version;
    }

    public function getId()
    {
        return $this->proyecto->id_proyecto ?? 0;
    }

    public function getUltimaVersion()
    {
        return $this->version->version ?? 0;
    }

    protected function prepararCampos(): array
    {
        $datos = $this->getValidados();
        $campos = [];

        !isset($datos['id']) || $datos['id']==0    ? $campos['id_diagnostico']  = $datos['diagnosticoId'] : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['vertiente']       = auth()->user()->directorio->organizacion->vertiente_id : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['id_organizacion'] = auth()->user()->directorio->id_organizacion : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['fecha_firma']     = date('Y-m-d H:i:s') : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['nombre_firmante'] = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['rfc_firmante']    = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['xml']             = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['nombre_certificador'] = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['rfc_certificador']    = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['sello_digital']       = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['sello_certificacion'] = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['cadena_original']   = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['dictaminador']      = '' : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['id_firmante']     = 0 : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['id_certificador'] = 0 : null;
        !isset($datos['id']) || $datos['id']==0    ? $campos['creador']         = auth()->user()->usuarios_id : null;

        isset($datos['mEst'])   ? $campos['monto_est'] = $datos['mEst']      : null;
        isset($datos['mFed'])   ? $campos['monto_fed'] = $datos['mFed']      : null;
        isset($datos['mTotal']) ? $campos['monto_total'] = $datos['mTotal']  : null;
        isset($datos['millar']) ? $campos['millar'] = $datos['millar']       : null;

        return $campos;
    }

    protected function crearVersion($presupuesto=0, array $datos = [])
    {
        $this->version = $this->proyecto->versiones()->create([
            'version'=>is_numeric($this->proyecto->versiones->max('version')) ? $this->proyecto->versiones->max('version') + 1 : 1,
            'id_proyecto'=>$this->proyecto->id_proyecto,
            'id_creador'=> auth()->user()->usuarios_id,
            'presupuesto_estatal'=>$presupuesto,
        ]);
        #print_r($this->version->version);

        $this->setVersionPrev($this->obtenerVersionPrevia($this->version->version));

        $this->crearSeguimiento($datos['seguimiento'] ?? []);

        $this->crearDesarrollo($datos['desarrollo'] ?? []);
    }

    protected function crearSeguimiento(array $datos = [])
    {
        new Seguimiento($this->version->seguimiento(), $this->prepararCamposSeguimiento($datos));
    }

    protected function crearDesarrollo(array $desarrollo = [])
    {
        $agregados = [];
        foreach ($desarrollo as $componente) {
            $registro = new Desarrollo($this->version->desarrollo(), $componente);
            if ($registro->crear()) {
                $agregados[] = $componente['id'];
            }
        }

        $this->replicarDesarrollo(array_merge($agregados, $this->datos['rm'] ?? []));
    }

    protected function obtenerVersionPrevia(int $verActual = 1): ModelVersion
    {
        return $this->proyecto->versiones->where('version','<', $verActual)->sortByDesc('version')->first() ?? new ModelVersion();
    }

    protected function existsProyecto(): bool
    {
        return isset($this->proyecto->id_proyecto) && $this->proyecto->id_proyecto>0;
    }
}
