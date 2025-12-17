<?php

namespace App\Http\Clases\Dashboard\Filters;

class Dashboard
{
    protected $filtro;

    public function __construct(Filtro $filtro)
    {
        $this->filtro = $filtro;
    }

    public function listarResultado(): array
    {
        return $this->filtro->listarResultado();
    }
}
