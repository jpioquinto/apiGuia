<?php

namespace App\Http\Clases\Store;

use Illuminate\Database\Eloquent\Relations\Relation;


use App\Http\Clases\Validations\ValidaObjetivo;

use App\Models\Project\Objetivo AS ModelObjetivo;

class Objetivo extends ValidaObjetivo
{
    protected $modeloObjetivo;

    public function __construct(Relation $modeloObjetivo, array $objetivo = [])
    {
        $this->modeloObjetivo = $modeloObjetivo;

        parent::__construct($objetivo);

        $this->crear(parent::getValidados());
    }

    public function crear(array $objetivo)
    {
        $campos = [];

        isset($objetivo['indice'])   ? $campos['indice'] = $objetivo['indice']       : null;
        isset($objetivo['objetivo']) ? $campos['objetivo_especifico'] = $objetivo['objetivo'] : null;
        isset($objetivo['alcance'])  ? $campos['alcance'] = $objetivo['alcance'] : null;

        count($campos)>0 ? $this->modeloObjetivo->create($campos) : null;
    }

}
