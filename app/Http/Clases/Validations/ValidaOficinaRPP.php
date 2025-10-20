<?php

namespace App\Http\Clases\Validations;

class ValidaOficinaRPP extends Validacion
{
    public function __construct(array $datos = [])
    {
        parent::__construct($datos);
    }

    public function rules()
    {
        return [
            'oficina'=>'required|integer',
            'concepto'=>'required',
            'acervo_existe'=>'required|integer',
            'acervo_digitalizado'=>'required|integer',
            'porc_digitalizado'=>'required|integer',
            'libros_legajos'=>'required|integer',
            'num_imagenes'=>'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'oficina.required'=>"El :attribute es requerido.",
            'oficina.integer'=>"El :attribute debe ser un entero consecutivo mayor que cero.",
            'concepto.required'=>"El :attribute (LIBROS/LEGAJOS) es requerido.",
            'acervo_existe.required'=>"El :attribute es requerido.",
            'acervo_existe.integer'=>"El :attribute debe ser un número entero.",
            'acervo_digitalizado.required'=>"El :attribute es requerido.",
            'acervo_digitalizado.integer'=>"El :attribute debe ser un número entero.",
            'porc_digitalizado.required'=>"El :attribute es requerido.",
            'porc_digitalizado.integer'=>"El :attribute debe ser un número entero.",
            'libros_legajos.required'=>"El :attribute es requerido.",
            'libros_legajos.integer'=>"El :attribute debe ser un número entero.",
            'num_imagenes.required'=>"El :attribute es requerido.",
            'num_imagenes.integer'=>"El :attribute debe ser un entero.",
        ];
    }

    public function attributes()
    {
        return [
            'oficina'=>'identificador de la oficina registral',
            'concepto'=>'tipo de acervo',
            'acervo_existe'=>'acervo existente',
            'acervo_digitalizado'=>'acervo digitalizado',
            'porc_digitalizado'=>'porcentaje de acervo digitalizado',
            'libros_legajos'=>'número de libros/legajos pendientes',
            'num_imagenes'=>'número de imágenes pendientes por digitalizar',
        ];
    }
}
