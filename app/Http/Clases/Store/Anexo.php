<?php

namespace App\Http\Clases\Store;

use Illuminate\Database\Eloquent\Relations\Relation;
use App\Http\Clases\Validations\ValidaAnexo;

use App\Models\Project\Anexo AS ModelAnexo;

class Anexo extends ValidaAnexo
{
    protected $modeloAnexo;

    public function __construct(Relation $modeloAnexo, array $anexo = [])
    {
        $this->modeloAnexo = $modeloAnexo;

        parent::__construct($anexo);

        $this->crear(parent::getValidados());
    }

    public function crear(array $anexo)
    {
        $campos = [
            'nombre'=>$anexo['nombre'],
            'nombre_anterior'=>$anexo['nombre_anterior'],
            'descripcion'=>$anexo['descripcion'],
        ];

        isset($anexo['ext'])  ? $campos['ext'] = $anexo['ext'] : null;

        $this->modeloAnexo->create($campos);
    }
}
