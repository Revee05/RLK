<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'desa';
    protected $fillable = [
        'nama_desa',
        'kecamatan_id',
        'kodepos',
    ];

    public function kecamatan(){
        return $this->belongsTo('App\Kecamatan','kecamatan_id');
    }
}
