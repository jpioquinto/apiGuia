<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $connection = 'guia';
    protected $table = 'tbl_versiones';
    protected $primaryKey = 'id_version';

    public $timestamps = false;

    protected $guarded = [];

    public function seguimiento()
    {
        return $this->hasOne(Seguimiento::class, 'id_version', 'id_version')->withDefault();
    }
    public function desarrollo()
    {
        return $this->hasMany(Desarrollo::class,'id_version','id_version');
    }
}
