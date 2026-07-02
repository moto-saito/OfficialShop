<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $newsList = News::published()->paginate(10);

        return view("news.index", compact("newsList"));
    }

    public function show(News $news)
    {
        abort_if(!$news->isPublished(), 404);

        return view("news.show", compact("news"));
    }
}
