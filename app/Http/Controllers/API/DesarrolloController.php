<?php

namespace App\Http\Controllers\API;

use App\Http\Clases\{Proyecto as CProyecto, Desarrollo};
use App\Http\Controllers\Controller;
use App\Models\Project\{Proyecto};

class DesarrolloController extends Controller
{
    public function __construct()
    {

    }

    public function index(Proyecto $proyecto)
    {
        abort_if(
            ($proyecto->versiones->where('version',$proyecto->versiones->max('version'))->first())==null,
            400,
            'No se encontró la versión para este proyecto.'
        );

        $cProyecto  = new CProyecto($proyecto);

        $desarrollo = new Desarrollo(
                        $cProyecto->getVersion()->desarrollo ?? collect([]),
                        $cProyecto->diagnostico->getComponentes($cProyecto->getVertiente()),
                        $cProyecto->getIdDiagnostico(),
                        $cProyecto->getCarpeta()
                    );

        if (strcmp($proyecto->vertiente, '1,2')==0) {
            return [
                'pec'=>$desarrollo->obtenerComponentes(1),
                'pem'=>$desarrollo->obtenerComponentes(2)
            ];
        }
        return [
            ($proyecto->vertiente==1 ? 'pec' : 'pem') => $desarrollo->obtenerComponentes()
        ];
    }
}
