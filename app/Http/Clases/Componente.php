<?php

namespace App\Http\Clases;

use App\Models\Diagnostic\Sigirc\Sigirc;
use App\Models\Diagnostic\Sigcm\Sigcm;

class Componente
{
    protected $modelo;

    public function __construct($modelo)
    {
        $this->setModelo($modelo);
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function getComponentes($vertiente = 1)
    {
        return $this->modelo->query()
        ->select(['componentes_id','modelos_id','nombre','nombre_corto','orden'])
        ->whereIn('modelos_id', explode(',', $vertiente))
        ->whereNotIn('componentes_id', [17,18])
        ->orderBy('orden','ASC')
        ->get();
    }
}
