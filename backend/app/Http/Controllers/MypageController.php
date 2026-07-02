<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    /** マイページトップ */
    public function index()
    {
        $user = Auth::user();

        return view('mypage.index', compact('user'));
    }

    /** プロフィール編集フォーム */
    public function edit()
    {
        $user = Auth::user();

        return view('mypage.edit', compact('user'));
    }

    /**
     * プロフィール更新
     * 将来的に配送先情報・決済情報の更新も同じ流れで拡張可能
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => "required|email|max:255|unique:users,email,{$user->id}",
            'postal_code'  => 'nullable|string|max:8',
            'prefecture'   => 'nullable|string|max:10',
            'address'      => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ], [
            'name.required'   => '氏名を入力してください。',
            'email.required'  => 'メールアドレスを入力してください。',
            'email.email'     => '正しいメールアドレスを入力してください。',
            'email.unique'    => 'このメールアドレスはすでに使用されています。',
        ]);

        $user->update($validated);

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました。');
    }

    // ─────────────────────────────────────────────────────
    // 注文履歴
    // ─────────────────────────────────────────────────────

    /** 注文履歴一覧 */
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mypage.orders.index', compact('orders'));
    }

    /** 注文詳細 */
    public function orderDetail(Order $order)
    {
        // 他人の注文は表示させない
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items');

        return view('mypage.orders.show', compact('order'));
    }
}
