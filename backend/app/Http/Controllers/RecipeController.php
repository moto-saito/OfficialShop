<?php

namespace App\Http\Controllers;

use App\Models\Recipe;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::published()->paginate(10);

        return view("recipes.index", compact("recipes"));
    }

    public function show(Recipe $recipe)
    {
        abort_if(!$recipe->isPublished(), 404);

        $recipe->load(["ingredients", "steps"]);

        return view("recipes.show", compact("recipe"));
    }
}
