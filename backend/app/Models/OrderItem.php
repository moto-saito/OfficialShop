<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // 商品が削除されても注文履歴は product_name/price で保持しているため nullable
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '¥' . number_format($this->price);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '¥' . number_format($this->subtotal);
    }
}
