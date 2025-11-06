<?php

namespace App\Models\Project\Situation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoSubcomponente extends Model
{
    protected $connection = 'guia';
    protected $table = 'cat_subcomponentes';
    protected $primaryKey = 'id_subcomponente';

    public function actividades(): HasMany
    {
        return $this->hasMany(CatalogoActividad::class,'id_subcomponente','id_subcomponente');
    }
}
