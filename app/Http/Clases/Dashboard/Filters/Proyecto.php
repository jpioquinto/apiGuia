<?php

namespace App\Http\Clases\Dashboard\Filters;

use App\Models\Project\{Proyecto as ModelProyecto, ProjectQueryBuilder as ProjectQuery};
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
class Proyecto implements Filtro
{
    protected int $edoId;
    protected int $anio;

    public function __construct(int $anio, int $edoId)
    {
        $this->setEdoId($edoId);
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

    public function setEdoId(int $edoId): void
    {
        $this->edoId = $edoId;
    }

    public function getEdoId(): int
    {
        return $this->edoId;
    }

    public function listarResultado(): array
    {
        $listado = $this->procesarResultado($this->realizarConsulta());
        $listado = $listado->sort(function (array $projectA, array $projectB) {
                        $vertienteA = 1;
                        if ($projectA['vertiente']) {
                            $vertienteA = $projectA['vertiente'] === '1,2' ? 3 : (int)$projectA['vertiente'];
                        }

                        $vertienteB = 1;
                        if ($projectB['vertiente']) {
                            $vertienteB = $projectB['vertiente'] === '1,2' ? 3 : (int)$projectB['vertiente'];
                        }

                        return ($vertienteA > $vertienteB) && $projectA['estatal'] && $projectB['estatal'];
                    });

        if (in_array($this->getAnio(), [2016, 2017])) {
            $municipales = $this->procesarResultado($this->realizarConsulta('sigcm'), 'sigcm');
            $municipales = $municipales->sortBy('descVertiente');
            $listado = $listado->merge($municipales);
        }

        return $listado->values()->all();
    }

    protected function procesarResultado(object $registros, string $organizacion = 'organizacion'): object
    {
        $listado = collect();
        $registros->each(function ($value, $key) use (&$listado, $organizacion) {
            if (!isset($value[$organizacion])) {
                return true;
            }

            $listado->push([
                'id'=>$value->id_proyecto,
                'vertiente'=>$value->vertiente,
                'diagnosticoId'=>$value->id_diagnostico,
                'appDiag'=>$value->id_app_diag,
                'millar'=>$value->millar ?: 0,
                'porcFed'=>$value->porc_fed ?: 0,
                'porcEst'=>$value->porc_est ?: 0,
                'descProyecto'=>$this->descProyecto($value),
                'descVertiente'=>$this->descVertiente($value),
                'estatal'=>$value->esEstatal(),
                'estatus'=>$value->estatus,
                'version'=>$value->version,
                'anio'=>$value->anio,
            ]);
        });

        return $listado;
    }

    protected function descProyecto(object $proyecto): string
    {
        if ($proyecto->esEstatal()) {
            return $proyecto->organizacion->estado->estado . ' ' .$proyecto->desc_vertiente;
        }

        if (isset($proyecto->organizacion)) {
            return $proyecto->organizacion->municipio->municipio . ', ' . $proyecto->organizacion->estado->abreviatura . ' ' . $proyecto->desc_vertiente;
        }

        return $proyecto->municipio . ' ' . $proyecto->desc_vertiente;
    }

    protected function descVertiente(object $proyecto): string
    {
        return (!$proyecto->esEstatal() ? "{$proyecto->municipio} " : "") . $proyecto->desc_vertiente . " - ({$proyecto->status->descripcion})";
    }

    protected function realizarConsulta(string $organizacion = 'organizacion'): object
    {
        $appDiag = $organizacion == 'sigcm' ? 2 : 1;

        return ModelProyecto::with([
                        $organizacion => function (Builder $query) {
                            $query->where('estados_id', $this->getEdoId());
                        }
                    ])
            ->whereRaw(sprintf("year(fecha) = %d", $this->getAnio()))
            ->where('estatus', '>', 0)
            ->where('id_app_diag', $appDiag)
            ->get();
    }
}
