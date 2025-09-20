<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use App\Services\QuizService;
use App\Services\UserAnswerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use ProtoneMedia\Splade\Facades\Splade;

class QuizController extends Controller
{
    protected QuizService $quizService;
    protected UserAnswerService $userAnswerService;

    public function __construct(
        QuizService $quizService,
        UserAnswerService $userAnswerService,
    ){
        $this->quizService = $quizService;
        $this->userAnswerService = $userAnswerService;
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

    public function start(Topic $topic): RedirectResponse
    {
        $questions = $topic->questions()->inRandomOrder()->limit(10)->pluck('id');

        if ($questions->isEmpty()) {
            Splade::toast('Este tópico ainda não possui perguntas.')->warning();
            return redirect()->back();
        }

        Session::put('quiz', [
            'topic_id' => $topic->id,
            'question_ids' => $questions->toArray(),
            'current_question_index' => 0,
            'score' => 0,
        ]);

        return redirect()->route('quiz.question', ['topic' => $topic, 'questionNumber' => 1]);
    }

    public function showQuestion(Topic $topic, int $questionNumber): View|RedirectResponse
    {
        $quizState = Session::get('quiz');
        $questionIndex = $questionNumber - 1;

        if (!$quizState || !isset($quizState['question_ids'][$questionIndex])) {
            Splade::toast('Quiz não encontrado ou finalizado.')->info();
            return redirect()->route('topics.show', $topic);
        }

        $questionId = $quizState['question_ids'][$questionIndex];
        $question = Question::findOrFail($questionId);

        return view('quiz.show', [
            'topic' => $topic,
            'question' => $question,
            'questionNumber' => $questionNumber,
            'totalQuestions' => count($quizState['question_ids']),
        ]);
    }

    public function answer(Request $request, Question $question)
    {
        $request->validate(['answer' => 'required|string|in:a,b,c,d']);

        $isCorrect = $request->answer === $question->correct_answer;

        if ($isCorrect) {
            Session::increment('quiz.score');
        }

        $this->userAnswerService->saveUserAnswer(
            userId: 1, //TODO: after login
            questionId: $question->id,
            userAnswer: $request->answer,
            isCorrect: $isCorrect
        );

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
        ]);
    }
}
