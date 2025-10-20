<?php

namespace App\Models\Diagnostic\Sigirc;

class ConcentradoPersonal
{
    public $categoria = '';
    public $confianza;
    public $honorarios;
    public $cantidad;

    private $tipoTrabajador = [
        1=>'confianza',
        2=>'honorarios'
    ];

    public function __construct($categoria)
    {
        $this->categoria   = $categoria;
        $this->confianza   = 0;
        $this->honorarios  = 0;
        $this->cantidad = 0;
    }
    public function actualizarDatos($registro)
    {
        if (is_numeric($registro['tipo']) && isset($this->tipoTrabajador[$registro['tipo']])) {
            $this->cantidad   += is_numeric($registro['cantidad']) ? $registro['cantidad'] : 0;
            $this->{$this->tipoTrabajador[$registro['tipo']]} += is_numeric($registro['cantidad']) ? $registro['cantidad'] : 0;
        }
    }

}
