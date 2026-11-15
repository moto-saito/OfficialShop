<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        "name",
        "description",
        "price",
        "image_path",
        "status",
    ];

    const STATUS_SELLING = "selling";
    const STATUS_SOLDOUT = "soldout";
    const STATUS_HIDDEN  = "hidden";

    /** 商品画像（表示順）。1枚目は image_path と同じ */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeSelling($query)
    {
        return $query->where("status", self::STATUS_SELLING);
    }

    /** 公開画面に表示する商品（販売中・売り切れ） */
    public function scopeVisible($query)
    {
        return $query->whereIn("status", [self::STATUS_SELLING, self::STATUS_SOLDOUT]);
    }

    public function isSelling(): bool
    {
        return $this->status === self::STATUS_SELLING;
    }

    public function isSoldOut(): bool
    {
        return $this->status === self::STATUS_SOLDOUT;
    }

    public function getFormattedPriceAttribute(): string
    {
        return "¥" . number_format($this->price);
    }
}
