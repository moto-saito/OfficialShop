<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        'status',
        'payment_status',
        'recipient_name',
        'postal_code',
        'prefecture',
        'address',
        'phone_number',
        'email',
    ];

    // ─── リレーション ──────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── ステータスラベル ───────────────────────────────

    /** 注文ステータスの日本語ラベルを返す */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '受付中',
            'processing' => '準備中',
            'shipped'    => '発送済',
            'completed'  => '完了',
            'cancelled'  => 'キャンセル',
            default      => $this->status,
        };
    }

    /** 注文ステータスに応じたバッジ色クラスを返す */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'bg-yellow-100 text-yellow-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'shipped'    => 'bg-indigo-100 text-indigo-700',
            'completed'  => 'bg-green-100 text-green-700',
            'cancelled'  => 'bg-gray-100 text-gray-500',
            default      => 'bg-gray-100 text-gray-500',
        };
    }

    /** 決済ステータスの日本語ラベルを返す */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => '未払い',
            'paid'   => '支払済',
            default  => $this->payment_status,
        };
    }

    /** 合計金額をフォーマット済みで返す */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '¥' . number_format($this->total_price);
    }
}
