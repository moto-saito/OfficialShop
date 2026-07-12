<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::selling()->paginate(12);

        // 商品一覧右上の小計欄用に、現在のセッションのカート情報を取得
        $cartItems = Cart::with('product')
            ->where('session_id', session()->getId())
            ->get();

        return view("products.index", compact("products", "cartItems"));
    }

    public function show(Product $product)
    {
        abort_if(!$product->isSelling(), 404);

        return view("products.show", compact("product"));
    }
}
