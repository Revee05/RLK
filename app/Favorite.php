<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\models\MerchProduct; 

class Favorite extends Model
{
    protected $table = 'favorites';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(MerchProduct::class, 'product_id');
    }
}
