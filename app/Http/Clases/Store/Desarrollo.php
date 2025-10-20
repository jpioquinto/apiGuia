<?php

namespace App\Http\Clases\Store;

use App\Http\Clases\Validations\ValidaDesarrollo;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Project\Desarrollo AS ModelDesarrollo;

class Desarrollo extends ValidaDesarrollo
{
    protected $desarrollo;

    protected $actividad;

    protected $seccion;

    protected $componente;

    public function __construct(Relation $modeloDesarrollo, array $componente, $seccion = 5)
    {
        $this->seccion    = $seccion;

        $this->componente = $componente;

        parent::__construct($componente);

        $this->crear(parent::getValidados(), $modeloDesarrollo);
    }

    public function crear(array $componente, Relation $modeloDesarrollo)
    {
        $campos = [
            'nomb_comp'=>$componente['nombre'],
            'id_componente'=>$componente['id'],
        ];

        isset($componente['orden'])          ? $campos['indice'] = $this->seccion .'.'. $componente['orden']  : null;
        isset($componente['situacion'])      ? $campos['situacion_actual'] =  $componente['situacion']        : null;
        isset($componente['estrategia'])     ? $campos['estrategia_desarrollo'] = $componente['estrategia']   : null;
        isset($componente['aporteFederal'])  ? $campos['aportacion_federal'] = $componente['aporteFederal']   : null;
        isset($componente['aporteEstatal'])  ? $campos['aportacion_estatal'] = $componente['aporteEstatal']   : null;
        isset($componente['tipoRecurso'])    ? $campos['tipo_recurso']       = $componente['tipoRecurso']     : null;

        $this->desarrollo = $modeloDesarrollo->create($campos);

        isset($componente['objetivos']) && is_array($componente['objetivos']) ? $this->crearObjetivo($componente['objetivos'], $campos['indice']) : null;

        isset($componente['programa']) && is_array($componente['programa']) ? $this->crearPrograma($componente['programa']) : null;
        isset($componente['actividades']) && is_array($componente['actividades']) ? $this->crearActividad($componente['actividades']) : null;
        isset($componente['acervo']['oficinas']) && is_array($componente['acervo']['oficinas']) ? $this->crearOficinaRPP($componente['acervo']['oficinas']) : null;
    }

    public function crearObjetivo($objetivos, $indice, $seccion = 2)
    {
        foreach ($objetivos as $objetivo) {
            $objetivo['indice'] = $indice . '.' . $seccion . '.' . $objetivo['orden'];
            new Objetivo($this->desarrollo->objetivos(), $objetivo);
        }
    }

    public function crearPrograma($programas)
    {
        foreach ($programas as $key => $value) {
            new Programa(
                    $this->desarrollo->programas(),
                    ['indice'=>$key, 'meses'=>implode(',', $value)]
                );
        }
    }

    public function crearOficinaRPP($oficinas)
    {
        foreach ($oficinas as $oficina) {
            $this->registrarAcervo($oficina['acervo']);
        }
    }

    protected function registrarAcervo($acervo)
    {
        foreach ($acervo as $value) {
            new OficinaRPP($this->desarrollo->oficinas(), $value);
        }
    }

    public function crearActividad($actividades)
    {
        foreach ($actividades as $actividad) {
            new Actividad($this->desarrollo->actividades(), $actividad);
        }
    }

}
