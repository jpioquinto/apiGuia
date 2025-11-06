<?php

namespace App\Http\Controllers\API;

use App\Models\Project\Situation\{CatalogoSubcomponente, CatalogoSubActividad, CatalogoEntregable, CatalogoUnidad};
use App\Http\Controllers\Controller;
use App\Models\Diagnostic\Sigirc\Organizacion as OrganizacionSIGIRC;
use App\Models\Diagnostic\Sigcm\Organizacion as OrganizacionSIGCM;
use Illuminate\Http\Request;
use App\Models\Project\Proyecto;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        return [$request->id=>$this->obtenerSubcomponentes($request->id, $request->idProyecto)];
    }

    function obtenerSubActividades(Request $request)
    {
        return CatalogoSubActividad::where(function ($query) use ($request) {
            $query->where('actividad', $request->id)
            ->orWhere('actividad', 'like', "{$request->id},%")
            ->orWhere('actividad', 'like', "%,{$request->id},%")
            ->orWhere('actividad', 'like', "%,{$request->id}");
        })->get();
    }

    function obtenerEntregables(Request $request)
    {
        return [$request->id=>$this->filtrarEntregables($this->consultarEntregables($request->id), $request->id)];
    }

    function obtenerUnidades()
    {
        return CatalogoUnidad::where('estatus', 1)->get();
    }

    function obtenerMunicipios(Request $request)
    {
        return $this->obtenerMpiosPorProyecto($request->idProyecto);
    }

    protected function consultarMunicipios($idProyecto=0)
    {

    }

    protected function filtrarEntregables($entregables, $idComponente)
    {
        if ($entregables->isEmpty()) {
            return $entregables;
        }

        $filtrdados =  $entregables->filter(function ($value, $key) use ($idComponente) {
            return in_array($idComponente, explode(',', str_replace(' ','', $value['id_componente'])))!==FALSE;
        });

        return $filtrdados->values()->all();
    }

    protected function consultarEntregables($idComponente)
    {
        return CatalogoEntregable::where(function ($query) use ($idComponente) {
            $query->where('id_componente', $idComponente)
            ->orWhere('id_componente', 'like', "{$idComponente},%")
            ->orWhere('id_componente', 'like', "%,{$idComponente},%")
            ->orWhere('id_componente', 'like', "%,{$idComponente}");
        })
        ->where('estatus', 1)->get();
    }

    protected function obtenerSubcomponentes($idComponente, $idProyecto)
    {
        return CatalogoSubcomponente::with(['actividades'])
        ->where(function ($query) use ($idComponente) {
            $query->where('componente', $idComponente)
            ->orWhere('componente', 'like', "{$idComponente},%")
            ->orWhere('componente', 'like', "%,{$idComponente},%")
            ->orWhere('componente', 'like', "%,{$idComponente}");
        })->where(function ($query) use ($idProyecto) {
            $query->where('diagnostico', $this->tipoDiagnostico($idProyecto))
            ->orWhere('diagnostico', '1,2');
        })->get();
    }

    protected function tipoDiagnostico($idProyecto)
    {
        $proyecto = Proyecto::where('id_proyecto',$idProyecto)->first();
        if (is_null($proyecto)) {
            return 1;
        }
        return $proyecto->id_app_diag;
    }

    protected function obtenerMpiosPorProyecto($idProyecto)
    {
        $proyecto = Proyecto::where('id_proyecto',$idProyecto)->first();

        if (is_null($proyecto)) {
            return auth()->user()->directorio->organizacion->obtenerMunicipios();
        }

        return $this->obtenerOrganizacion($proyecto)->obtenerMunicipios();

    }

    protected function obtenerOrganizacion(Proyecto $proyecto)
    {
        $modelo = new OrganizacionSIGIRC;
        if (!$proyecto->esEstatal() && $proyecto->anio()<2019) {
            $modelo = new OrganizacionSIGCM;
        }
        return $modelo::where('id_organizacion', $proyecto->id_organizacion)->first();
    }
}
