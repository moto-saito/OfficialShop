<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
