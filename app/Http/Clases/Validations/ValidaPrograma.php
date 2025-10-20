<?php

namespace App\Http\Clases\Validations;

class ValidaPrograma extends Validacion
{
    public function __construct(array $programa = [])
    {
        parent::__construct($programa);
    }

    public function rules()
    {
        return [
            'indice'=>'required|integer',
            'meses'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'indice.required'=>"El :attribute es requerido.",
            'indice.integer'=>"El :attribute debe ser un entero mayor a cero.",
            'meses.required'=>"El número de :attribute que durará la ejecución es requerido.",
        ];
    }

    public function attributes()
    {
        return [
            'indice'=>'número de actividades',
            'meses'=>'meses',
        ];
    }
}
