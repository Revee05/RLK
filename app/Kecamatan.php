<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    protected $fillable = [
        'nama_kecamatan',
        'kabupaten_id',
    ];

    public function kabupaten(){
        return $this->belongsTo('App\Kabupaten','kabupaten_id');
    }
}
