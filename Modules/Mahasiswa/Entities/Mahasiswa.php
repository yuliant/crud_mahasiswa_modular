<?php

namespace Modules\Mahasiswa\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Prodi\Entities\Prodi;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    public $timestamps = false;

    protected $fillable = ['id_prodi', 'nama', 'nim'];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }
}
