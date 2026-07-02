<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ==================== 会員登録 ====================

    public function showRegisterForm()
    {
        // ログイン済みならマイページへ
        if (Auth::check()) {
            return redirect()->route('mypage.index');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'      => '氏名を入力してください。',
            'email.required'     => 'メールアドレスを入力してください。',
            'email.email'        => '正しいメールアドレスを入力してください。',
            'email.unique'       => 'このメールアドレスはすでに登録されています。',
            'password.required'  => 'パスワードを入力してください。',
            'password.min'       => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',
        ]);

        $user = User::create($validated);

        // 登録後にそのままログイン
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('mypage.index')->with('success', '会員登録が完了しました。');
    }

    // ==================== ログイン ====================

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('mypage.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'メールアドレスを入力してください。',
            'email.email'       => '正しいメールアドレスを入力してください。',
            'password.required' => 'パスワードを入力してください。',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // ログイン前にアクセスしようとしていたページへ（なければマイページ）
        return redirect()->intended(route('mypage.index'));
    }

    // ==================== ログアウト ====================

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
