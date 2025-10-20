<?php

namespace App\Http\Clases\Validations;

class ValidaAnexo extends Validacion
{
    public function __construct(array $anexo = [])
    {
        parent::__construct($anexo);
    }

    public function rules()
    {
        return [
            'nombre'=>'required',
            'nombre_anterior'=>'required',
            'descripcion'=>'required',
            'ext'=>'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'=>"El :attribute es requerido.",
            'nombre_anterior.required'=>"El :attribute es requerido.",
            'descripcion.required'=>"La :attribute es requerida de manera clara y resumida.",
            'ext.string'=>"La :attribute debe ser una cadena.",
        ];
    }

    public function attributes()
    {
        return [
            'nombre'=>'nombre asignado al documento',
            'nombre_anterior'=>'nombre original del documento',
            'descripcion'=>'descripción del anexo',
            'ext'=>'extensión del documento',
        ];
    }
}
