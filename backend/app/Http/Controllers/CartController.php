<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // 商品情報を eager load して N+1 を防ぐ
        $items = Cart::with('product')
            ->where('session_id', session()->getId())
            ->get();

        return view('cart.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|min:1',
            'quantity'   => 'required|integer|min:1',
        ]);

        $sessionId = session()->getId();

        $cart = Cart::where('session_id', $sessionId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
                'session_id' => $sessionId,
            ]);
        }

        // 商品一覧ページの小計欄からのAJAX送信にはJSONでカート情報を返す
        if ($request->wantsJson()) {
            $items = Cart::with('product')
                ->where('session_id', $sessionId)
                ->get();

            return response()->json([
                'success'  => true,
                'items'    => $items->map(fn ($item) => [
                    'id'       => $item->id,
                    'name'     => $item->product?->name,
                    'price'    => $item->product?->price ?? 0,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]),
                'subtotal' => $items->sum(fn ($item) => $item->subtotal),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'カートに追加しました。');
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', '個数を変更しました。');
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return redirect()->route('cart.index')->with('success', '商品を削除しました。');
    }
}
