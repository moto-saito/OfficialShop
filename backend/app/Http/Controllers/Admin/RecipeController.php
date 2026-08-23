<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
        $validated = $this->validateRecipe($request);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('recipes', 'public');
        }
        unset($validated['image'], $validated['ingredients'], $validated['steps']);

        $recipe = Recipe::create($validated);

        $this->syncIngredients($recipe, $request->input('ingredients', []));
        $this->syncSteps($recipe, $request->all('steps')['steps'] ?? []);

        return redirect()->route('admin.recipes.index')->with('success', 'レシピを作成しました。');
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load(['ingredients', 'steps']);

        return view('admin.recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $this->validateRecipe($request);

        if ($request->hasFile('image')) {
            if ($recipe->image_path) {
                Storage::disk('public')->delete($recipe->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('recipes', 'public');
        }
        unset($validated['image'], $validated['ingredients'], $validated['steps']);

        $recipe->update($validated);

        $this->syncIngredients($recipe, $request->input('ingredients', []));
        $this->syncSteps($recipe, $request->all('steps')['steps'] ?? []);

        return redirect()->route('admin.recipes.index')->with('success', 'レシピを更新しました。');
    }

    public function destroy(Recipe $recipe)
    {
        if ($recipe->image_path) {
            Storage::disk('public')->delete($recipe->image_path);
        }

        foreach ($recipe->steps as $step) {
            if ($step->image_path) {
                Storage::disk('public')->delete($step->image_path);
            }
        }

        $recipe->delete();

        return redirect()->route('admin.recipes.index')->with('success', 'レシピを削除しました。');
    }

    private function validateRecipe(Request $request): array
    {
        return $request->validate([
            'title'                => 'required|string|max:255',
            'servings'             => 'nullable|string|max:50',
            'content'              => 'nullable|string',
            'image'                => 'nullable|image|max:2048',
            'status'               => 'required|in:published,draft',
            'published_at'         => 'nullable|date',
            'ingredients'          => 'nullable|array',
            'ingredients.*.name'   => 'nullable|string|max:255',
            'ingredients.*.amount' => 'nullable|string|max:100',
            'steps'                => 'nullable|array',
            'steps.*.body'         => 'nullable|string',
            'steps.*.image'        => 'nullable|image|max:2048',
        ]);
    }

    private function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        $recipe->ingredients()->delete();

        $order = 0;
        foreach ($ingredients as $ingredient) {
            if (empty($ingredient['name'])) {
                continue;
            }

            $recipe->ingredients()->create([
                'name'       => $ingredient['name'],
                'amount'     => $ingredient['amount'] ?? null,
                'sort_order' => $order++,
            ]);
        }
    }

    private function syncSteps(Recipe $recipe, array $steps): void
    {
        $oldImagePaths = $recipe->steps()->whereNotNull('image_path')->pluck('image_path')->all();

        $newSteps       = [];
        $keptImagePaths = [];
        $order          = 0;

        foreach ($steps as $step) {
            if (empty($step['body'])) {
                continue;
            }

            $imagePath = null;
            if (($step['image'] ?? null) instanceof UploadedFile) {
                $imagePath = $step['image']->store('recipes/steps', 'public');
            } elseif (!empty($step['existing_image'])) {
                $imagePath        = $step['existing_image'];
                $keptImagePaths[] = $imagePath;
            }

            $newSteps[] = [
                'body'       => $step['body'],
                'image_path' => $imagePath,
                'sort_order' => $order++,
            ];
        }

        $recipe->steps()->delete();
        foreach ($newSteps as $newStep) {
            $recipe->steps()->create($newStep);
        }

        foreach (array_diff($oldImagePaths, $keptImagePaths) as $orphanedPath) {
            Storage::disk('public')->delete($orphanedPath);
        }
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
