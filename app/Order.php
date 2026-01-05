<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'label_address',
        'address',
        'products_id',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'product_id',
        'pengirim',
        'jenis_ongkir',
        'bid_terakhir',
        'total_ongkir',
        'asuransi_pengiriman',
        'total_tagihan',
        'orderid_uuid',
        'order_invoice',
        'payment_status',
        'status_pesanan',
        'nomor_resi',
        'snap_token',
        'created_at',
    ];
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public function product(){
        return $this->belongsTo('App\Products','product_id');
    }
    
    public function bid(){
        return $this->hasOne('App\Bid','product_id','product_id')->latest();
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
    public function gettanggalOrderAttribute()
    {
        return Carbon::parse($this->created_at)->isoFormat('dddd, D MMMM Y');
    }
    public function getstatusTxtAttribute()
    {
        if ($this->status == '1') {
            return '<span class="badge bg-info text-white rounded-1">Menunggu Pembayaran</span>';
        } elseif ($this->status == '2') {
            return '<span class="badge bg-success text-white rounded-1">Sudah dibayar</span>';
        } elseif ($this->status == '3') {
            return '<span class="badge bg-success text-white rounded-1">Kadaluarsa</span>';
        } else{
            return '<span class="badge bg-danger text-white rounded-1">Batal</span>';
        }
    }
}
