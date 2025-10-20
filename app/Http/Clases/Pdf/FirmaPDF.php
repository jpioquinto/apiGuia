<?php

namespace App\Http\Clases\Pdf;

use App\Http\Clases\{Proyecto, Usuario};

class FirmaPDF
{
    protected $proyecto;

    protected $emisor;

    protected $certificador;

    const _URL_ = "images/temp/";

    public function __construct(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;

        $this->emisor        = new Usuario($proyecto->getIdFirmante(), $proyecto->getAppDiagnostico());

        $this->certificador  = new Usuario($proyecto->getIdCertificador(), $proyecto->getAppDiagnostico());
    }

    public function vista()
    {
        if (in_array($this->proyecto->getId(), [95, 123])) {
            return $this->vistaFirmaAutografa();
        }

        return view(
            "reports.project.partial.tabla_firmas_v2",
            [
                'institucion'=>$this->proyecto->getInstitucion(),
                'qrEmisor'=>$this->proyecto->estaFirmado() ? self::_URL_."doc_xml_{$this->proyecto->getId()}.png" : self::_URL_."doc_xml_.png",
                'emisor'=>$this->emisor->getNombreCompleto(),
                'selloEmisor'=>$this->proyecto->getSelloDigital(),
                'urSEDATU'=>$this->proyecto->getAnio()>2019 ? $this->certificador->getInstitucion() : 'Coordinación General de Modernización y Vinculación Registral y Catastral',
                'qrCertificador'=>$this->proyecto->estaFirmado() ? self::_URL_."doc_txt_{$this->proyecto->getId()}.png" : self::_URL_."doc_txt_.png",
                'certificador'=>$this->certificador->getNombreCompleto(),
                'selloCertificador'=>$this->proyecto->getSelloCertificacion(),
                'cadenaOriginal'=>$this->proyecto->getCadenaOriginal(),
            ]
        );

    }

    public function vistaFirmaAutografa()
    {

        return view(
            "reports.project.partial.tabla_firmas_v1",
            [
                'titular'=>$this->proyecto->getId()==123 ? 'LIC. GISELA ACUÑA BAÑUELOS' : 'DUA. CARLOS AUGUSTO FLORES PÉREZ',
                'cargoTitular'=>$this->proyecto->getId()==123 ? 'TESORERA MUNICIPAL' : 'JEFE DEL DEPARTAMENTO DE CATASTRO MUNICIPAL',
                'dictaminador'=>$this->proyecto->getId()==123 ? 'ING. JESÚS ALBERTO ROMERO JUÁREZ"' : 'GEÓG. ARMANDO HERRERA REYES',
                'cargoDictaminador'=>$this->proyecto->getId()==123 ? 'DIRECTOR DE INTEGRACIÓN DOCUMENTAL' : 'DIRECTOR GENERAL ADJUNTO DE EVALUACIÓN Y SEGUIMIENTO',
            ]
        );
    }

}
