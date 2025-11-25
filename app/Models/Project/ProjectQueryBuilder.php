<?php

namespace App\Models\Project;

use Illuminate\Support\Facades\DB;

class ProjectQueryBuilder
{
    public static string $table = 'tbl_proyectos';

    public static function listYearProjects()
    {
        return DB::connection('guia')
                ->table(self::$table . " as p")
                ->leftJoin(self::$table . " as pc", function ($join) {
                    $join->on('pc.id_proyecto', '=', 'p.id_proyecto')
                        ->where('pc.vertiente', '=', '1');
                })
                ->leftJoin(self::$table . " as pr", function ($join) {
                    $join->on('pr.id_proyecto', '=', 'p.id_proyecto')
                        ->where('pr.vertiente', '=', '2');
                })
                ->leftJoin(self::$table . " as pi", function ($join) {
                    $join->on('pi.id_proyecto', '=', 'p.id_proyecto')
                        ->where('pi.vertiente', '=', '1,2');
                })
                ->selectRaw('YEAR(p.fecha) AS anio, count(pc.vertiente) as pemc, count(pr.vertiente) as pemr, count(pi.vertiente) as pemi, count(p.vertiente) as total')
                ->where('p.estatus', '>', 0)
                ->groupBy('anio')
                ->orderBy('anio', 'DESC')
                ->get();
    }

}
