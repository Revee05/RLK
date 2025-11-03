<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';
    protected $fillable = [
        'nama_kabupaten',
        'provinsi_id',
    ];

    public function provinsi(){
        return $this->belongsTo('App\Provinsi','provinsi_id');
    }
}
