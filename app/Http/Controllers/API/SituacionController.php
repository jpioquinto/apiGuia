<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Situation\Situacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SituacionController extends Controller
{
    public function index(Request $request)
    {
        $situacion = new Situacion($request->idProyecto, $request->id);

        return $situacion->obtenerSituacion();

    }
}
