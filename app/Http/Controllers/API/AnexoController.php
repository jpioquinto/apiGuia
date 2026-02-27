<?php

namespace App\Http\Controllers\API;

use App\Http\Clases\Upload\UploadAnexo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Clases\{Proyecto as CProyecto};
use App\Models\Project\Proyecto;
use Exception;

class AnexoController extends Controller
{
    protected $env;

    public function __construct()
    {
        $this->env = Config::get('filesystems.default');
    }

    public function upload(Request $request)
    {
        try {
            $upload = new UploadAnexo($request);
            $move   = $upload->upload();

            if (!$move['solicitud']) {
                throw new Exception('Operación fallida. ' . ($move['testing'] ?? $move['message']));
            }
        } catch (Exception $e) {
            return response(['solicitud'=>false,'message'=>$e->getMessage()], 400);
        }

        return response($move, 200);
    }

    public function getAnexo(Request $request)
    {
        try {
            $path = $this->getDirectory($this->queryProject($request->proyectoId)) . "/{$request->nombre}";

            if (!Storage::disk($this->env)->exists($path)) {
                throw new Exception('No se encontró el anexo solicitado.');
            }

            $mimeType = Storage::disk($this->env)->mimeType($path);
            $data = Storage::disk($this->env)->get($path);
        } catch (Exception $e) {
            return response(['message'=>$e->getMessage()], 404);
        }



        return response($data, 200)
            ->header('Content-Type', $mimeType)
            ->header('Permissions-Policy', 'fullscreen=*');
    }

    protected function getDirectory(Proyecto $proyecto)
    {
        $project = new CProyecto($proyecto);
        return sprintf("documentos/%s/%d/anexos", $project->getCarpeta(), $project->getAnio());
    }

    protected function queryProject($id=0)
    {
        return Proyecto::with('versiones')
            ->where('id_proyecto', $id)
            ->first() ?: new Proyecto();
    }
}
