<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // ─────────────────────────────────────────────────────
    // STEP 1: 購入者情報入力フォーム
    // GET /checkout
    // ─────────────────────────────────────────────────────
    public function index()
    {
        $items = $this->getCartItems();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        // ログイン済みのユーザー情報を初期値として渡す
        $user = Auth::user();

        return view('checkout.index', compact('items', 'user'));
    }

    // ─────────────────────────────────────────────────────
    // STEP 2: 注文確認画面へ
    // POST /checkout/confirm
    // ─────────────────────────────────────────────────────
    public function confirm(Request $request)
    {
        $validated = $this->validateBuyerInfo($request);

        $items = $this->getCartItems();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        // 入力情報をセッションに保存し、確認画面を表示
        session(['checkout_data' => $validated]);

        $totalPrice = $items->sum(fn ($item) => $item->subtotal);

        return view('checkout.confirm', compact('items', 'validated', 'totalPrice'));
    }

    // ─────────────────────────────────────────────────────
    // STEP 3: 注文登録
    // POST /checkout
    // ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // セッションから購入者情報を取得（確認画面をスキップした直接POSTを防ぐ）
        $checkoutData = session('checkout_data');

        if (!$checkoutData) {
            return redirect()->route('checkout.index');
        }

        $items = $this->getCartItems();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        $totalPrice = $items->sum(fn ($item) => $item->subtotal);

        // DB トランザクションで注文と明細を一括保存
        $order = DB::transaction(function () use ($checkoutData, $items, $totalPrice) {

            // 一意な注文番号を生成
            $orderNumber = $this->generateOrderNumber();

            $order = Order::create([
                'user_id'        => Auth::id(),
                'order_number'   => $orderNumber,
                'total_price'    => $totalPrice,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
                ...$checkoutData,
            ]);

            // 注文明細を保存（商品名・価格は注文時点のスナップショット）
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->subtotal,
                ]);
            }

            return $order;
        });

        // カートを空にする
        Cart::where('session_id', session()->getId())->delete();

        // セッションの購入者情報を削除
        session()->forget('checkout_data');

        // 完了画面用に注文番号をセッションへ
        session(['completed_order_number' => $order->order_number]);

        return redirect()->route('checkout.complete');
    }

    // ─────────────────────────────────────────────────────
    // STEP 4: 注文完了画面
    // GET /checkout/complete
    // ─────────────────────────────────────────────────────
    public function complete()
    {
        $orderNumber = session('completed_order_number');

        if (!$orderNumber) {
            return redirect()->route('home');
        }

        // セッションから削除（リロードで表示できないように）
        session()->forget('completed_order_number');

        return view('checkout.complete', compact('orderNumber'));
    }

    // ─────────────────────────────────────────────────────
    // 内部メソッド
    // ─────────────────────────────────────────────────────

    /** 現在のセッションのカートアイテムを商品情報付きで取得 */
    private function getCartItems()
    {
        return Cart::with('product')
            ->where('session_id', session()->getId())
            ->get();
    }

    /** 購入者情報のバリデーション */
    private function validateBuyerInfo(Request $request): array
    {
        return $request->validate([
            'recipient_name' => 'required|string|max:255',
            'postal_code'    => ['required', 'string', 'regex:/^\d{3}-?\d{4}$/'],
            'prefecture'     => 'required|string|max:10',
            'address'        => 'required|string|max:255',
            'phone_number'   => ['required', 'string', 'regex:/^[0-9\-]{10,13}$/'],
            'email'          => 'required|email|max:255',
        ], [
            'recipient_name.required' => '氏名を入力してください。',
            'postal_code.required'    => '郵便番号を入力してください。',
            'postal_code.regex'       => '郵便番号は「123-4567」または「1234567」の形式で入力してください。',
            'prefecture.required'     => '都道府県を選択してください。',
            'address.required'        => '住所を入力してください。',
            'phone_number.required'   => '電話番号を入力してください。',
            'phone_number.regex'      => '電話番号は半角数字とハイフンで入力してください（例: 090-1234-5678）。',
            'email.required'          => 'メールアドレスを入力してください。',
            'email.email'             => '正しいメールアドレスを入力してください。',
        ]);
    }

    /**
     * 一意な注文番号を生成
     * 形式: ORD-YYYYMMDD-XXXXXXXX（大文字英数字8桁）
     */
    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
