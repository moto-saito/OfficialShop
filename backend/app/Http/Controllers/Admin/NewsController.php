<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $newsList = News::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.news.index', compact('newsList'));
    }

    public function create()
    {
        return view('admin.news.create');
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
            $validated['image_path'] = $request->file('image')->store('news', 'public');
        }
        unset($validated['image']);

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'お知らせを作成しました。');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'status'       => 'required|in:published,draft',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('news', 'public');
        }
        unset($validated['image']);

        $news->update($validated);

        return redirect()->route('admin.news.index')->with('success', 'お知らせを更新しました。');
    }

    public function destroy(News $news)
    {
        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'お知らせを削除しました。');
    }

    public function toggleStatus(News $news)
    {
        if ($news->status === 'published') {
            $news->update(['status' => 'draft']);
            $message = '非公開にしました。';
        } else {
            $news->update([
                'status'       => 'published',
                'published_at' => $news->published_at ?? now(),
            ]);
            $message = '公開しました。';
        }

        return redirect()->route('admin.news.index')->with('success', $message);
    }
}
