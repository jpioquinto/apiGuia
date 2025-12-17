<?php

namespace App\Http\Controllers\API;

use App\Http\Clases\Upload\UploadAnexo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnexoController extends Controller
{
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
}
