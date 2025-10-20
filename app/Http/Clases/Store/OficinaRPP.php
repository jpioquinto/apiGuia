<?php

namespace App\Http\Clases\Store;

use App\Http\Clases\Validations\ValidaOficinaRPP;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Project\Oficina AS ModelOficina;

class OficinaRPP extends ValidaOficinaRPP
{
    protected $modeloOficina;

    public function __construct(Relation $modeloOficina, array $oficina)
    {
        $this->modeloOficina = $modeloOficina;

        parent::__construct($oficina);

        $this->crear(parent::getValidados());
    }

    public function crear(array $oficina)
    {
        $campos = [
            'oficina'=>$oficina['oficina'],
            'concepto'=>$oficina['concepto'],
            'acervo_existe'=>$oficina['acervo_existe'],
            'acervo_digitalizado'=>$oficina['acervo_digitalizado'],
            'porc_digitalizado'=>$oficina['porc_digitalizado'],
            'libros_legajos'=>$oficina['libros_legajos'],
            'num_imagenes'=>$oficina['num_imagenes'],
        ];

       $this->modeloOficina->create($campos);
    }
}
