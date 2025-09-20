<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::middleware(['splade'])->group(function () {

    Route::get('/', HomeController::class)->name('home');
    Route::get('/import', [QuizController::class, 'showImportForm'])->name('eidos.import.form');
    Route::post('/import', [QuizController::class, 'import'])->name('eidos.import');
    Route::resource('topics', TopicController::class)->only(['index', 'show']);

    Route::get('/topics/{topic}/quiz/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::get('/topics/{topic}/quiz/question/{questionNumber}', [QuizController::class, 'showQuestion'])->name('quiz.question');
    Route::post('/quiz/question/{question}/answer', [QuizController::class, 'answer'])->name('quiz.answer');

    Route::spladeWithVueBridge();
    Route::spladePasswordConfirmation();
    Route::spladeTable();
    Route::spladeUploads();
});
