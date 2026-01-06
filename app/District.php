<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';
    
    protected $fillable = ['name', 'city_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Alias untuk backward compatibility dengan sistem lama (Kecamatan)
     */
    public function getNamaKecamatanAttribute()
    {
        return $this->name;
    }
}
