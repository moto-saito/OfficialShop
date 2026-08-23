<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Product;
use App\Models\Recipe;

class HomeController extends Controller
{
    public function index()
    {
        $latestNews         = News::published()->limit(10)->get();
        $products           = Product::selling()->limit(3)->get();
        $recommendedRecipes = Recipe::published()->limit(3)->get();

        return view('home', compact('latestNews', 'products', 'recommendedRecipes'));
    }
}
