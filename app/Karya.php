<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Karya extends Model
{
    protected $table = "karya";
    protected $fillable = [
        'name',
        'slug',
        'profession',
        'description',
        'bio',
        'social',
        'image',
        'address',
        'province_id',
        'city_id',
        'district_id',
    ];
    protected $casts = [
        'social' => 'array',

    ];
    
    public function product(){
        return $this->hasMany('App\Products');
    }
    
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
