<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Casts\Attribute; // <-- KITA HAPUS INI KARENA INI UNTUK LARAVEL 8+

class Products extends Model
{
    protected $table = "products";
    protected $fillable = [
        'id',
        'user_id',
        'kategori_id',
        'karya_id',
        'title',
        'slug',
        'description',
        'price',
        'diskon',
        'stock',
        'sku',
        'weight',
        'asuransi',
        'long',
        'height',
        'width',
        'status',
        'kondisi',
        'type',
        'kelipatan',
        'end_date',
    ];

    /**
     * Penting: Pastikan end_date di-cast sebagai datetime
     * agar $this->end_date menjadi objek Carbon.
     */
    protected $casts = [
        'end_date' => 'datetime',
    ];

    // --- Relasi Anda (Sudah Benar) ---
    public function images(){
        return $this->hasMany('App\ProductImage','products_id');
    }

    public function imageUtama()
    {
        return $this->hasOne('App\ProductImage', 'products_id')->where('name', '=', 'img_utama');
    }
    public function imageDepan()
    {
        return $this->hasOne('App\ProductImage', 'products_id')->where('name', '=', 'img_depan');
    }
    public function imageSamping()
    {
        return $this->hasOne('App\ProductImage', 'products_id')->where('name', '=', 'img_samping');
    }
    public function imageAtas()
    {
        return $this->hasOne('App\ProductImage', 'products_id')->where('name', '=', 'img_atas');
    }
    
    public function kategori(){
        return $this->belongsTo('App\Kategori','kategori_id');
    }
    public function karya(){
        return $this->belongsTo('App\Karya','karya_id');
    }
    public function bid()
    {
     

      return $this->hasMany('App\Bid', 'product_id');
    }
    function kelengkapans()
    {
        return $this->belongsToMany(Kelengkapan::class)->withTimestamps();
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // --- ACCESSOR LAMA ANDA (SINTAKS SUDAH BENAR UNTUK LARAVEL 6) ---
    
    public function getPriceStrAttribute()
    {
        // Saya perbaiki format number, Anda menggunakan ('', '.') 
        // seharusnya (0, ',', '.') untuk pemisah ribuan
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
    
    public function getKelipatanBidAttribute()
    {
        return 'Rp ' . number_format($this->kelipatan, 0, ',', '.');
    }
    
    public function getEndDateIndoAttribute()
    {
        return Carbon::parse($this->end_date)->isoFormat('dddd, D MMMM Y H:mm:s');
    }
    
    public function getStatusTxtAttribute()
    {
        if ($this->status == '1') {
            return '<span class="badge bg-info text-white rounded-0">PUBLISHED</span>';
        } elseif ($this->status == '2') {
            return '<span class="badge bg-danger text-white rounded-0">SOLD OUT</span>';
        } elseif ($this->status == '3') {
            return '<span class="badge bg-success text-white rounded-0">LELANG EXPIRED</span>';
        } else{
            return '<span class="badge bg-warning text-white rounded-0">DRAFT</span>';
        }
    }
    
    // --- ACCESSOR BARU (UNTUK DISKON & JS) DALAM SINTAKS LARAVEL 6 ---
    
    /**
     * (BARU)
     * Menghitung harga final (setelah diskon) sebagai angka.
     * Dipanggil di Blade via: $produk->final_price
     */
    public function getFinalPriceAttribute()
    {
        if ($this->diskon > 0) {
            $diskon = $this->diskon / 100;
            $newPrice = $this->price - ($diskon * $this->price); 
            return $newPrice;
        }
        return $this->price; // Jika tidak ada diskon, kembalikan harga asli
    }

    /**
     * (BARU)
     * Mendapatkan harga final dalam format string Rupiah.
     * Dipanggil di Blade via: $produk->final_price_str
     */
    public function getFinalPriceStrAttribute()
    {
        // $this->final_price akan otomatis memanggil getFinalPriceAttribute() di atas
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * (BARU)
     * Pengecekan boolean apakah produk memiliki diskon.
     * Dipanggil di Blade via: $produk->has_diskon
     */
    public function getHasDiskonAttribute()
    {
        return $this->diskon > 0;
    }
    
    /**
     * (BARU)
     * Mendapatkan tanggal akhir lelang dalam format ISO 8601
     * (Dibutuhkan oleh JavaScript untuk countdown timer).
     * Dipanggil di Blade via: $produk->end_date_iso
     */
    public function getEndDateIsoAttribute()
    {
        // Ini akan berfungsi jika 'end_date' ada di $casts
        return $this->end_date ? $this->end_date->toIso8601String() : null;
    }

    public function favorites()
{
    return $this->hasMany(Favorite::class);
}


}