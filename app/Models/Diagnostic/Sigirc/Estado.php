<?php

namespace App\Models\Diagnostic\Sigirc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    protected $connection = 'sigirc';
    protected $table = 'tbl_entidad';
    protected $primaryKey = 'estados_id';

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'estado_id', 'estados_id');
    }

    public function organizaciones(): HasMany
    {
        return $this->hasMany(Organizacion::class, 'estados_id', 'estados_id');
    }
}
