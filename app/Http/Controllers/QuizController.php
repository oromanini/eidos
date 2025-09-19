<?php

namespace App\Http\Controllers;

use App\Services\QuizService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use ProtoneMedia\Splade\Facades\Splade;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }


    public function showImportForm(): View
    {
        return view('eidos.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5000',
        ]);

        $filePath = $request->file('csv_file')->store('imports');
        $this->quizService->importQuestionsFromCsv($filePath);

        Splade::toast('As perguntas foram importadas com sucesso!')->autoDismiss(5);

        return redirect()->back();
    }
}
