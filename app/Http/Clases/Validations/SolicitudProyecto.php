<?php

namespace App\Http\Clases\Validations;

class SolicitudProyecto extends Validacion
{
    protected $datos;

    public function __construct(array $datos = [])
    {
        $this->datos = $datos;
        parent::__construct($datos);
    }

    public function rules()
    {
        return [
            'id'=>'nullable|numeric',
            'diagnosticoId'=>'nullable|numeric',
            'mEst'=>'nullable|numeric',
            'mFed'=>'nullable|numeric',
            'mTotal'=>'nullable|numeric',
            'millar'=>'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'id.numeric'=>"El :attribute debe ser un entero mayor que cero.",
            'diagnosticoId.numeric'=>"El :attribute debe ser un entero mayor que cero.",
            'mEst.numeric'=>"La :attribute debe ser una cantidad numérica.",
            'mFed.numeric'=>"La :attribute debe ser una cantidad numérica.",
            'mTotal.numeric'=>"El :attribute debe ser una cantidad numérica.",
            'millar.numeric'=>"El :attribute para la fiscalización debe ser una cantidad numérica.",
        ];
    }

    public function attributes()
    {
        return [
            'id'=>'identificador de proyecto',
            'diagnosticoId'=>'identificador de diagnóstico',
            'mEst'=>'aportación estatal',
            'mFed'=>'aportación federal',
            'mTotal'=>'monto total',
            'millar'=>'uno al millar',
        ];
    }
}
