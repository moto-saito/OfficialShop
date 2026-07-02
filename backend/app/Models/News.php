<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        "title",
        "content",
        "image_path",
        "status",
        "published_at",
    ];

    protected $casts = [
        "published_at" => "datetime",
    ];

    public function scopePublished($query)
    {
        return $query
            ->where("status", "published")
            ->where("published_at", "<=", now())
            ->orderBy("published_at", "desc");
    }

    public function isPublished(): bool
    {
        return $this->status === "published"
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }
}
