<?php

namespace App\Http\Controllers\API;

use App\Http\Clases\Dashboard\Filters\{Dashboard, Entidad, Proyecto as ProyectoDash};
use App\Models\Project\{Proyecto, ProjectQueryBuilder as ProjectQuery};
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Models\Diagnostic\Sigirc\Estado;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function listarAnios(Request $request)
    {
        return response(['solicitud'=>true, 'message'=>'Listado de Años.', 'anios'=>ProjectQuery::listYearProjects()], 200);
    }

    public function listarEntidades(Request $request)
    {
        $dashboard = new Dashboard( new Entidad($request->anio) );

        return response([
            'solicitud'=>true,
            'message'=>'Listado de Entidades.',
            'entidades'=>$dashboard->listarResultado()
        ], 200);
    }

    public function listarProyectos(Request $request)
    {
        $dashboard = new Dashboard( new ProyectoDash($request->anio, $request->edoId) );

        return response([
            'solicitud'=>true,
            'message'=>'Listado de Proyectos.',
            'proyectos'=>$dashboard->listarResultado()
        ], 200);
    }
}
