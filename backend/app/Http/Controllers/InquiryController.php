<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{
    // ─────────────────────────────────────────────────────
    // STEP 1: 問い合わせ入力フォーム
    // GET /contact
    // ─────────────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();
        $types = Inquiry::TYPES;

        return view('contact.index', compact('user', 'types'));
    }

    // ─────────────────────────────────────────────────────
    // STEP 2: 入力内容の確認画面へ
    // POST /contact/confirm
    // ─────────────────────────────────────────────────────
    public function confirm(Request $request)
    {
        $validated = $this->validateInquiry($request);

        // 入力情報をセッションに保存し、確認画面を表示
        session(['inquiry_data' => $validated]);

        $typeLabel = Inquiry::TYPES[$validated['type']];

        return view('contact.confirm', compact('validated', 'typeLabel'));
    }

    // ─────────────────────────────────────────────────────
    // STEP 3: 問い合わせ登録
    // POST /contact
    // ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // セッションから入力内容を取得（確認画面をスキップした直接POSTを防ぐ）
        $inquiryData = session('inquiry_data');

        if (!$inquiryData) {
            return redirect()->route('contact.index');
        }

        Inquiry::create([
            'user_id' => Auth::id(),
            'status' => Inquiry::STATUS_UNREAD,
            ...$inquiryData,
        ]);

        session()->forget('inquiry_data');
        session(['inquiry_submitted' => true]);

        return redirect()->route('contact.complete');
    }

    // ─────────────────────────────────────────────────────
    // STEP 4: 送信完了画面
    // GET /contact/complete
    // ─────────────────────────────────────────────────────
    public function complete()
    {
        if (!session('inquiry_submitted')) {
            return redirect()->route('contact.index');
        }

        // セッションから削除（リロードで再表示できないように）
        session()->forget('inquiry_submitted');

        return view('contact.complete');
    }

    // ─────────────────────────────────────────────────────
    // 内部メソッド
    // ─────────────────────────────────────────────────────

    /** 問い合わせ内容のバリデーション */
    private function validateInquiry(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Inquiry::TYPES)),
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
        ], [
            'name.required' => '名前を入力してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '正しいメールアドレスの形式で入力してください。',
            'type.required' => '問い合わせ種別を選択してください。',
            'type.in' => '問い合わせ種別が正しくありません。',
            'subject.required' => '件名を入力してください。',
            'subject.max' => '件名は255文字以内で入力してください。',
            'body.required' => '問い合わせ内容を入力してください。',
            'body.max' => '問い合わせ内容は2000文字以内で入力してください。',
        ]);
    }
}
