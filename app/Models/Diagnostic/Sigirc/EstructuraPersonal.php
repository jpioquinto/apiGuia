<?php

namespace App\Models\Diagnostic\Sigirc;

class EstructuraPersonal
{
    public $categoria = '';
    private $perfiles = [
        1=>'administracion',
        2=>'comunicacion',
        3=>'contabilidad',
        4=>'derecho',
        5=>'ingenieria',
        6=>'logistica',
        7=>'mercadotecnia',
        8=>'recursos',
        9=>'tecnologias'
    ];
    public function __construct($categoria)
    {
        $this->categoria = $categoria;
        $this->inicializaPropiedades();
    }
    public function inicializaPropiedades()
    {
        foreach ($this->perfiles as $perfil) {
            $this->$perfil = 0;
        }
    }
    public function actualizaDatos($registro)
    {
        if (is_numeric($registro['perfil']) && isset($this->perfiles[$registro['perfil']])) {
            $this->{$this->perfiles[$registro['perfil']]} += is_numeric($registro['cantidad']) ? $registro['cantidad'] : 0;
        }

    }

}
