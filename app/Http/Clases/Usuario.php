<?php

namespace App\Http\Clases;

use App\Models\{User, UserSIGCM};

class Usuario
{
    protected $registro;

    protected $modelo;

    public function __construct($id, $app = 1)
    {
        $this->setModelo($app == 1 ? new User() : new UserSIGCM());

        $this->setRegistro($this->consultar($id));
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function setRegistro($registro)
    {
        $this->registro = $registro;
    }

    public function getNombreCompleto()
    {
        return $this->registro->directorio->nombre_completo;
    }

    public function getInstitucion()
    {
        return $this->registro->directorio->organizacion->nombre_organizacion;
    }

    public function getCargo()
    {
        return $this->registro->directorio->cargo_actual;
    }

    protected function consultar($id)
    {
        return $this->modelo::with('directorio.organizacion')->where('usuarios_id', $id)->first();
    }
}
