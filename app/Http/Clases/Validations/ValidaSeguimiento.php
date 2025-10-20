<?php

namespace App\Http\Clases\Validations;

class ValidaSeguimiento extends Validacion
{
    public function __construct(array $datos = [])
    {
        parent::__construct($datos);
    }

    public function rules()
    {
        return [
            'intro'=>'nullable|string',
            'situacion'=>'nullable|string',
            'logros'=>'nullable|string',
            'metas'=>'nullable|string',
            'objetivo'=>'nullable|string',
            'obsResumen'=>'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'intro.string'=>"Capture una :attribute lo más descriptible y coherente posible.",
            'situacion.string'=>"Describa la :attribute en la que se cuentra su institución.",
            'logros.string'=>"Describa los :attribute de la apliación del Programa de Modernización.",
            'metas.string'=>"Describa los :attribute con el desarrollo e implementación del Proyecto Ejecutivo de Modernización.",
            'objetivo.string'=>"Describa los :attribute a alcanzar lo más claro posible.",
            'obsResumen.string'=>"Capture la :attribute lo más clara y coherente posible.",
        ];
    }

    public function attributes()
    {
        return [
            'intro'=>'introducción',
            'situacion'=>'situación general',
            'logros'=>'logros de la aplicación',
            'metas'=>'resultados esperados',
            'objetivo'=>'objetivos',
            'obsResumen'=>'observación al resumen financiero',
        ];
    }
}
