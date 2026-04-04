<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use ProtoneMedia\Splade\Facades\Toast;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with('topics:id,category_id')
            ->orderBy('name')
            ->paginate(15);

        $categories->getCollection()->transform(function (Category $category): Category {
            $category->setAttribute('topics_count', $category->topics->count());

            return $category;
        });

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($validated);

        Toast::success('Categoria criada com sucesso.')->autoDismiss(5);

        return redirect()->route('admin.categories.index');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        Toast::success('Categoria atualizada com sucesso.')->autoDismiss(5);

        return redirect()->route('admin.categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $defaultCategory = Category::firstOrCreateGeneralKnowledge();

        if ($category->id === $defaultCategory->id) {
            Toast::warning('A categoria padrão não pode ser removida.')->autoDismiss(5);

            return redirect()->route('admin.categories.index');
        }

        $category->topics()->update(['category_id' => $defaultCategory->id]);
        $category->delete();

        Toast::success('Categoria removida e tópicos movidos para Conhecimentos gerais.')->autoDismiss(5);

        return redirect()->route('admin.categories.index');
    }
}
