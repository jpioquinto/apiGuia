<?php

namespace App\Http\Clases\Store;

use App\Http\Clases\Validations\ValidaPrograma;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Project\Programa AS ModelPrograma;

class Programa extends ValidaPrograma
{
    protected $modeloPrograma;

    public function __construct(Relation $modeloPrograma, array $programa = [])
    {
        $this->modeloPrograma = $modeloPrograma;

        parent::__construct($programa);

        $this->crear(parent::getValidados());
    }

    public function crear(array $programa)
    {
        $campos = [];

        isset($programa['indice']) ? $campos['indice'] = $programa['indice']  : null;
        isset($programa['meses'])  ? $campos['meses']  = $programa['meses']   : null;

        count($campos)>0 ? $this->modeloPrograma->create($campos) : null;
    }

}
