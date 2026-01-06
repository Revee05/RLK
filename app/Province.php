<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';
    
    protected $fillable = ['name'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Alias untuk backward compatibility dengan sistem lama (Provinsi)
     */
    public function getNamaProvinsiAttribute()
    {
        return $this->name;
    }
}
