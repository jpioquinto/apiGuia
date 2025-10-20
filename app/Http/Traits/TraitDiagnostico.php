<?php

namespace App\Http\Traits;

use App\Models\Diagnostic\Sigirc\{Sigirc, CatalogoEstatus};
use App\Models\Diagnostic\Sigcm\Sigcm;
use App\Models\Diagnostic\Diagnostico AS ModelDiagnostic;
use Illuminate\Support\Facades\Log;
trait TraitDiagnostico
{
    static $estatusParams = [];

    protected $modeloDiagnostico;
    protected $proyecto;
    protected $vertiente;
    protected $diagnostico;

    public function obtenerModeloDiagnostico($idProyecto=0)
    {
        if ($idProyecto==0 || $this->proyecto==null) {
            return new Sigirc;
        }
        return $this->proyecto->id_app_diag==1 ? new Sigirc : new Sigcm;
    }
    public function diagnosticoEntidad(int $idDiagnostico)
    {
        $this->inicializaDiagnostico($this->consultarDiagnostico($idDiagnostico));
        return $this->obtenerDiagnostico();
    }
    protected function obtenUltimoDiagnostico()
    {
        return Sigirc::obtenerDiagnosticoActual(auth()->user()->directorio->id_organizacion);

    }
    protected function consultarDiagnostico(int $idDiagnostico)
    {
        return $this->modeloDiagnostico::with(['detalles','detalles.tabla'])
            ->where('diagnosticos_id',$idDiagnostico)
            ->first();
    }
    public function obtenerDiagnostico()
    {
        return $this->diagnostico;
    }
    public function obtenerIdDiagnostico()
    {
        return $this->proyecto->id_diagnostico ?? $this->obtenUltimoDiagnostico();
    }
    public function obtenerAnioDiagnostico()
    {
        return $this->diagnostico->ano_proyecto;
    }
    public function obtenerVertienteInicial()
    {
        return $this->proyecto->vertiente ?? auth()->user()->directorio->organizacion->vertiente_id;
    }
    public function obtenerVertiente()
    {
        return $this->vertiente;
    }
    public function inicializaVertiente($vertiente)
    {
        $this->vertiente = $vertiente;
    }
    public function inicializaDiagnostico(ModelDiagnostic $diagnostico = null)
    {
        $this->diagnostico = $diagnostico ?? $this->consultarDiagnostico( $this->obtenerIdDiagnostico() );
    }
    function obtenerEstatus($anio=0)
	{
        if ($this->modeloDiagnostico && $this->obtenerFuente(get_class($this->modeloDiagnostico))=="SIGCM") {
            return 1;
        }
		return $this->encontrarEstatus(($anio==0) ? date('Y') : $anio);
	}
	protected function encontrarEstatus($anio)
	{
        $estatus = 0;
        if (count(self::$estatusParams)==0) {
			self::$estatusParams = CatalogoEstatus::get();
		}
        self::$estatusParams->each(function ($value, $key) use ($anio, &$estatus) {
            if (is_numeric( $estatus=$this->buscarEnPeriodo($value, $anio) )) {
                return false;
            }
        });
		return is_numeric($estatus) ? $estatus : 0;
	}
	protected function buscarEnPeriodo($periodo, $anio)
	{
		if (is_numeric($periodo['termina'])) {
			return $this->periodoTerminado($periodo, $anio);
		}
		return $this->periodoInicial($periodo, $anio);
	}
	protected function periodoTerminado($periodo, $anio)
	{
		if ($anio>=$periodo['inicia'] && $anio<=$periodo['termina']) {
			return $periodo['estatus'];
		}
		return;
	}
	protected function periodoInicial($periodo, $anio)
	{
		if ($anio>=$periodo['inicia']) {
			return $periodo['estatus'];
		}
		return;
	}
	protected function obtenerFuente($clase)
	{
        $clase = explode("\\", $clase);
        return strtoupper(end($clase));
	}
}
