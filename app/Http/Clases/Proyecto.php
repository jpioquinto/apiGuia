<?php

namespace App\Http\Clases;

use App\Models\Diagnostic\Sigirc\Organizacion AS OrgSigirc;
use App\Models\Diagnostic\Sigcm\Organizacion AS OrgSigcm;
use App\Models\Project\Proyecto AS Modelo;

class Proyecto
{
    protected $modelo;

    protected $ultimaVersion;

    protected $alMillar;

    public $seguimiento;

    public $diagnostico;

    public $desarrollo;

    public $organizacion;

    public function __construct(Modelo $modelo)
    {
        $this->setModelo($modelo);

        $this->setVersion($modelo->versiones->where('version', $modelo->versiones->max('version'))->first());

        $this->seguimiento = new Seguimiento( $this->getVersion() );

        $this->diagnostico = new Diagnostico($this->getIdDiagnostico(), $this->getAppDiagnostico());

        $this->organizacion = new Organizacion($this->getAppDiagnostico()==1 ? new OrgSigirc : new OrgSigcm, $this->getIdOrganizacion());

        $this->desarrollo   = new Desarrollo(
                                    $this->ultimaVersion->desarrollo ?? collect(),
                                    $this->diagnostico->getComponentes($this->getVertiente()),
                                    $this->getIdDiagnostico(),
                                    $this->getCarpeta()
                                );
        $this->alMillar     = 1;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setVersion($version)
    {
        $this->ultimaVersion = $version;
    }

    public function getEstatus()
    {
        return $this->modelo->estatus;
    }

    public function getDescripcionEstatus()
    {
        return $this->modelo->status->descripcion;
    }

    public function getVersion()
    {
        return $this->ultimaVersion;
    }

    public function getUltimaVersion()
    {
        return $this->ultimaVersion->version;
    }

    public function getIdUltimaVersion()
    {
        return $this->ultimaVersion->id_version;
    }

    public function getId()
    {
        return $this->modelo->id_proyecto;
    }

    public function getIdDiagnostico()
    {
        return $this->modelo->id_diagnostico;
    }

    public function getIdComplemento()
    {
        return $this->modelo->id_diagcomplemento;
    }

    public function getIdOrganizacion()
    {
        return $this->modelo->id_organizacion;
    }

    public function getIdFirmante()
    {
        return $this->modelo->id_firmante;
    }

    public function getIdCertificador()
    {
        return $this->modelo->id_certificador;
    }

    public function getSelloDigital()
    {
        return $this->modelo->sello_digital;
    }

    public function getSelloCertificacion()
    {
        return $this->modelo->sello_certificacion;
    }

    public function getCadenaOriginal()
    {
        return $this->modelo->cadena_original;
    }

    public function getMillar()
    {
        return isset($this->modelo->millar) && is_numeric($this->modelo->millar) ? (float)$this->modelo->millar : 0;
    }

    public function getAlMillar()
    {
        return $this->alMillar;
    }

    public function getFechaCreacion()
    {
        return $this->modelo->fecha;
    }

    public function getFechaUltimaModifcacion()
    {
        return  $this->ultimaVersion->fecha_creacion;
    }

    public function getAnio()
    {
        return $this->modelo->anio;
    }

    public function getVertiente()
    {
        return $this->modelo->vertiente;
    }

    public function getAppDiagnostico()
    {
        return $this->modelo->id_app_diag;
    }

    public function getInstitucion()
    {
        return $this->organizacion->getNombre();
    }

    public function getCarpeta()
    {
        return $this->organizacion->getCarpeta();
    }

    public function getSubCarpeta()
    {
        return '';
    }

    public function estaFirmado()
    {
        return in_array($this->modelo->estatus, [5, 11]);
    }

    public function esIntegral()
    {
        return strcmp($this->getVertiente(), '1,2') == 0;
    }
}
