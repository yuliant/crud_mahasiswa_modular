<?php

namespace Modules\Prodi\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Mahasiswa\Entities\Mahasiswa;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $table = 'prodi';

    public $timestamps = false;

    protected $fillable = ['nama_prodi'];

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'id_prodi');
    }
}
