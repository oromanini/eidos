<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        $category->load(['topics' => fn ($query) => $query->orderBy('name')]);

        return view('categories.show', compact('category'));
    }
}
