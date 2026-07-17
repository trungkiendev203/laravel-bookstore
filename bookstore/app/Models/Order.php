<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'note',
        'shipping_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'retry_count',
    ];

    protected $casts = [
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Một đơn hàng thuộc về một người dùng (nếu đã đăng nhập)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Một đơn hàng có nhiều chi tiết mặt hàng
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
