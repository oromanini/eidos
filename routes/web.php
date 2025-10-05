<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');

Route::middleware(['auth', 'splade'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Alteração Principal: Renomeando 'home' para 'dashboard'
    Route::get('/dashboard', HomeController::class)->name('dashboard');

    Route::get('/import', [QuizController::class, 'showImportForm'])->name('eidos.import.form');
    Route::post('/import', [QuizController::class, 'import'])->name('eidos.import');
    Route::resource('topics', TopicController::class)->only(['index', 'show']);
    Route::get('/topics/{topic}/quiz/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::get('/topics/{topic}/quiz/question/{questionNumber}', [QuizController::class, 'showQuestion'])->name('quiz.question');
    Route::post('/quiz/question/{question}/answer', [QuizController::class, 'answer'])->name('quiz.answer');
    Route::get('/topics/{topic}/quiz/finish', [QuizController::class, 'finish'])->name('quiz.finish');
    Route::get('/quiz/results/{quizHistory}', [QuizController::class, 'results'])->name('quiz.results');

    Route::middleware('is_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/approve', [AdminController::class, 'approve'])->name('users.approve');
    });

    Route::spladeWithVueBridge();
    Route::spladePasswordConfirmation();
    Route::spladeTable();
    Route::spladeUploads();
});
