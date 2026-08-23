<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    public const TYPE_PRODUCT = 'product';
    public const TYPE_ORDER = 'order';
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_RETURN = 'return';
    public const TYPE_OTHER = 'other';

    /** 問い合わせ種別の内部値 => 表示ラベル */
    public const TYPES = [
        self::TYPE_PRODUCT => '商品について',
        self::TYPE_ORDER => '注文について',
        self::TYPE_PAYMENT => '支払いについて',
        self::TYPE_SHIPPING => '配送について',
        self::TYPE_RETURN => '返品・交換について',
        self::TYPE_OTHER => 'その他',
    ];

    public const STATUS_UNREAD = 'unread';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';

    /** ステータスの内部値 => 表示ラベル */
    public const STATUSES = [
        self::STATUS_UNREAD => '未読',
        self::STATUS_IN_PROGRESS => '対応中',
        self::STATUS_RESOLVED => '対応済み',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'type',
        'subject',
        'body',
        'status',
    ];

    // ─── リレーション ──────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── 表示用アクセサ ─────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** ステータスに応じた common_status-badge の配色モディファイアクラスを返す */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_UNREAD => 'common_status-badge--warning',
            self::STATUS_IN_PROGRESS => 'common_status-badge--neutral',
            self::STATUS_RESOLVED => 'common_status-badge--success',
            default => 'common_status-badge--neutral',
        };
    }
}
