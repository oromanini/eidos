<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->withCount('topics')
            ->orderBy('name')
            ->get();

        return view('home', [
            'categories' => $categories,
        ]);
    }
}
