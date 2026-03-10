<?php

namespace App\Http\Clases\Signature\Data;

use Illuminate\Support\Facades\Config;
use App\Http\Clases\Proyecto;
use DomDocument;

class ProyectoXML implements Documento
{
    protected Proyecto $proyecto;
    protected $xml;

    public function __construct(Proyecto $proyecto)
    {
        $this->setProyecto($proyecto);
    }

    public function setProyecto(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    }

    public function create()
    {
        $this->xml = new DomDocument('1.0', 'UTF-8');

        $proyectoNode = $this->iniDocumento($this->xml);
        $proyectoNode = $this->seguimientoDocumento($this->xml, $proyectoNode);

        $this->xml->appendChild($proyectoNode);

        return $this->xml->saveXML();
    }

    protected function iniDocumento(DomDocument $xml)
    {
        $proyectoNode = $xml->createElement('Proyecto');
        $proyectoNode->setAttribute('elaborado', $this->proyecto->getFechaCreacion());
        $proyectoNode->setAttribute('firmado', date('Y-m-d H:i:s'));
        $proyectoNode->setAttribute('version', $this->proyecto->getUltimaVersion());
        $proyectoNode->setAttribute('fechaVersion', $this->proyecto->getFechaUltimaModifcacion());
        $proyectoNode->setAttribute('entidad', $this->proyecto->getEntidad());
        $proyectoNode->setAttribute('institucion', $this->proyecto->getInstitucion());
        $proyectoNode->setAttribute(
                    'lugarExpedicion',
                    'Av. Nuevo León, No. 210, Hipódromo Condesa, C.P. 06100, Cuauhtémoc. Ciudad de México.'
                    );
        $appNode = $xml->createElement('App', 'Guía para la Elaboración de Proyectos de Modernización');
        $appNode->setAttribute('url', Config::get('app.url'));
        $appNode->setAttribute('version', '2.0.11');
        $proyectoNode->appendChild($appNode);
        return $proyectoNode;
    }

    protected function seguimientoDocumento(DomDocument $xml, \DOMElement $proyectoNode)
    {
        $proyectoNode->appendChild($xml->createElement('Introduccion', $this->proyecto->seguimiento->getIntroduccion()));
        $proyectoNode->appendChild($xml->createElement('SituacionGral', $this->proyecto->seguimiento->getSituacion()));
        $proyectoNode->appendChild($xml->createElement('LogroAplicacion', $this->proyecto->seguimiento->getLogro()));
        $proyectoNode->appendChild($xml->createElement('ObjetivoGral', $this->proyecto->seguimiento->getObjetivo()));
        return $proyectoNode;
    }

    protected function desarrolloDocumento(DomDocument $xml, \DOMElement $proyectoNode)
    {
        $componentes = $this->proyecto->desarrollo->obtenerComponentes();
        $devNode = $xml->createElement('Desarrollo');
        $componentes->each(function ($componente) use ($xml, &$proyectoNode) {
            $componenteNode = $xml->createElement('Componente');
            $componenteNode->setAttribute('nombre', $componente->nombre);
            $componenteNode->appendChild($xml->createElement('SituacionActual', $componente->situacion));
            $componenteNode->appendChild($xml->createElement('ObjectivosAlcance', $componente->objectivos));
            $ActividadesNode = $xml->createElement('Actividades');
            $proyectoNode->appendChild($componenteNode);
        });

        return $proyectoNode;
    }

    protected function actividadesDocumento(DomDocument $xml, \DOMElement $componenteNode, $actividades)
    {
        $actividades->each(function ($actividad) use ($xml, &$componenteNode) {
            $actividadNode = $xml->createElement('Actividad');
            $actividadNode->setAttribute('entregable', $actividad->descEntregable);
            $actividadNode->setAttribute('unidad', $actividad->descUnidad);
            $actividadNode->setAttribute('tipoRecurso', $actividad->tipoRecurso===1 ? 'Recurso del proyecto' : 'Recurso propio');

            $importe = is_numeric($actividad->cantidad) && is_numeric($actividad->costo) ? $actividad->cantidad * $actividad->costo : 0;

            $actividadNode->appendChild($xml->createElement('SubComponente', $actividad->descSubcomp));
            $actividadNode->appendChild($xml->createElement('CatActividad', $actividad->descAct));
            $actividadNode->appendChild($xml->createElement('CatSubActividad', $actividad->descSubAct));
            $actividadNode->appendChild($xml->createElement('Descripcion', $actividad->desc));
            $actividadNode->appendChild($xml->createElement('Cantidad', $actividad->cantidad));
            $actividadNode->appendChild($xml->createElement('CostoUnitario', $actividad->costo));
            $actividadNode->appendChild($xml->createElement('Importe', $importe));
            $actividadNode->appendChild($xml->createElement('IVA', $actividad->iva));
            $actividadNode->appendChild($xml->createElement('Total', $actividad->total));

            $componenteNode->appendChild($actividadNode);
        });

        return $componenteNode;
    }
}
