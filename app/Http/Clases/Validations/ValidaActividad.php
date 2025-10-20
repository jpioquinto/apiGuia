<?php

namespace App\Http\Clases\Validations;

class ValidaActividad extends Validacion
{
    public function __construct(array $actividad = [])
    {
        parent::__construct($actividad);
    }

    public function rules()
    {
        return [
            'subcomp'=>'required|integer',
            'act'=>'required|integer',
            'subact'=>'nullable|integer',
            'desc'=>'required',
            'entregable'=>'required|integer',
            'unidad'=>'required|integer',
            'cantidad'=>'required|numeric',
            'costo'=>'required|numeric',
            'iva'=>'required|numeric',
            'total'=>'required|numeric',
            'munpios'=>'required',
            'ejecucion'=>'nullable|string',
            'tipoRecurso'=>'nullable|integer',
            'mest'=>'nullable|numeric',
            'mfed'=>'nullable|numeric',
            'anexos'=>'nullable|present',
        ];
    }

    public function messages()
    {
        return [
            'subcomp.required'=>"El :attribute es requerido.",
            'subcomp.integer'=>"El :attribute debe ser un entero consecutivo mayor que cero.",
            'act.required'=>"El :attribute es requerido.",
            'act.integer'=>"El :attribute debe ser un entero consecutivo mayor que cero.",
            'subact.integer'=>"El :attribute debe ser un entero consecutivo mayor que cero.",
            'desc.required'=>"La :attribute es requerida y esta debe ser resumida y clara.",
            'entregable.required'=>"El :attribute es requerido.",
            'entregable.integer'=>"El :attribute debe ser un entero consecutivo mayor que cero.",
            'unidad.required'=>"La :attribute es requerido.",
            'unidad.integer'=>"El :attribute debe ser un entero consecutivo mayor que cero.",
            'cantidad.required'=>"La :attribute es requerida.",
            'cantidad.numeric'=>"La :attribute debe ser un dato numérico.",
            'costo.required'=>"El :attribute es requerido.",
            'costo.numeric'=>"El :attribute debe ser una cantidad numérica.",
            'iva.required'=>"El :attribute es requerido.",
            'iva.numeric'=>"El :attribute debe ser una cantidad numérica.",
            'total.required'=>"El costo :attribute es requerido.",
            'total.numeric'=>"El costo :attribute debe ser una cantidad numérica.",
            'munpios.required'=>"Se requiere el o los :attribute beneficiado(s) con la ejecución de la actividad.",
            'ejecucion.string'=>"El o los :attribute debe ser una cadena valida.",
            'tipoRecurso.integer'=>"El :attribute debe ser un entero.",
            'mest.numeric'=>"El :attribute debe ser una cantidad numérica.",
            'mfed.numeric'=>"El :attribute debe ser una cantidad numérica.",
        ];
    }

    public function attributes()
    {
        return [
            'subcomp'=>'identificador del subcomponente',
            'act'=>'identificador de la actividad',
            'subact'=>'identificador de la subactividad',
            'desc'=>'descripción de la actividad',
            'entregable'=>'identificador del entregable',
            'unidad'=>'identificador de la unidad',
            'cantidad'=>'cantidad',
            'costo'=>'costo unitario',
            'iva'=>'iva',
            'total'=>'total',
            'munpios'=>'municipio(s)',
            'ejecucion'=>'meses de ejecución',
            'tipoRecurso'=>'tipo de recurso',
            'mest'=>'monto estatal',
            'mfed'=>'monto federal',
        ];
    }
}
