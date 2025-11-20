<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_address';
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'desa_id',
        'kodepos',
        'label_address',
    ];
    
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public function provinsi(){
        return $this->belongsTo('App\Provinsi','provinsi_id');
    }
    public function kabupaten(){
        return $this->belongsTo('App\Kabupaten','kabupaten_id');
    }
    public function kecamatan(){
        return $this->belongsTo('App\Kecamatan','kecamatan_id');
    }
    // public function desa(){
    //     return $this->belongsTo('App\Desa','desa_id');
    // }
        
    
}
