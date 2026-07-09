<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// ─── TOP ───────────────────────────────────────────────
Route::get("/", [HomeController::class, "index"])->name("home");

// ─── 公開画面 ───────────────────────────────────────────
Route::get("/products", [ProductController::class, "index"])->name("products.index");
Route::get("/products/{product}", [ProductController::class, "show"])->name("products.show");

Route::get("/news", [NewsController::class, "index"])->name("news.index");
Route::get("/news/{news}", [NewsController::class, "show"])->name("news.show");

Route::get("/cart", [CartController::class, "index"])->name("cart.index");
Route::post("/cart", [CartController::class, "store"])->name("cart.store");
Route::patch("/cart/{cart}", [CartController::class, "update"])->name("cart.update");
Route::delete("/cart/{cart}", [CartController::class, "destroy"])->name("cart.destroy");

// ─── ユーザー認証（ゲスト用） ──────────────────────────
Route::middleware("guest")->group(function () {
    Route::get("/register", [AuthController::class, "showRegisterForm"])->name("register");
    Route::post("/register", [AuthController::class, "register"]);

    Route::get("/login", [AuthController::class, "showLoginForm"])->name("login");
    Route::post("/login", [AuthController::class, "login"]);
});

Route::post("/logout", [AuthController::class, "logout"])->name("logout");

// ─── ログイン必須ページ ─────────────────────────────────
Route::middleware("auth")->group(function () {

    // マイページ
    Route::get("/mypage", [MypageController::class, "index"])->name("mypage.index");
    Route::get("/mypage/edit", [MypageController::class, "edit"])->name("mypage.edit");
    Route::patch("/mypage", [MypageController::class, "update"])->name("mypage.update");

    // 注文履歴（マイページ）
    Route::get("/mypage/orders", [MypageController::class, "orders"])->name("mypage.orders.index");
    Route::get("/mypage/orders/{order}", [MypageController::class, "orderDetail"])->name("mypage.orders.show");

    // チェックアウト
    Route::get("/checkout", [CheckoutController::class, "index"])->name("checkout.index");
    Route::post("/checkout/confirm", [CheckoutController::class, "confirm"])->name("checkout.confirm");
    Route::post("/checkout", [CheckoutController::class, "store"])->name("checkout.store");
    Route::get("/checkout/complete", [CheckoutController::class, "complete"])->name("checkout.complete");
});

// ─── 管理画面 ───────────────────────────────────────────
Route::prefix("admin")->name("admin.")->group(function () {

    Route::get("login", [AdminAuthController::class, "showLoginForm"])->name("login");
    Route::post("login", [AdminAuthController::class, "login"]);
    Route::post("logout", [AdminAuthController::class, "logout"])->name("logout");

    Route::middleware("auth.admin")->group(function () {
        Route::get("/", [AdminDashboardController::class, "index"])->name("dashboard");

        Route::resource("news", AdminNewsController::class);
        Route::patch("news/{news}/toggle", [AdminNewsController::class, "toggleStatus"])->name("news.toggle");
        Route::resource("products", AdminProductController::class)->except(["show"]);

        // {order}パラメータとの衝突を避けるため export は resource より前に定義
        Route::get("orders/export", [AdminOrderController::class, "export"])->name("orders.export");
        Route::resource("orders", AdminOrderController::class)->only(["index", "show", "destroy"]);
        Route::patch("orders/{order}/status", [AdminOrderController::class, "updateStatus"])->name("orders.updateStatus");
    });
});
