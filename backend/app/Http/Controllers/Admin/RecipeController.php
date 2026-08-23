<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.recipes.index', compact('recipes'));
    }

    public function create()
    {
        return view('admin.recipes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'status'       => 'required|in:published,draft',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('recipes', 'public');
        }
        unset($validated['image']);

        Recipe::create($validated);

        return redirect()->route('admin.recipes.index')->with('success', 'レシピを作成しました。');
    }

    public function edit(Recipe $recipe)
    {
        return view('admin.recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'status'       => 'required|in:published,draft',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($recipe->image_path) {
                Storage::disk('public')->delete($recipe->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('recipes', 'public');
        }
        unset($validated['image']);

        $recipe->update($validated);

        return redirect()->route('admin.recipes.index')->with('success', 'レシピを更新しました。');
    }

    public function destroy(Recipe $recipe)
    {
        if ($recipe->image_path) {
            Storage::disk('public')->delete($recipe->image_path);
        }
        $recipe->delete();

        return redirect()->route('admin.recipes.index')->with('success', 'レシピを削除しました。');
    }

    public function toggleStatus(Recipe $recipe)
    {
        if ($recipe->status === 'published') {
            $recipe->update(['status' => 'draft']);
            $message = '非公開にしました。';
        } else {
            $recipe->update([
                'status'       => 'published',
                'published_at' => $recipe->published_at ?? now(),
            ]);
            $message = '公開しました。';
        }

        return redirect()->route('admin.recipes.index')->with('success', $message);
    }
}
