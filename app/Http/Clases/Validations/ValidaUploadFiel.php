<?php

namespace App\Http\Clases\Validations;

use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\File;

class ValidaUploadFiel extends Validacion
{
    public function __construct(array $datos = [])
    {
        parent::__construct($datos);
    }

    public function rules()
    {
        return [
            'proyectoId'=>'nullable|numeric',
            'archivoCer'=>['required', FILE::types(['cer', 'crt', 'csr', 'bin'])->max(Config::get('max_size', 25) . 'mb')],
            'archivoKey'=>['required', FILE::types(['key', 'bin'])->max(Config::get('max_size', 25) . 'mb')],
        ];
    }

    public function messages()
    {
        return [
            'archivoCer.required'=>"No se recibió el :attribute a cargar.",
            'archivoCer.max'=>"El archivo :attribute sobre pasa el tamaño permitido de " . Config::get('max_size', 25) . 'MB',
            'archivoKey.required'=>"No se recibió el :attribute a cargar.",
            'archivoKey.max'=>"El  archivo :attribute sobre pasa el tamaño permitido de " . Config::get('max_size', 25) . 'MB'
        ];
    }

    public function attributes()
    {
        return [
            'proyectoId'=>'identificador del proyecto',
            'archivoCer'=>'certificado',
            'archivoKey'=>'llave privada'
        ];
    }
}
