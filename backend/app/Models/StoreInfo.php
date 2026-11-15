<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreInfo extends Model
{
    protected $fillable = [
        "title",
        "description",
        "image_path",
        "address",
        "business_hours",
        "access",
        "map_image_path",
        // 送り状（ヤマト運輸 B2クラウド）のご依頼主
        "sender_name",
        "sender_phone",
        "sender_postal",
        "sender_address",
        "sender_building",
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], ["title" => "店舗・工場紹介"]);
    }
}
