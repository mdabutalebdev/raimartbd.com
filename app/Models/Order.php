<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'delivery_area',
        'notes',
        'subtotal',
        'coupon_code',
        'discount',
        'shipping_fee',
        'total',
        'status',
        'courier_name',
        'tracking_number',
        'admin_note',
        'payment_method',
        'payment_status',
        'ga_client_id',
        'ga_session_id',
        'purchase_tracked_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'purchase_tracked_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaymentMethodNameAttribute()
    {
        if ($this->payment_method === 'cod') {
            return 'Cash on Delivery';
        }

        if ($this->payment_method === 'piprapay') {
            $payment = $this->payments()->where('status', 'completed')->latest()->first();
            if ($payment && isset($payment->raw_response['payment_method'])) {
                return $payment->raw_response['payment_method'];
            }
            if ($payment && isset($payment->raw_response['payment_network'])) {
                return $payment->raw_response['payment_network'];
            }
            return 'Online Payment';
        }

        return strtoupper($this->payment_method);
    }
}
