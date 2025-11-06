<?php

namespace App\Models\Diagnostic\Sigcm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organizacion extends Model
{
    protected $connection = 'sigcm';
    protected $table = 'cat_dir_organizacion';
    protected $primaryKey = 'id_organizacion';

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estados_id', 'estados_id');
    }
}
