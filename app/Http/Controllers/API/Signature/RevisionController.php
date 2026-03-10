<?php

namespace App\Http\Controllers\API\Signature;

use App\Http\Controllers\Controller;
use App\Models\Project\Proyecto;
use Illuminate\Http\Request;

class RevisionController extends Controller
{
    public function revision(Request $request)
    {
        $proyecto = Proyecto::where('id_proyecto', $request->proyectoId)->first();
        if ($proyecto) {
            $proyecto->estatus = $request->estatus;
            if ($proyecto->save()) {
                return response(['solicitud'=>true, 'message'=>'Proyecto firmado en espera de firma.'], 200);
            }
            return response(['solicitud'=>false, 'message'=>'Error al enviar el Proyecto a espera de firma.'], 400);
        }
        return response(['solicitud'=>false, 'message'=>'Proyecto a firmar no encontrado.'], 404);
    }
}
