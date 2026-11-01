<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingFeeController extends Controller
{
    public function edit()
    {
        $fees = ShippingFee::feeMap();

        return view("admin.shipping-fees.edit", compact("fees"));
    }

    public function update(Request $request)
    {
        $rules = [];
        foreach (ShippingFee::PREFECTURES as $prefecture) {
            $rules["fees.{$prefecture}"] = "required|integer|min:0|max:99999";
        }

        $validated = $request->validate($rules, [
            "fees.*.required" => "送料を入力してください。",
            "fees.*.integer"  => "送料は半角数字で入力してください。",
            "fees.*.min"      => "送料は0円以上で入力してください。",
            "fees.*.max"      => "送料は99,999円以下で入力してください。",
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated["fees"] as $prefecture => $fee) {
                ShippingFee::updateOrCreate(
                    ["prefecture" => $prefecture],
                    ["fee" => (int) $fee],
                );
            }
        });

        return redirect()->route("admin.shipping-fees.edit")->with("success", "送料設定を更新しました。");
    }
}
