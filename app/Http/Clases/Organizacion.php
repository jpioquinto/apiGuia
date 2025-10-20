<?php

namespace App\Http\Clases;

class Organizacion
{
    protected $modelo;

    protected $me;

    public function __construct($modelo, $id)
    {
        $this->setModelo($modelo);

        $this->setOrganizacion($this->consultar($id));
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function setOrganizacion($organizacion)
    {
        $this->me = $organizacion;
    }

    public function getId()
    {
        return $this->me->id_organizacion ?? 0;
    }

    public function getNombre()
    {
        return $this->me->nombre_organizacion ?? '';
    }

    public function getEstado()
    {
        return $this->me->estado->estado;
    }

    public function getEstadoISO()
    {
        return $this->me->estado->estado_iso;
    }

    public function getCarpeta()
    {
        return $this->me->carpeta_documentos ?? '';
    }

    public function consultar($id)
    {
        return $this->modelo::with('estado')->where('id_organizacion', $id)->first();
    }
}
