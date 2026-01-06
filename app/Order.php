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
        // Unified fields
        'payment_method',
        'payment_channel',
        'payment_destination',
        'stock_reduced',
        'email_sent',
        'payment_url',
        'paid_at',
        'note',
        'address_id',
        'shipper_id',
        'items',
        'shipping',
        'gift_wrap',
        'status',
        'invoice',
    ];

    protected $casts = [
        'items' => 'array',
        'shipping' => 'array',
        'gift_wrap' => 'boolean',
        'stock_reduced' => 'boolean',
        'email_sent' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public function product(){
        return $this->belongsTo('App\Products','product_id');
    }
    
    public function address(){
        return $this->belongsTo('App\UserAddress','address_id');
    }
    
    public function shipper(){
        return $this->belongsTo('App\Shipper','shipper_id');
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

    // Backward compatibility: payment_status → status
    public function getPaymentStatusAttribute()
    {
        // Jika field baru 'status' ada, convert ke numeric untuk backward compat
        if (!empty($this->attributes['status'])) {
            $statusMap = [
                'pending' => 1,
                'success' => 2,
                'expired' => 3,
                'cancelled' => 4,
            ];
            return $statusMap[$this->attributes['status']] ?? 1;
        }
        
        // Jika tidak ada, return nilai asli dari payment_status
        return $this->attributes['payment_status'] ?? 1;
    }

    // Backward compatibility: order_invoice → invoice
    public function getOrderInvoiceAttribute()
    {
        // Prioritas: invoice (baru) → order_invoice (lama)
        return $this->attributes['invoice'] ?? $this->attributes['order_invoice'] ?? '';
    }

    public function getstatusTxtAttribute()
    {
        // Gunakan field 'status' (string) jika ada, fallback ke payment_status (numeric)
        $status = $this->attributes['status'] ?? null;
        
        if ($status) {
            switch ($status) {
                case 'success':
                    return '<span class="badge bg-success text-white rounded-1">Sudah dibayar</span>';
                case 'expired':
                    return '<span class="badge bg-danger text-white rounded-1">Kadaluarsa</span>';
                case 'cancelled':
                    return '<span class="badge bg-danger text-white rounded-1">Batal</span>';
                case 'pending':
                default:
                    return '<span class="badge bg-info text-white rounded-1">Menunggu Pembayaran</span>';
            }
        }
        
        // Fallback untuk data lama (payment_status numeric)
        $paymentStatus = $this->attributes['payment_status'] ?? 1;
        if ($paymentStatus == '1') {
            return '<span class="badge bg-info text-white rounded-1">Menunggu Pembayaran</span>';
        } elseif ($paymentStatus == '2') {
            return '<span class="badge bg-success text-white rounded-1">Sudah dibayar</span>';
        } elseif ($paymentStatus == '3') {
            return '<span class="badge bg-success text-white rounded-1">Kadaluarsa</span>';
        } else{
            return '<span class="badge bg-danger text-white rounded-1">Batal</span>';
        }
    }
}
