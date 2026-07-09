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

    // ─── 検索 ──────────────────────────────────────────

    /**
     * 管理画面の注文検索用スコープ
     * 各条件はすべて任意で、指定されたものだけAND条件で絞り込む
     */
    public function scopeSearch($query, array $filters)
    {
        return $query
            ->when($filters['order_number'] ?? null, function ($q, $value) {
                $q->where('order_number', 'like', "%{$value}%");
            })
            ->when($filters['recipient_name'] ?? null, function ($q, $value) {
                $q->where('recipient_name', 'like', "%{$value}%");
            })
            ->when($filters['email'] ?? null, function ($q, $value) {
                $q->where('email', 'like', "%{$value}%");
            })
            ->when($filters['status'] ?? null, function ($q, $value) {
                $q->where('status', $value);
            })
            ->when($filters['payment_status'] ?? null, function ($q, $value) {
                $q->where('payment_status', $value);
            })
            ->when($filters['date_from'] ?? null, function ($q, $value) {
                $q->whereDate('created_at', '>=', $value);
            })
            ->when($filters['date_to'] ?? null, function ($q, $value) {
                $q->whereDate('created_at', '<=', $value);
            });
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
