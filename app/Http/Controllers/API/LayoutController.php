<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Project\MenuLateral;

class LayoutController extends Controller
{
    public function index()
    {
        return $this->obtenerMenu();
    }

    protected function obtenerMenu()
    {
        $listado = collect();
        MenuLateral::orderBy('orden')->get()->each(function($value, $key) use (&$listado) {
            $listado[$key] = $value;
            #$listado[$key]['icono'] = '';
            $listado[$key]['cargado'] = false;
            $listado[$key]['capturado'] = false;
            $listado[$key]['seleccionado'] = false;
        });
        return $listado;
    }
}
