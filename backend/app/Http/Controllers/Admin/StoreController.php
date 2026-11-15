<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function edit()
    {
        $store = StoreInfo::current();

        return view("admin.store.edit", compact("store"));
    }

    public function update(Request $request)
    {
        $store = StoreInfo::current();

        $validated = $request->validate([
            "title"          => "required|string|max:255",
            "description"    => "nullable|string",
            "address"        => "nullable|string|max:255",
            "business_hours" => "nullable|string|max:255",
            "access"         => "nullable|string",
            "image"          => "nullable|image|max:2048",
            "map_image"      => "nullable|image|max:2048",

            // 送り状のご依頼主（B2クラウドの文字数に合わせる。全角は2・半角は1として数える）
            "sender_name"     => ["nullable", "string", $this->maxSjisBytes(32)],
            "sender_phone"    => ["nullable", "string", "max:15", "regex:/^[0-9０-９\\-－‐ー−]+$/u"],
            "sender_postal"   => ["nullable", "string", "max:8", "regex:/^([0-9０-９]{3}[\\-－‐ー−]?[0-9０-９]{4})$/u"],
            "sender_address"  => ["nullable", "string", $this->maxSjisBytes(64)],
            "sender_building" => ["nullable", "string", $this->maxSjisBytes(32)],
        ], [
            "sender_phone.regex"  => "電話番号は数字とハイフンで入力してください。",
            "sender_postal.regex" => "郵便番号は7桁（例: 123-4567）で入力してください。",
        ], [
            "sender_name"     => "ご依頼主名",
            "sender_phone"    => "電話番号",
            "sender_postal"   => "郵便番号",
            "sender_address"  => "住所",
            "sender_building" => "建物名",
        ]);

        // 電話番号・郵便番号は半角に揃えて保存する
        foreach (["sender_phone", "sender_postal"] as $key) {
            if (!empty($validated[$key])) {
                $validated[$key] = preg_replace("/[－‐ー−]/u", "-", mb_convert_kana($validated[$key], "n"));
            }
        }
        if (!empty($validated["sender_postal"]) && !str_contains($validated["sender_postal"], "-")) {
            $validated["sender_postal"] = substr($validated["sender_postal"], 0, 3) . "-" . substr($validated["sender_postal"], 3);
        }

        if ($request->hasFile("image")) {
            if ($store->image_path) {
                Storage::disk("public")->delete($store->image_path);
            }
            $validated["image_path"] = $request->file("image")->store("store", "public");
        }

        if ($request->hasFile("map_image")) {
            if ($store->map_image_path) {
                Storage::disk("public")->delete($store->map_image_path);
            }
            $validated["map_image_path"] = $request->file("map_image")->store("store", "public");
        }

        unset($validated["image"], $validated["map_image"]);

        $store->update($validated);

        return redirect()->route("admin.store.edit")->with("success", "店舗・工場情報を更新しました。");
    }

    /**
     * 送り状に印字できる長さ（Shift_JIS のバイト数）以内か。送り状で使えない文字（絵文字など）も弾く
     */
    private function maxSjisBytes(int $max): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($max) {
            $sjis = mb_convert_encoding((string) $value, "SJIS-win", "UTF-8");
            if (mb_convert_encoding($sjis, "UTF-8", "SJIS-win") !== (string) $value) {
                $fail(":attributeに送り状で使えない文字（絵文字・一部の旧字体など）が含まれています。");
            } elseif (strlen($sjis) > $max) {
                $fail(sprintf(":attributeは全角%d文字（半角%d文字）以内で入力してください。", intdiv($max, 2), $max));
            }
        };
    }
}
