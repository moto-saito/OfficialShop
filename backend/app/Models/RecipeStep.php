<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeStep extends Model
{
    protected $fillable = [
        "recipe_id",
        "body",
        "image_path",
        "sort_order",
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
