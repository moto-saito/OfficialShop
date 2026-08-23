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
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], ["title" => "店舗・工場紹介"]);
    }
}
