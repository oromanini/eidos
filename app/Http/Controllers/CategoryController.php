<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        Topic::assignUncategorizedToGeneralKnowledge();

        $topics = Topic::query()
            ->where('category_id', $category->id)
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('categories.show', compact('category', 'topics'));
    }
}
