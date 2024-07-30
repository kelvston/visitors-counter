<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorsCounterController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/report', [VisitorsCounterController::class, 'show'])->name('counter.report');
Route::post('/report', [VisitorsCounterController::class, 'increment'])->name('counter.increment');
Route::get('/chatbot', [VisitorsCounterController::class, 'index']);
Route::post('/chatbot', [VisitorsCounterController::class, 'respond']);
Route::get('/visitor-summary', [VisitorsCounterController::class, 'getVisitorSummary'])->name('visitor.summary');
Route::get('/visitor-data', [VisitorsCounterController::class, 'getVisitorDataForChatbot'])->name('visitor.data');
Route::post('/analyze-text', [VisitorsCounterController::class, 'analyzeTextWithSpacy']);
Route::get('/analyze-test', [VisitorsCounterController::class, 'testPythonScript']);

Route::get('/test-script-path', function () {
    $scriptPath = base_path('analyze_text_spacy.py');
    return response()->json(['scriptPath' => $scriptPath]);
});


require __DIR__.'/auth.php';
