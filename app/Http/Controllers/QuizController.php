<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizHistory;
use App\Models\Topic;
use App\Services\QuizHistoryService;
use App\Services\QuizService;
use App\Services\UserAnswerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use ProtoneMedia\Splade\Facades\Splade;

class QuizController extends Controller
{
    protected QuizService $quizService;
    protected UserAnswerService $userAnswerService;
    private QuizHistoryService $quizHistoryService;

    public function __construct(
        QuizService $quizService,
        UserAnswerService $userAnswerService,
        QuizHistoryService $quizHistoryService
    ){
        $this->quizService = $quizService;
        $this->userAnswerService = $userAnswerService;
        $this->quizHistoryService = $quizHistoryService;
    }

    public function showImportForm(): View
    {
        return view('eidos.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $filePath = $request->file('csv_file')->store('imports');

        $this->quizService->importQuestionsFromCsv($filePath);

        Splade::toast('O tópico foi importado com sucesso!')->autoDismiss(5);

        return redirect()->route('home');
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
            'start_time' => time(), // << ADICIONA O TEMPO DE INÍCIO

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


    public function answer(Request $request, Question $question): \Illuminate\Http\JsonResponse
    {
        $request->validate(['answer' => 'required|string|in:a,b,c,d']);

        $isCorrect = $request->answer === $question->correct_answer;

        if ($isCorrect) {
            Session::increment('quiz.score');
        }

        $this->userAnswerService->saveUserAnswer(
            userId: 1,
            questionId: $question->id,
            userAnswer: $request->answer,
            isCorrect: $isCorrect
        );

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
        ]);
    }

    public function results(QuizHistory $quizHistory)
    {
        $repetitionCount = QuizHistory::where('user_id', $quizHistory->user_id)
            ->where('topic_id', $quizHistory->topic_id)
            ->count();

        return view('quiz.results', [
            'history' => $quizHistory,
            'repetitionCount' => $repetitionCount,
        ]);
    }

    public function finish(Topic $topic)
    {
        $quizState = Session::get('quiz');

        if (!$quizState || $quizState['topic_id'] !== $topic->id) {
            return redirect()->route('home');
        }

        $duration = time() - $quizState['start_time'];
        $totalQuestions = count($quizState['question_ids']);
        $score = $quizState['score'];
        $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;

        $history = $this->quizHistoryService->logQuizCompletion([
            'user_id' => 1,
            'topic_id' => $topic->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => round($percentage, 2),
            'duration_in_seconds' => $duration,
        ]);

        Session::forget('quiz');

        return redirect()->route('quiz.results', $history->id);
    }
}
