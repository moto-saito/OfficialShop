<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::selling()->paginate(12);

        return view("products.index", compact("products"));
    }

    public function show(Product $product)
    {
        abort_if(!$product->isSelling(), 404);

        return view("products.show", compact("product"));
    }
}
