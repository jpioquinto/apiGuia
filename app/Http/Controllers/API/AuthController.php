<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
#use Illuminate\Http\RedirectResponse;
use App\Models\Permissions\{Permiso, Accion};

class AuthController extends Controller
{
    public function __construct()
    {
        #$this->middleware('auth:api')->except('login');
    }
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'nickname' => 'required',
            'password' => 'required'
        ]);

        if(!Auth::attempt($loginData)) {
            return response(['message'=>'Las credenciales de acceso son incorrectas'],401);
        }

        $resultToken = auth()->user()->createToken('authToken');

        if ($request->remember) {
           $resultToken->accessToken->expires_at = Carbon::now()->addWeeks(1);
        }
        #$resultToken->token->save();

        return response([
                    'user' => [
                        'nickname'=> auth()->user()->nickname,
                        'rol'=>in_array(auth()->user()->perfiles_id, [6,7,14]) ? 'user' : 'admin',
                        'nombre'=>(auth()->user()->directorio->nombre_completo ?: auth()->user()->nickname),
                        'acciones'=>$this->obtenerPermisos(auth()->user()->usuarios_id, auth()->user()->perfiles_id),
                    ],
                    'access_token' => $resultToken->plainTextToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => Carbon::parse($resultToken->accessToken->expires_at)->toDateTimeString(),
                ], 200);
    }
    public function logout(Request $request)
    {
        // Revoke all tokens...
		$request->user()->tokens()->delete();

		$request->user()->currentAccessToken()->delete();

        return response()->json([
            'solicitud'=>true,
            'message' =>'Sessión cerrada correctamente.'
        ],200);
    }

    protected function obtenerPermisos($userId, $perfilId)
    {
        $permisos = Permiso::where(function ($query) use ($userId, $perfilId) {
            $query->where('id_usuario', $userId)
            ->orWhere('id_perfil', $perfilId);
        })->get();

        $acciones = [];
        $permisos->each(function ($value, $key) use (&$acciones) {
            $acciones = array_merge($acciones, explode(',', $value->acciones));
        });
        $acciones = array_unique($acciones);
        $accionesDB = Accion::where(function ($query) use ($acciones) {
            $query->whereIn('id_acciones', $acciones);
        })->get();

        $listar = [];
        $accionesDB->each(function ($value, $key) use (&$listar){
            $listar[$value->id_acciones] = $value->accion;
        });

        return $listar;
    }
}
