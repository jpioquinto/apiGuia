<?php

namespace App\Http\Clases\Validations;

use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\File;

class ValidaUploadAnexo extends Validacion
{
    public function __construct(array $datos = [])
    {
        parent::__construct($datos);
    }

    public function rules()
    {
        return [
            'nombre'=>'required|string|min:3',
            'proyectoId'=>'nullable|numeric',
            'archivo'=>['required', FILE::types(['pdf', 'tiff', 'tif'])->max(Config::get('max_size', 25) . 'mb')],
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'=>"El :attribute es requerido.",
            'nombre.string'=>"El campo :attribute debe ser una cadena.",
            'nombre.min'=>"El campo :attribute debe tener al menos 3 caracteres.",
            'proyectoId.required'=>"El :attribute es requerido.",
            'archivo.required'=>"No se recibió el :attribute a cargar.",
            'archivo.max'=>"El :attribute sobre pasa el tamaño permitido de " . Config::get('max_size', 25) . 'MB'
        ];
    }

    public function attributes()
    {
        return [
            'nombre'=>'nombre del documento',
            'proyectoId'=>'identificador del proyecto',
            'archivo'=>'archivo'
        ];
    }
}
