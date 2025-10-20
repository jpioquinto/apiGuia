<?php

namespace App\Http\Clases\Validations;

class ValidaObjetivo extends Validacion
{
    public function __construct(array $objetivo = [])
    {
        parent::__construct($objetivo);
    }

    public function rules()
    {
        return [
            'indice'=>'nullable|string',
            'orden'=>'required|integer',
            'objetivo'=>'required',
            'alcance'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'orden.required'=>"No se recibió el :attribute.",
            'orden.integer'=>"El :attribute debe ser un entero consecutivo.",
            'indice.string'=>"No se recibió el :attribute para el objetivo específico.",
            'objetivo.required'=>"Describa el :attribute de manera clara y precisa.",
            'alcance.required'=>"Describa el :attribute del objetivo específico.",
        ];
    }

    public function attributes()
    {
        return [
            'indice'=>'indice',
            'orden'=>'número de objetivo',
            'objetivo'=>'objetivo especifico',
            'alcance'=>'alcance',
        ];
    }
}
