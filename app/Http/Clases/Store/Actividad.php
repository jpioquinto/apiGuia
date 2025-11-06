<?php

namespace App\Http\Clases\Store;

use App\Http\Clases\Validations\ValidaActividad;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Project\Actividad AS ModelActividad;
use Illuminate\Support\Facades\Log;

class Actividad extends ValidaActividad
{
    protected $actividad;

    public function __construct(Relation $modeloActividad, array $actividad)
    {
        parent::__construct($actividad);
        #Log::info('actividad...', ['porValidar'=>parent::getFirstError()]);
        $this->crear($modeloActividad, parent::getValidados());
    }

    public function crear(Relation $modeloActividad, $actividad)
    {
        $campos = [
            'id_subcomponente'=>$actividad['subcomp'],
            'id_cat_actividad'=>$actividad['act'],
            'descripcion'=>$actividad['desc'],
            'id_entregable'=>$actividad['entregable'],
            'id_unidad'=>$actividad['unidad'],
            'cantidad'=>$actividad['cantidad'],
            'costo_unitario'=>$actividad['costo'],
            'iva'=>$actividad['iva'],
            'total'=>$actividad['total'],
            'municipios'=>implode(',', $actividad['munpios']),
        ];

        isset($actividad['subact'])      ? $campos['id_sub_actividad']   = $actividad['subact']    : null;
        isset($actividad['ejecucion'])   ? $campos['programa_ejecucion'] = $actividad['ejecucion'] : null;
        isset($actividad['tipoRecurso']) ? $campos['tipo_recurso']  = $actividad['tipoRecurso']    : null;
        isset($actividad['mest'])        ? $campos['monto_estatal'] = $actividad['mest']  : null;
        isset($actividad['mfed'])        ? $campos['monto_federal'] = $actividad['mfed']  : null;
        !isset($actividad['comentarios'])? $campos['comentarios']   = '' : null;
        !isset($actividad['historial'])  ? $campos['historial']     = '' : null;

        $this->actividad = $modeloActividad->create($campos);

        isset($actividad['anexos']) && is_array($actividad['anexos']) ? $this->crearAnexo($actividad['anexos']) : null;
    }

    public function crearAnexo($anexos)
    {
        foreach ($anexos as $anexo) {
            $anexo['ext'] = $this->obtenerExt($anexo['nombre_anterior'] ?? '');
            new Anexo($this->actividad->anexos(), $anexo);
        }
    }

    protected function obtenerExt($cadena)
    {
        $extension = explode('.', $cadena);

        return mb_strtolower(trim( end($extension) ));
    }
}
