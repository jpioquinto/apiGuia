<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Antecedent\Antecedentes;
use App\Http\Clases\Store\{Proyecto as StoreProyecto};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Project\{Proyecto};
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Exception;

use Illuminate\Support\Facades\Log;

class ProyectoController extends Controller
{
    protected $proyecto;

    public function index(Request $request)
    {
        return array_merge($this->obtenerSeguimiento($request->id), $this->obtenerDiagnostico($this->proyecto));
    }

    public function test()
    {#Proyecto::where('id_proyecto', 41500)->first()
        return response(['solicitud'=>true, 'message'=>'prueba', 'proyecto'=>$this->obtenerProyecto(415)], 200);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            $proyecto = new StoreProyecto($request->all());
            if ($proyecto->guardar()) {
                DB::commit();
            } else {
                throw new Exception($proyecto->existsError() ? $proyecto->getFirstError() : '');
            }
        } catch (Exception $e) {
            DB::rollback();return $e;
            return response(['solicitud'=>false, 'message'=>'Error al guardar la información del Proyecto. '.$e->getMessage()], 400);
        }

        return response([
            'solicitud'=>true,
            'message'=>'Proyecto guardado correctamente.',
            'version'=>$proyecto->getUltimaVersion(),
            'id'=>$request->input('id') ?? $proyecto->getId(),
        ], 200);
    }

    public function setProyecto(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    }

    protected function obtenerProyecto($id=0)
    {
        if ($id>0) {
            return Proyecto::with('versiones')
            ->where('id_proyecto', $id)
            ->first() ?: new Proyecto();
        }
        return  Proyecto::with('versiones')
                ->where('id_organizacion', auth()->user()->directorio->id_organizacion)
                ->whereYear('fecha', date('Y')) #quitar el -1
                ->whereBetween('estatus', [1,11])
                ->orderBy('fecha','DESC')
                ->first() ?: new Proyecto();
    }

    protected function obtenerSeguimiento($id)
    {
        $this->setProyecto($proyecto = $this->obtenerProyecto($id));
        if ($proyecto==null) {
            return $this->proyectoNoEncontrado();
        }
        $ultimaVersion = $proyecto->versiones->where('version',$proyecto->versiones->max('version'))->first();
        if ($ultimaVersion==null) {
            return $this->proyectoNoEncontrado();
        }
        /*if (!$proyecto->millar && $proyecto->anio<2019) {
            $proyecto->millar = $this->calcularFiscalizacion();
        }*/
        return [
            'suficiencia'  => $ultimaVersion->presupuesto_estatal,
            'introduccion' => $ultimaVersion->seguimiento->introduccion,
            'situacion' => $ultimaVersion->seguimiento->situacion_gral,
            'logros' => $ultimaVersion->seguimiento->logros_aplicacion,
            'objetivo' => $ultimaVersion->seguimiento->objetivo_gral,
            'observaciones_resumen' => $ultimaVersion->seguimiento->observaciones_resumen,
            'metas'  => $ultimaVersion->seguimiento->metas_globales,
            'version'  => $ultimaVersion->version,
            'vertiente'=>$proyecto->vertiente ?: auth()->user()->directorio->organizacion->vertiente_id,
            'millar'=>$proyecto->millar ? (float)$proyecto->millar : $proyecto->millar,
            'estatus'=>$proyecto->estatus,
            'id' => $proyecto->id_proyecto,
            'anio'=>$proyecto->anio,
            'iconos'=>$this->obtenerIconos(),
            'alMillar'=>1,
            'iva'=>16,
        ];
    }

    protected function proyectoNoEncontrado()
    {
        return [
            'suficiencia'  => null,
            'introduccion' => null,
            'situacion' => null,
            'logros' => null,
            'objetivo' => null,
            'observacion_resumen' => null,
            'metas'  => null,
            'vertiente'=> auth()->user()->directorio->organizacion->vertiente_id,
            'millar'=>0,
            'estatus'=>1,
            'id' => 0,
            'anio'=>(int)date('Y'),
            'iconos'=>$this->obtenerIconos(),
            'alMillar'=>1,
            'iva'=>16,
        ];
    }

    protected function obtenerIconos()
    {
        return [
            'pdf'=>asset('images/iconos/png/32x32/pdf.png')
        ];
    }

    protected function obtenerDiagnostico(Proyecto $proyecto = null)
    {
        $antecedente = new Antecedentes($proyecto, $proyecto->vertiente ?? null);
        $antecedente->inicializaDiagnostico();
        return [
            'anioDiagnostico'=>$antecedente->obtenerAnioDiagnostico(),
            'idDiagnostico'=>$antecedente->obtenerIdDiagnostico(),
        ];
    }
}
