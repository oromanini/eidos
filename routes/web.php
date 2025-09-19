<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::middleware(['splade'])->group(function () {
    // Rotas do Eidos
    Route::get('/', HomeController::class)->name('home');
    Route::get('/import', [QuizController::class, 'showImportForm'])->name('eidos.import.form');
    Route::post('/import', [QuizController::class, 'import'])->name('eidos.import');
    Route::resource('topics', TopicController::class)->only(['index', 'show']);

    // Rotas do Splade (necessárias para o funcionamento do framework)
    Route::spladeWithVueBridge();
    Route::spladePasswordConfirmation();
    Route::spladeTable();
    Route::spladeUploads();
});
