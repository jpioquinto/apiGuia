<?php

namespace App\Http\Clases\Store;

use App\Http\Clases\Validations\ValidaSeguimiento;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Project\Seguimiento AS ModelSeguimiento;

class Seguimiento extends ValidaSeguimiento
{
    protected $modeloSeguimiento;

    public function __construct(Relation $modeloSeguimiento, array $datos = [])
    {
        $this->modeloSeguimiento = $modeloSeguimiento;

        parent::__construct($datos);

        $this->crear(parent::getValidados());
    }

    public function crear(array $datos)
    {
        $campos = [];

        isset($datos['intro'])      ? $campos['introduccion'] = $datos['intro']       : null;
        isset($datos['situacion'])  ? $campos['situacion_gral'] = $datos['situacion'] : null;
        isset($datos['logros'])     ? $campos['logros_aplicacion'] = $datos['logros'] : null;
        isset($datos['objetivo'])   ? $campos['objetivo_gral'] = $datos['objetivo']   : null;
        isset($datos['metas'])      ? $campos['metas_globales'] = $datos['metas']     : null;
        $campos['observaciones_resumen'] = $datos['obsResumen'] ?? '';

        return count($campos)>0 ? $this->modeloSeguimiento->create($campos) : null;
    }
}
