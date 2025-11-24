<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // $guarded = [] berarti semua kolom boleh diisi (selama ada di database).
    // Ini aman, tidak perlu diubah meski kolom product_id sudah hilang.
    protected $guarded = [];

    // 1. Relasi ke User
    public function user()
    {
        return $this->belongsTo('App\User'); 
    }

    // ==========================================
    // BAGIAN MERCHANDISE
    // ==========================================

    // 2. Relasi ke Induk Merch (Baju Keren)
    public function merchProduct()
    {
        // Pastikan path model sesuai (App\models atau App\Models)
        return $this->belongsTo('App\models\MerchProduct', 'merch_product_id');
    }

    // 3. Relasi ke Varian Spesifik (Warna: Merah)
    public function merchVariant()
    {
        return $this->belongsTo('App\models\MerchProductVariant', 'merch_product_variant_id');
    }

    // 4. Relasi ke Ukuran Spesifik (Size: XL)
    public function merchSize()
    {
        return $this->belongsTo('App\models\MerchProductVariantSize', 'merch_product_variant_size_id');
    }
}