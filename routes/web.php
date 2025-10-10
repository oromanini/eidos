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

Route::get('/debug-vite', function () {
    $manifestPath = public_path('build/manifest.json');
    $buildDirPath = public_path('build');

    echo '<pre style="background-color: #111; color: #eee; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6;">';
    echo '<h1>-- Diagnóstico do Vite --</h1>';

    echo "<h2>1. Caminho do Manifesto:</h2>";
    var_dump($manifestPath);

    echo "\n<h2>2. A pasta 'public/build' existe?</h2>";
    var_dump(is_dir($buildDirPath));

    echo "\n<h2>3. O arquivo 'manifest.json' existe?</h2>";
    var_dump(file_exists($manifestPath));

    if (file_exists($buildDirPath)) {
        echo "\n<h2>4. Conteúdo da pasta 'public/build':</h2>";
        // scandir lista os arquivos no diretório
        var_dump(scandir($buildDirPath));
    }

    if (file_exists($manifestPath)) {
        echo "\n<h2>5. O arquivo 'manifest.json' é legível?</h2>";
        var_dump(is_readable($manifestPath));

        echo "\n<h2>6. Conteúdo do 'manifest.json':</h2>";
        // file_get_contents tenta ler o arquivo
        var_dump(file_get_contents($manifestPath));
    }

    echo '</pre>';
});
