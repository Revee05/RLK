<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = [];

    // 1. Relasi ke User
    public function user()
    {
        return $this->belongsTo('App\User'); 
    }

    // 2. Relasi ke Produk Lelang/Lama (Legacy)
    public function product()
    {
        return $this->belongsTo('App\Products', 'product_id'); 
    }

    // ==========================================
    // BAGIAN MERCHANDISE (DIPERBAIKI)
    // ==========================================

    // 3. Relasi ke Induk Merch (Baju Keren)
    public function merchProduct()
    {
        return $this->belongsTo('App\models\MerchProduct', 'merch_product_id');
    }

    // 4. [BARU] Relasi ke Varian Spesifik (Warna: Merah)
    // Pastikan kolom 'merch_product_variant_id' ada di tabel cart_items
    public function merchVariant()
    {
        return $this->belongsTo('App\models\MerchProductVariant', 'merch_product_variant_id');
    }

    // 5. [BARU] Relasi ke Ukuran Spesifik (Size: XL)
    // Pastikan kolom 'merch_product_variant_size_id' ada di tabel cart_items
    public function merchSize()
    {
        return $this->belongsTo('App\models\MerchProductVariantSize', 'merch_product_variant_size_id');
    }
}

