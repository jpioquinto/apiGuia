<?php

namespace App\Http\Controllers\API;

use App\Http\Clases\{Proyecto as CProyecto, Desarrollo};
use App\Http\Controllers\API\Acquis\Conservacion;
use App\Http\Controllers\Controller;
use App\Models\Project\Proyecto;
use Illuminate\Http\Request;

class OficinaController extends Controller
{
    public $mapeo = [

        [
            'concepto'=>'LIBROS DE INSCRIPCIÓN',
            'total_libros'=>'acervo_existe',
            'tomos_libros_dig'=>'acervo_digitalizado',
            'porcentaje_libros_dig'=>'porc_digitalizado',
            'tomos_libros_pend'=>'libros_legajos',
            'imagenes_libros_pend'=>'num_imagenes'
        ],
        [
            'concepto'=>'LEGAJOS',
            'total_legajos'=>'acervo_existe',
            'tomos_legajos_dig'=>'acervo_digitalizado',
            'porcentaje_legajos_dig'=>'porc_digitalizado',
            'tomos_legajos_pend'=>'libros_legajos',
            'imagenes_legajos_pend'=>'num_imagenes'
        ]
    ];

    public function index(Request $request)
    {
        return $this->obtenerOficinasRegistrales($request->idDiagnostico, $request->idOficina);
    }

    public function listado(Proyecto $proyecto)
    {
        $acervo     = new Conservacion($proyecto->id_diagnostico);

        $cProyecto = new CProyecto($proyecto);

        $desarrollo = new Desarrollo(
                            $cProyecto->getVersion()->desarrollo ?? collect([]),
                            $cProyecto->diagnostico->getComponentes($cProyecto->getVertiente()),
                            $cProyecto->getIdDiagnostico(),
                            $cProyecto->getCarpeta()
                        );

        $desarrollo->obtenerComponentes($cProyecto->getVertiente());

        return collect(['listado'=>$acervo->procesarListadoOficinas($acervo->obtenerOficinas(), $desarrollo->getOficinas())]);
    }

    protected function obtenerOficinasRegistrales($idDiagnostico, $idOficina)
    {
        $acervo = new Conservacion($idDiagnostico);

        $oficina = $acervo->obtenerOficinas()->firstWhere('detalle_oficinas_id', $idOficina);

        abort_if(
            !isset($oficina->detalle_oficinas_id),
            400,
            'No se encontró captura de la Cobertura de oficinas registrales, en su diagnóstico.'
        );

        $captura = $acervo->obtenerOficinas('conservacionAcervo')->firstWhere('num_oficina', $oficina->numero_oficina);

        abort_if(
            !isset($captura->id_conservacion),
            400,
            'No se capturó información para esta oficina en su Diagnóstico.'
        );

        return $this->procesarOficina($oficina, $captura);
    }

    protected function procesarOficina($oficina, $captura)
    {
        $datos = [
            'actualizar'=>false,
            'nombre'=>$oficina->oficina,
            'acervo'=>collect(),
            'total'=>collect([
                'concepto'=>'TOTAL',
                'existente'=>0,
                'digitalizado'=>0,
                'porcentaje'=>0,
                'librosLegajos'=>0,
                'numImagenes'=>0
            ]),
            'id'=>$oficina->detalle_oficinas_id
        ];

        foreach ($this->mapeo as $value) {
            $datos['acervo']->push($acervo = $this->obtenerCaptura($captura, $value, $datos['id']));
            $datos['total']['existente'] += isset($acervo['acervo_existe']) ? $this->procesarDatoNumerico($acervo['acervo_existe']) : 0;
            $datos['total']['digitalizado'] += isset($acervo['acervo_digitalizado']) ? $this->procesarDatoNumerico($acervo['acervo_digitalizado']) : 0;
            $datos['total']['porcentaje'] += isset($acervo['acervo_existe']) ? $this->procesarDatoNumerico($acervo['porc_digitalizado']) : 0;
            $datos['total']['librosLegajos'] += isset($acervo['libros_legajos']) ? $this->procesarDatoNumerico($acervo['libros_legajos']) : 0;
            $datos['total']['numImagenes'] += isset($acervo['num_imagenes']) ? $this->procesarDatoNumerico($acervo['num_imagenes']) : 0;
        }

        return $datos;
    }

    protected function obtenerCaptura($captura, $mapeo, $idOficina)
    {
        $info = ['concepto'=>$mapeo['concepto'], 'oficina'=>$idOficina];

        foreach ($mapeo as $key=>$campo) {
            if (!isset($captura->{$key})) {
                continue;
            }
            $info[$campo] = $captura->{$key};
        }
        return $info;
    }

    protected function procesarDatoNumerico($dato)
    {
        return is_numeric($dato) ? $dato : 0;
    }
}
