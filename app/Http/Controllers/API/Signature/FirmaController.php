<?php

namespace App\Http\Controllers\API\Signature;

use App\Http\Clases\Signature\FielValidator;
use App\Http\Clases\Upload\UploadFiel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class FirmaController extends Controller
{
    public function validar(Request $request)
    {
        try {#return $request->file('archivoCer')->extension();
            $upload = new UploadFiel($request);
            $load   = $upload->upload();

            if (!$load['solicitud']) {
                throw new Exception('Ocurrió un error al intentar cargar los archivos de la FIEL. ' . ($load['testing'] ?? $load['message']));
            }

            $valid = FielValidator::validate($load->getNameCer(), $load->getNameKey(), $request->input('fielPassword'));
            if (!$valid['solicitud']) {
                throw new Exception('Error al validar la FIEL. ' . ($valid['error'] ?? ''));
            }

            return response(array_merge($valid, ['cer' => $load->getNameCer(), 'key' => $load->getNameKey()]), 200);
        } catch (Exception $e) {
            return response(['solicitud'=>false,'message'=>$e->getMessage()], 400);
        }
    }

    public function firmar(Request $request)
    {

    }
}
