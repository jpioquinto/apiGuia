<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Relations\{HasOne, HasMany};
use Illuminate\Database\Eloquent\Model;

class Desarrollo extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_desarrollo_proyecto';
    protected $primaryKey = 'id_desarrollo';

    public $timestamps = false;

    protected $guarded = [];

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'id_desarrollo', 'id_desarrollo');
    }

    public function objetivos(): HasMany
    {
        return $this->hasMany(Objetivo::class, 'id_desarrollo', 'id_desarrollo');
    }

    public function programas(): HasMany
    {
        return $this->hasMany(Programa::class, 'id_desarrollo', 'id_desarrollo');
    }

    public function oficinas(): HasMany
    {
        return $this->hasMany(Oficina::class, 'id_desarrollo', 'id_desarrollo');
    }
}
