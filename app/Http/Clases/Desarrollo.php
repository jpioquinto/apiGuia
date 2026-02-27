<?php

namespace App\Http\Clases;

use App\Http\Controllers\API\Acquis\Conservacion;
use App\Models\Project\Anexo AS ModelAnexo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class Desarrollo
{
    protected $carpeta;
    protected $oficinas;
    protected $totalComponente;
    protected $componentesModelo;

    protected $idDiagnostico;
    protected $desarrollo;
    protected $anio;
    protected $env;

    public const IVA=0.16;

    protected $numDec;

    protected $homologos = [
        1=>['id'=>8,  'nombre'=>'Marco Jurídico'],
        2=>['id'=>9,  'nombre'=>'Procesos Catastrales / Registrales'],
        3=>['id'=>10, 'nombre'=>'Tecnologías de la Información'],
        4=>['id'=>15, 'nombre'=>'Vinculación RPP-Catastro / Participacion y Vinculación con otros Sectores'],
        5=>['id'=>12, 'nombre'=>'Profesionalización de la Función Catastral / Registral'],
        6=>['id'=>11, 'nombre'=>'Gestión de la Calidad'],
        7=>['id'=>13, 'nombre'=>'Políticas Institucionales']
    ];

    public function __construct($desarrollo, $componentesModelo, $idDiagnostico, $carpeta = '_documentos', $numDec = 2)
    {
        $this->componentesModelo = $componentesModelo;
        $this->desarrollo        = $desarrollo;
        $this->carpeta           = $carpeta;
        $this->numDec            = $numDec;

        $this->idDiagnostico = $idDiagnostico;

        $this->oficinas      = collect();

        $this->anio = date('Y');

        $this->env = Config::get('filesystems.default');
    }

    public function getHomologos()
    {
        return $this->homologos;
    }

    public function getNumDeC()
    {
        return $this->numDec;
    }

    public function setAnio(int $anio)
    {
        $this->anio = $anio;
    }

    public function getAnio(): int
    {
        return $this->anio;
    }

    public function setOficinas($oficinas)
    {
        $this->oficinas = $oficinas;
    }

    public function getOficinas()
    {
        return $this->oficinas;
    }

    public function obtenerTotal()
    {
        return $this->totalComponente;
    }

    public function obtenerComponentes($vertiente = '')
    {
        $componentes = collect();
        $this->desarrollo->each(function($value, $index) use ($componentes, $vertiente) {
            if ($vertiente!='' && !$this->perteneceAvertiente($value['id_componente'], $vertiente)) {
                return true;
            }
            $this->totalComponente = 0;
            $componenteModelo = $this->obtenerComponente($value['id_componente']);
            $componente = [
                'id'=>$value['id_componente'],
                'vertiente'=>$componenteModelo->modelos_id,
                'nombre'=>$this->procesarNombreComponente($value['nomb_comp']),
                'orden'=> $index + 1,
                'situacion'=>$value['situacion_actual'],
                'objetivos'=>$this->procesarObjetivos($value->objetivos),
                'actividades'=>$this->procesarActividades($value->actividades),
                'programa'=>$this->procesarPrograma($value->programas),
                'estrategia'=>$value['estrategia_desarrollo'],
                'aporteFederal'=>$value['aportacion_federal'],
                'aporteEstatal'=>$value['aportacion_estatal'],
                'posicion'=>$componenteModelo->orden,
                'total'=>round($this->obtenerTotal(), 2),
                'actualizado'=>false
            ];
            if ($componente['id']==14) {
                $componente['acervo'] = [
                    'listado'=>$listado = $this->obtenerOficinasRegistrales($this->idDiagnostico, $value->oficinas),
                    'oficinas'=>$this->procesarOficinas($value->oficinas, $listado)
                ];

                $this->setOficinas($this->oficinas->merge($value->oficinas));
            }
            $componentes->push($componente);
        });
        return $componentes;
    }

    protected function procesarObjetivos($registros)
    {
        $objetivos = collect();
        $registros->each(function($value, $index) use ($objetivos) {
            $objetivos->push([
                'objetivo'=>preg_replace('/^5[0-9|.]*/', '', $value['objetivo_especifico']),
                'alcance'=>$value['alcance'],
                'orden'=>($index + 1)
            ]);
        });
        return $objetivos;
    }

    protected function procesarActividades($registros)
    {
        $actividades = collect();
        $registros->each(function($value, $index) use (&$actividades) {
            $iva = $this->calcularIva($value['cantidad'], $value['costo_unitario']);
            $value['iva'] = $value['iva']==$iva ? $value['iva'] : $iva;
            $actividades->push([
                'id'=>$value['id_actividad'],
                'descSubcomp'=>$value->subcomponente->subcomponente,
                'descAct'=>$value->catactividad->actividad,
                'descSubAct'=>is_numeric($value['id_sub_actividad']) ? $value->catsubactividad->subactividad : '',
                'descEntregable'=>$value->entregable->entregable,
                'descUnidad'=>$value->unidad->unidad,
                'subcomp'=>$value['id_subcomponente'],
                'act'=>$value['id_cat_actividad'],
                'subact'=>is_numeric($value['id_sub_actividad']) ? $value['id_sub_actividad'] : null,
                'desc'=>$value['descripcion'],
                'entregable'=>$value['id_entregable'],
                'unidad'=>$value['id_unidad'],
                'munpios'=>explode(',', $value['municipios']),
                'cantidad'=>(float)$this->procesarRecurso($value['cantidad'], $value['tipo_recurso']),
                'costo'=>(float)$this->procesarRecurso( $value['costo_unitario'], $value['tipo_recurso']),
                'iva'=>(float)$this->procesarRecurso($value['iva'], $value['tipo_recurso']),
                'total'=>($total=$value['tipo_recurso']==1?$this->calcularTotalActividad($value['cantidad'], $value['costo_unitario'], $value['iva']):0),
                'tipoRecurso'=>$value['tipo_recurso'],
                'anexos'=>$this->procesarAnexo($value->anexos),
                'conIVA'=>$value['con_iva'],
                'toggleText'=>true,
                'placeHolderDescAct'=>$value->catactividad->actividad,#$this->limpiarHTML($value->catactividad->actividad, 42),
                'placeHolderDesc'=>$value['descripcion'],#$this->limpiarHTML($value['descripcion'], 42),
                'placeHolderEnt'=>$value->entregable->entregable,#$this->limpiarHTML($value->entregable->entregable, 42)
            ]);
            $this->totalComponente += $total;
        });
        return $actividades;
    }

    protected function procesarOficinas($acervo, $listado)
    {
        $oficinas = collect();

        $acervo->each(function ($value, $key) use ($oficinas, $listado) {
            if (!$oficinas->has($value['oficina'])) {
                $oficinas[$value['oficina']] = collect([
                    'actualizar'=>!$listado->contains('detalle_oficinas_id', $value['oficina']),
                    'nombre'=>$value->diagnostico->oficina,
                    'acervo'=>collect(),
                    'total'=>collect([
                        'concepto'=>'TOTAL',
                        'existente'=>0,
                        'digitalizado'=>0,
                        'porcentaje'=>0,
                        'librosLegajos'=>0,
                        'numImagenes'=>0
                    ]),
                    'id'=>$value['oficina']
                ]);
            }
            $oficinas[$value['oficina']]['total']['existente'] += is_numeric($value['acervo_existe']) ? $value['acervo_existe'] : 0;
            $oficinas[$value['oficina']]['total']['digitalizado'] += is_numeric($value['acervo_digitalizado']) ? $value['acervo_digitalizado'] : 0;
            $oficinas[$value['oficina']]['total']['porcentaje'] += is_numeric($value['porc_digitalizado']) ? $value['porc_digitalizado'] : 0;
            $oficinas[$value['oficina']]['total']['librosLegajos'] += is_numeric($value['libros_legajos']) ? $value['libros_legajos'] : 0;
            $oficinas[$value['oficina']]['total']['numImagenes'] += is_numeric($value['num_imagenes']) ? $value['num_imagenes'] : 0;

            $oficinas[$value['oficina']]['acervo']->push($value);
        });
        $lista = [];
        $oficinas->each(function ($value, $key) use (&$lista){
            $lista[] = $value;
        });
        return $lista;
    }

    protected function procesarPrograma($registros)
    {
        $programa = collect();
        $registros->each(function($value, $index) use ($programa) {
            $programa[$value['indice']] = explode(',', $value['meses']);
        });
        return $programa;
    }

    protected function procesarAnexo($registros)
    {
        $anexo = collect();
        $registros->each(function($value, $index) use (&$anexo) {
            $value['url']= $this->obtenerUrlDocumento($value['nombre']);

            if ($value['url']==='') {
                ModelAnexo::where('id_anexo', $value['id_anexo'])->delete();
                return true;
            }

            $value['nombre_anterior'] = mb_strtoupper($value['nombre_anterior']);
            $value['descripcion'] = mb_strtoupper($value['descripcion']);
            $anexo[$index] = $value;
        });
        return $anexo;
    }

    protected function procesarNombreComponente($nombre)
    {
        if (strpos($nombre,':')===FALSE) {
            return $nombre;
        }
        $componente = explode(':', $nombre);
        return isset($componente[1]) ? trim($componente[1]) : $nombre;
    }

    protected function calcularTotalActividad($cantidad, $costo, $iva)
    {

        $cantidad = is_numeric($cantidad) ? (float)$cantidad : 0;
        $costo = is_numeric($costo) ? (float)$costo : 0;
        $iva = is_numeric($iva) ? (float)$iva : 0;

        return round(($cantidad*$costo) + $iva, 2);
    }

    protected function calcularIva($cantidad, $costo)
    {
        $cantidad = is_numeric($cantidad) ? (float)$cantidad : 0;
        $costo = is_numeric($costo) ? (float)$costo : 0;

        return ($cantidad*$costo) * self::IVA;
    }

    protected function perteneceAvertiente($idComponente, $vertiente)
    {
        $componente = $this->componentesModelo->where('componentes_id', $idComponente)->first();
        if (!$componente) {
            return;
        }
        return in_array($componente->modelos_id, explode(',', $vertiente));
    }

    protected function obtenerVertiente($idComponente)
    {
        $componente = $this->componentesModelo->where('componentes_id', $idComponente)->first();
        if (!$componente) {
            return '';
        }
        return $componente->modelos_id;
    }

    protected function obtenerComponente($idComponente)
    {
        $componente = $this->componentesModelo->where('componentes_id', $idComponente)->first();
        if (!$componente) {
            return '';
        }
        return $componente;
    }

    protected function obtenerCarpeta()
    {
        return $this->carpeta;
    }

    protected function obtenerExtension($nombreDoc)
    {
        $doc = explode('.', $nombreDoc);
        return trim( end($doc) );
    }

    protected function obtenerUrlDocumento($nombreDoc)
    {
        $ruta = "/documentos/{$this->carpeta}/{$this->getAnio()}/anexos/{$nombreDoc}";
        return Storage::disk($this->env)->exists($ruta) ? asset("storage{$ruta}") : '';
    }

    protected function obtenerOficinasRegistrales($idDiagnostico, $capturas)
    {
        $acervo = new Conservacion($idDiagnostico);
        return $acervo->procesarListadoOficinas($acervo->obtenerOficinas(), $capturas);
    }

    protected function procesarRecurso($cantidad, $tipo = 1)
    {
        if ($tipo != 1) {
            return 0;
        }

        return is_numeric($cantidad) ? $cantidad : 0;
    }

    protected function limpiarHTML($cadena, $longitud=null)
    {
        $cadena = strip_tags($cadena);

        return (is_numeric($longitud) && $longitud>0) ? substr($cadena, 0, $longitud) . '...' : $cadena;
    }

    protected function estaHomologado($idComponente)
    {
        $homologado = false;
        foreach ($this->homologos  as $idComp=>$componente) {
            if ($this->homologos[$idComp]['id'] == $idComponente) {
                $homologado = true; break;
            }
        }
        return $homologado;
    }

    protected function obtenerIdHomologo($idComponente)
    {
        return isset($this->homologos[$idComponente]) ? $this->homologos[$idComponente]['id'] : $idComponente;
    }

}
