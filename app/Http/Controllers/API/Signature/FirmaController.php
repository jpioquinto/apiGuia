<?php

namespace App\Http\Controllers\API\Signature;

use App\Http\Clases\Signature\FielValidator;
use App\Http\Clases\Upload\UploadFiel;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\Project\Proyecto;
use Illuminate\Http\Request;
use Exception;

class FirmaController extends Controller
{
    public function validar(Request $request)
    {
        try {
            $upload = new UploadFiel($request);
            $load   = $upload->upload();

            if (!$load['solicitud']) {
                throw new Exception('Ocurrió un error al intentar cargar los archivos de la FIEL. ' . ($load['testing'] ?? $load['message']));
            }

            $valid = FielValidator::validate($upload->getNameCer(), $upload->getNameKey(), $request->input('fielPassword'));
            if (!$valid['solicitud']) {
                throw new Exception('Error al validar la FIEL. ' . ($valid['error'] ?? ''));
            }

            $this->setCamposFiel(
                $data = array_merge($valid, ['cer' => $upload->getNameCer(), 'key' => $upload->getNameKey()]),
                Crypt::encryptString($request->input('fielPassword')),
                Proyecto::where('id_proyecto', $request->proyectoId)->first() ?? new Proyecto()
            );

            return response($data, 200);
        } catch (Exception $e) {
            return response(['solicitud'=>false,'message'=>$e->getMessage()], 400);
        }
    }

    public function firmar(Request $request)
    {

    }

    public function setCamposFiel(array $data, string $password, Proyecto $proyecto)
    {
        if (auth()->user()->directorio->organizacion->tipo==2 || auth()->user()->perfiles_id==18) {
            auth()->user()->archivo_cer = $data['cer'];
            auth()->user()->archivo_key = $data['key'];
            auth()->user()->pass_key = $password;
            auth()->user()->save();
            $proyecto->id_certificador = auth()->user()->usuarios_id;
            $proyecto->nombre_certificador = $data['subject']['name'] ?? '';
            $proyecto->rfc_certificador = $data['subject']['x500UniqueIdentifier'] ?? '';
            $proyecto->save();
        } else {
            $proyecto->id_firmante = auth()->user()->usuarios_id;
            $proyecto->nombre_firmante = $data['subject']['name'] ?? '';
            $proyecto->rfc_firmante = $data['subject']['x500UniqueIdentifier'] ?? '';
            $proyecto->save();
        }
    }
}
