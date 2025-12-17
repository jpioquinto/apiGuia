<?php

namespace App\Http\Clases\Dashboard\Filters;

use App\Models\Project\{Proyecto, ProjectQueryBuilder as ProjectQuery};
use Illuminate\Contracts\Database\Eloquent\Builder;

class Entidad implements Filtro
{
    protected int $anio;

    protected object $listado;

    public function __construct(int $anio)
    {
        $this->listado = collect();
        $this->setAnio($anio);
    }

    public function setAnio(int $anio): void
    {
        $this->anio = $anio;
    }

    public function getAnio(): int
    {
        return $this->anio;
    }

    public function listarResultado(): array
    {#return $this->procesarResultado($this->realizarConsulta())->toArray();
        $listado = $this->listado = $this->procesarResultado($this->realizarConsulta());
        if (in_array($this->getAnio(), [2016, 2017])) {
            $listado = $listado->merge($this->procesarResultado($this->realizarConsulta('sigcm'), 'sigcm'));
        }

        $listadoOrdenado = $listado->sortBy('entidad');

        return $listadoOrdenado->values()->all();
    }

    protected function procesarResultado(object $registros, string $organizacion = 'organizacion')
    {
        $listado = collect();
        $registros->each(function($value, $key) use (&$listado, $organizacion) {
            if (!isset($value[$organizacion])) {
                return true;
            }
            if ($value[$organizacion]->estado && !$this->existeElemento($listado, $value[$organizacion]->estado->estados_id)) {
                $listado->push([
                    'id'=>$value[$organizacion]->estado->estados_id,
                    'entidad'=>$value[$organizacion]->estado->estado,
                    'escudo'=>$value[$organizacion]->estado->escudo,
                    'edoIso'=>$value[$organizacion]->estado->estado_iso,
                    'poblacion'=>$value[$organizacion]->estado->poblacion,
                    'extTerritorial'=>$value[$organizacion]->estado->extension_territorial,
                    'distUrbana'=>$value[$organizacion]->estado->distribucion_urbana,
                    'distRural'=>$value[$organizacion]->estado->distribucion_rural,
                    'abreviatura'=>$value[$organizacion]->estado->abreviatura
                ]);
            }
        });
        return $listado;
    }

    protected function realizarConsulta(string $organizacion = 'organizacion'): object
    {
        $appDiag = $organizacion == 'sigcm' ? 2 : 1;

        return Proyecto::with([$organizacion])
            ->whereRaw(sprintf("year(fecha) = %d", $this->getAnio()))
            ->where('estatus', '>', 0)
            ->where('id_app_diag', $appDiag)
            ->get();
    }

    protected function existeElemento(object $elementos, int $id): bool
    {
        return $elementos->contains('id', $id) || $this->listado->contains('id', $id);
    }
}
