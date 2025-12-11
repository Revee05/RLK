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
        'province_id',
        'city_id',
        'district_id',
        'desa_id',
        'kodepos',
        'label_address',
        'is_primary',
    ];
    
    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public function province(){
        return $this->belongsTo('App\Province','province_id');
    }
    public function city(){
        return $this->belongsTo('App\City','city_id');
    }
    public function district(){
        return $this->belongsTo('App\District','district_id');
    }
    // public function desa(){
    //     return $this->belongsTo('App\Desa','desa_id');
    // }


}
