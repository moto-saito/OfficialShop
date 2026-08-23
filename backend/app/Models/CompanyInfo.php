<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $fillable = [
        "title",
        "description",
        "image_path",
    ];

    public function historyEntries()
    {
        return $this->hasMany(CompanyHistoryEntry::class)->orderBy("sort_order");
    }

    public function achievements()
    {
        return $this->hasMany(CompanyAchievement::class)->orderBy("sort_order");
    }

    public static function current(): self
    {
        return static::firstOrCreate([], ["title" => "企業歴史・実績"]);
    }
}
