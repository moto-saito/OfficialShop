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
        ]);

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
}
