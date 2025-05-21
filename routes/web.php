<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorsCounterController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\reportController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/barcode', function () {
    return view('barcode');
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
Route::post('/multiple', [VisitorsCounterController::class, 'incrementMultiple'])->name('counter.increment.multiple');
Route::get('/chatbot', [VisitorsCounterController::class, 'index']);
Route::post('/chatbot', [VisitorsCounterController::class, 'respond']);
Route::get('/visitor-summary', [VisitorsCounterController::class, 'getVisitorSummary'])->name('visitor.summary');
Route::get('/visitor-data', [VisitorsCounterController::class, 'getVisitorDataForChatbot'])->name('visitor.data');
Route::post('/analyze-text', [VisitorsCounterController::class, 'analyzeTextWithSpacy']);
Route::get('/analyze-test', [VisitorsCounterController::class, 'testPythonScript']);
Route::post('/counter/decrement', [VisitorsCounterController::class, 'decrement'])->name('counter.decrement');

Route::get('/visitor_stats', [reportController::class, 'index'])->name('visitor_stats');
Route::get('/news_leter', [NewsLetterController::class, 'index'])->name('news_leter');
Route::post('/news_leter_store', [NewsLetterController::class, 'store'])->name('news_leter.store');
Route::get('/data', [reportController::class, 'getData'])->name('report.data');



Route::get('/test-script-path', function () {
    $scriptPath = base_path('analyze_text_spacy.py');
    return response()->json(['scriptPath' => $scriptPath]);
});


require __DIR__.'/auth.php';
