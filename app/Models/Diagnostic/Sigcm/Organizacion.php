<?php

namespace App\Models\Diagnostic\Sigcm;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\Model;
use App\Models\Project\Proyecto;

class Organizacion extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'cat_dir_organizacion';
    protected $primaryKey = 'id_organizacion';

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estados_id', 'estados_id');
    }

    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'id_organizacion', 'id_organizacion');
    }

}
