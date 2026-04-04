<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\CategoryController;
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

    Route::get('/dashboard', HomeController::class)->name('dashboard');

    Route::get('/import', [QuizController::class, 'showImportForm'])->name('eidos.import.form');
    Route::post('/import', [QuizController::class, 'import'])->name('eidos.import');
    Route::resource('topics', TopicController::class)->only(['index', 'show']);

    Route::post('/topics/{topic}/summary', [TopicController::class, 'updateSummary'])->name('topics.summary.update');
    Route::post('/topics/{topic}/infographics', [TopicController::class, 'storeInfographic'])->name('topics.infographics.store');
    Route::post('/topics/{topic}/audios', [TopicController::class, 'storeAudio'])->name('topics.audios.store');
    Route::put('/topics/{topic}/audios/{audio}', [TopicController::class, 'updateAudio'])->name('topics.audios.update');
    Route::delete('/topics/{topic}/audios/{audio}', [TopicController::class, 'destroyAudio'])->name('topics.audios.destroy');
    Route::post('/topics/{topic}/videos', [TopicController::class, 'storeVideo'])->name('topics.videos.store');
    Route::put('/topics/{topic}/videos/{video}', [TopicController::class, 'updateVideo'])->name('topics.videos.update');
    Route::delete('/topics/{topic}/videos/{video}', [TopicController::class, 'destroyVideo'])->name('topics.videos.destroy');
    Route::get('/categorias/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/topics/{topic}/quiz/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::get('/topics/{topic}/quiz/question/{questionNumber}', [QuizController::class, 'showQuestion'])->name('quiz.question');
    Route::post('/quiz/question/{question}/answer', [QuizController::class, 'answer'])->name('quiz.answer');
    Route::get('/topics/{topic}/quiz/finish', [QuizController::class, 'finish'])->name('quiz.finish');
    Route::get('/quiz/results/{quizHistory}', [QuizController::class, 'results'])->name('quiz.results');

    Route::middleware('is_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/approve', [AdminController::class, 'approve'])->name('users.approve');
        Route::get('/topics', [AdminController::class, 'topicsIndex'])->name('topics.index');
        Route::get('/topics/{topic}/edit', [AdminController::class, 'topicsEdit'])->name('topics.edit');
        Route::put('/topics/{topic}', [AdminController::class, 'topicsUpdate'])->name('topics.update');
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
    });

    Route::spladeWithVueBridge();
    Route::spladePasswordConfirmation();
    Route::spladeTable();
    Route::spladeUploads();
});
