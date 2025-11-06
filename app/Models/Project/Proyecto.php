<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Relations\{HasOne, HasMany};
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Proyecto extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_proyectos';
    protected $primaryKey = 'id_proyecto';

    public $timestamps = false;

    protected $guarded = [];

    public function versiones(): HasMany
    {
        return $this->hasMany(Version::class,'id_proyecto','id_proyecto');
    }

    public function status(): HasOne
    {
        return $this->hasOne(Estatus::class, 'id_estatus', 'estatus');
    }

    public function esEstatal()
    {
        return $this->id_app_diag==1;
    }

    public function getAnioAttribute()
    {
        if (!$this->fecha) {
            return date('Y');
        }
        return (int)(new Carbon($this->fecha))->format('Y');
    }
}
