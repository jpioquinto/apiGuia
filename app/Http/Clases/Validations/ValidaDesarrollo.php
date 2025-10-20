<?php

namespace App\Http\Clases\Validations;

class ValidaDesarrollo extends Validacion
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
            'orden'=>'nullable|integer',
            'id'=>'required|integer',
            'nombre'=>'required',
            'situacion'=>'nullable|string',
            'estrategia'=>'nullable|string',
            'aporteFederal'=>'nullable|numeric',
            'aporteEstatal'=>'nullable|numeric',
            'tipoRecurso'=>'nullable|integer',
            'objetivos'=>'nullable',
            'actividades'=>'nullable',
            'programa'=>'nullable',
            'acervo'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'orden.integer'=>"El :attribute debe ser un entero consecutivo mayor que cero.",
            'id.required'=>"El :attribute es requerido.",
            'id.integer'=>"El :attribute debe ser un entero mayor que cero.",
            'nombre.required'=>"El :attribute es requerido.",
            'situacion.string'=>"La :attribute debe ser clara y coherente.",
            'estrategia.string'=>"La :attribute debe ser clara y coherente.",
            'aporteFederal.numeric'=>"La :attribute debe ser una cantidad numérica.",
            'aporteEstatal.numeric'=>"La :attribute debe ser una cantidad numérica.",
            'tipoRecurso.integer'=>"El :attribute debe ser un entero.",
        ];
    }

    public function attributes()
    {
        return [
            'orden'=>'orden',
            'id'=>'identificador del componente',
            'nombre'=>'nombre del componente',
            'situacion'=>'situacion actual',
            'estrategia'=>'estrategia de desarrollo',
            'aporteFederal'=>'aportación federal',
            'aporteEstatal'=>'aportación estatal',
            'tipoRecurso'=>'tipo de recurso',
        ];
    }
}
