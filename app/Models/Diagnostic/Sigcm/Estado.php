<?php

namespace App\Models\Diagnostic\Sigcm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'tbl_entidad';
    protected $primaryKey = 'estados_id';

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'cvegeoedo', 'estados_id');
    }

    public function organizaciones(): HasMany
    {
        return $this->hasMany(Organizacion::class, 'estados_id', 'estados_id');
    }
}
