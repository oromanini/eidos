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

        $category->load(['topics' => fn ($query) => $query->orderByDesc('created_at')]);

        return view('categories.show', compact('category'));
    }
}
