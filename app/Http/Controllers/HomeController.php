<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->with('topics:id,category_id')
            ->orderBy('name')
            ->get()
            ->each(function (Category $category): void {
                $category->setAttribute('topics_count', $category->topics->count());
            });

        return view('home', [
            'categories' => $categories,
        ]);
    }
}
