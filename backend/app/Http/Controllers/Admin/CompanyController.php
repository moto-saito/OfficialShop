<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = CompanyInfo::current();
        $company->load(["historyEntries", "achievements"]);

        return view("admin.company.edit", compact("company"));
    }

    public function update(Request $request)
    {
        $company = CompanyInfo::current();

        $validated = $request->validate([
            "title"                       => "required|string|max:255",
            "description"                 => "nullable|string",
            "image"                       => "nullable|image|max:2048",
            "history"                     => "nullable|array",
            "history.*.year"              => "nullable|string|max:50",
            "history.*.event"             => "nullable|string",
            "achievements"                => "nullable|array",
            "achievements.*.title"        => "nullable|string|max:255",
            "achievements.*.description"  => "nullable|string",
        ]);

        if ($request->hasFile("image")) {
            if ($company->image_path) {
                Storage::disk("public")->delete($company->image_path);
            }
            $validated["image_path"] = $request->file("image")->store("company", "public");
        }
        unset($validated["image"], $validated["history"], $validated["achievements"]);

        $company->update($validated);

        $this->syncHistory($company, $request->input("history", []));
        $this->syncAchievements($company, $request->input("achievements", []));

        return redirect()->route("admin.company.edit")->with("success", "企業歴史・実績を更新しました。");
    }

    private function syncHistory(CompanyInfo $company, array $entries): void
    {
        $company->historyEntries()->delete();

        $order = 0;
        foreach ($entries as $entry) {
            if (empty($entry["year"]) && empty($entry["event"])) {
                continue;
            }

            $company->historyEntries()->create([
                "year"       => $entry["year"] ?? "",
                "event"      => $entry["event"] ?? "",
                "sort_order" => $order++,
            ]);
        }
    }

    private function syncAchievements(CompanyInfo $company, array $achievements): void
    {
        $company->achievements()->delete();

        $order = 0;
        foreach ($achievements as $achievement) {
            if (empty($achievement["title"])) {
                continue;
            }

            $company->achievements()->create([
                "title"       => $achievement["title"],
                "description" => $achievement["description"] ?? null,
                "sort_order"  => $order++,
            ]);
        }
    }
}
