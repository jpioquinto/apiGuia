<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Diagnostic\Sigirc\Oficina as OficinaDiagnostico;

class Oficina extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_oficinas_registrales';
    protected $primaryKey = 'id_oficina_reg';

    public $timestamps = false;

    protected $guarded = [];

    public function diagnostico(): BelongsTo
    {
        return $this->belongsTo(OficinaDiagnostico::class, 'oficina', 'detalle_oficinas_id');
    }
}
