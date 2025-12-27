<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'tracking_number',
        'buyer_confirmed_at',
        'voucher_id',
        'discount_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'buyer_confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isUnpaid()
    {
        return $this->payment_status === 'unpaid';
    }

    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }

    public function isConfirmed()
    {
        return $this->buyer_confirmed_at !== null;
    }

    public function hasTracking()
    {
        return $this->tracking_number !== null;
    }
}
