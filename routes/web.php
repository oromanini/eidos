<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['splade'])->group(function () {
    // --- NOSSAS ROTAS DO EIDOS ---
    // Rotas públicas
    Route::get('/', HomeController::class)->name('home');
    Route::get('/import', [QuizController::class, 'showImportForm'])->name('eidos.import.form');
    Route::post('/import', [QuizController::class, 'import'])->name('eidos.import');
    Route::resource('topics', TopicController::class)->only(['index', 'show']);

    // Rotas do Quiz (sem autenticação por enquanto)
    Route::get('/topics/{topic}/quiz/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::get('/topics/{topic}/quiz/question/{questionNumber}', [QuizController::class, 'showQuestion'])->name('quiz.question');
    Route::post('/quiz/question/{question}/answer', [QuizController::class, 'answer'])->name('quiz.answer');
// Em: routes/web.php
    Route::get('/topics/{topic}/quiz/finish', [QuizController::class, 'finish'])->name('quiz.finish');
    Route::get('/quiz/results/{quizHistory}', [QuizController::class, 'results'])->name('quiz.results');

    // --- ROTAS DO SPLADE ---
    // Registra rotas para suportar os componentes interativos...
    Route::spladeWithVueBridge();

    // Registra rotas para suportar a confirmação de senha nos componentes Form e Link...
    Route::spladePasswordConfirmation();

    // Registra rotas para suportar Ações em Massa e Exportações de Tabelas...
    Route::spladeTable();

    // Registra rotas para suportar Uploads de Arquivos assíncronos com Filepond...
    Route::spladeUploads();
});
