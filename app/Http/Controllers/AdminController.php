<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable; // Importe a classe View

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View // Especifique o tipo de retorno
    {
        return view('admin.users.index', [
            'users' => SpladeTable::for(User::class)
                ->withGlobalSearch(columns: ['name', 'email'])
                ->defaultSort('created_at')
                ->column('name', 'Nome', sortable: true)
                ->column('email', 'Email', sortable: true)
                ->column('created_at', 'Data de Registro', sortable: true)
                ->column('status', 'Status')
                ->column('actions', 'Ações', canBeHidden: false),
        ]);
    }

    public function approve(User $user)
    {
        $user->update(['approved_at' => now()]);

        Toast::title('Usuário aprovado!')
            ->message("O usuário {$user->name} agora pode acessar o sistema.")
            ->success()
            ->autoDismiss(5);

        return redirect()->back();
    }

    public function topicsIndex(): View
    {
        $topics = Topic::query()
            ->with(['questions', 'category'])
            ->orderByDesc('created_at')
            ->paginate(15);
        $topics->getCollection()->transform(function (Topic $topic) {
            $topic->setAttribute('questions_count', $topic->questions->count());

            return $topic;
        });

        return view('admin.topics.index', compact('topics'));
    }

    public function topicsEdit(Topic $topic): View
    {
        $topic->load('questions');

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.topics.edit', compact('topic', 'categories'));
    }

    public function topicsUpdate(Request $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.id' => ['required', 'string'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.options.a' => ['required', 'string'],
            'questions.*.options.b' => ['required', 'string'],
            'questions.*.options.c' => ['required', 'string'],
            'questions.*.options.d' => ['required', 'string'],
            'questions.*.correct_answer' => ['required', 'string', 'in:a,b,c,d'],
        ]);

        $topic->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'category_id' => $validated['category_id'],
        ]);

        foreach ($validated['questions'] as $questionData) {
            $question = $topic->questions()->find($questionData['id']);

            if (! $question) {
                continue;
            }

            $question->update([
                'question_text' => $questionData['question_text'],
                'options' => $questionData['options'],
                'correct_answer' => $questionData['correct_answer'],
            ]);
        }

        Toast::title('Tópico atualizado!')
            ->message('Título, descrição, perguntas e respostas foram salvos com sucesso.')
            ->success()
            ->autoDismiss(5);

        return redirect()->route('admin.topics.edit', $topic);
    }
}
