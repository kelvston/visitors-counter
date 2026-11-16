<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorsCounterController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\reportController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatbotTrainingController;
use App\Http\Controllers\StaffMailController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\AcademicMeetingController;
use App\Http\Controllers\LibraryCalendarController;
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
Route::middleware('auth')->group(function () {
    Route::get('/reports', [VisitorsCounterController::class, 'show'])->name('counter.report');
    Route::post('/report', [VisitorsCounterController::class, 'increment'])->name('counter.increment');
    Route::post('/multiple', [VisitorsCounterController::class, 'incrementMultiple'])->name('counter.increment.multiple');
    Route::get('/staff-mail', [StaffMailController::class, 'inbox'])->name('staff-mail.inbox');
    Route::get('/staff-mail/sent', [StaffMailController::class, 'sent'])->name('staff-mail.sent');
    Route::get('/staff-mail/compose', [StaffMailController::class, 'create'])->name('staff-mail.create');
    Route::post('/staff-mail', [StaffMailController::class, 'store'])->name('staff-mail.store');
    Route::get('/staff-mail/attachments/{message}/preview', [StaffMailController::class, 'previewAttachment'])
        ->name('staff-mail.attachments.preview');
    Route::get('/staff-mail/attachments/{message}', [StaffMailController::class, 'downloadAttachment'])
        ->name('staff-mail.attachments.download');
    Route::get('/staff-mail/{conversation}', [StaffMailController::class, 'show'])->name('staff-mail.show');
    Route::post('/staff-mail/{conversation}/reply', [StaffMailController::class, 'reply'])->name('staff-mail.reply');
});
Route::middleware('auth')->group(function () {
    Route::get('/chatbot', [VisitorsCounterController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot', [VisitorsCounterController::class, 'respond'])->name('chatbot.respond');
});
Route::middleware(['auth', 'chatbot.trainer', 'throttle:10,1'])->prefix('admin/chatbot-training')->name('chatbot.training.')->group(function () {
    Route::get('/', [ChatbotTrainingController::class, 'index'])->name('index');
    Route::post('/examples', [ChatbotTrainingController::class, 'store'])->name('examples.store');
    Route::post('/train', [ChatbotTrainingController::class, 'train'])->name('train');
});
Route::middleware(['auth', 'admin'])->prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('index');
    Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
});
Route::middleware(['auth', 'meeting.access:administrative'])->prefix('admin/meetings')->name('admin.meetings.')->group(function () {
    Route::get('/', [MeetingController::class, 'index'])->name('index');
    Route::get('/{meeting}/minutes', [MeetingController::class, 'downloadMinutes'])->name('minutes.download');
    Route::get('/{meeting}/audio', [MeetingController::class, 'playAudio'])->name('audio');

    Route::middleware('meeting.access:administrative,manage')->group(function () {
        Route::get('/create', [MeetingController::class, 'create'])->name('create');
        Route::post('/', [MeetingController::class, 'store'])->name('store');
        Route::get('/{meeting}/edit', [MeetingController::class, 'edit'])->name('edit');
        Route::put('/{meeting}', [MeetingController::class, 'update'])->name('update');
        Route::delete('/{meeting}', [MeetingController::class, 'destroy'])->name('destroy');
    });
});
Route::middleware(['auth', 'meeting.access:academic'])->prefix('academics/meetings')->name('academics.meetings.')->group(function () {
    Route::get('/', [AcademicMeetingController::class, 'index'])->name('index');
    Route::get('/{meeting}/minutes', [AcademicMeetingController::class, 'downloadMinutes'])->name('minutes.download');
    Route::get('/{meeting}/audio', [AcademicMeetingController::class, 'playAudio'])->name('audio');

    Route::middleware('meeting.access:academic,manage')->group(function () {
        Route::get('/create', [AcademicMeetingController::class, 'create'])->name('create');
        Route::post('/', [AcademicMeetingController::class, 'store'])->name('store');
        Route::get('/{meeting}/edit', [AcademicMeetingController::class, 'edit'])->name('edit');
        Route::put('/{meeting}', [AcademicMeetingController::class, 'update'])->name('update');
        Route::delete('/{meeting}', [AcademicMeetingController::class, 'destroy'])->name('destroy');
    });
});
Route::get('/visitor-summary', [VisitorsCounterController::class, 'getVisitorSummary'])->name('visitor.summary');
Route::get('/visitor-data', [VisitorsCounterController::class, 'getVisitorDataForChatbot'])->name('visitor.data');
Route::post('/analyze-text', [VisitorsCounterController::class, 'analyzeTextWithSpacy']);
Route::get('/analyze-test', [VisitorsCounterController::class, 'testPythonScript']);
Route::post('/counter/decrement', [VisitorsCounterController::class, 'decrement'])->name('counter.decrement');
Route::get('/visitor_stats', [reportController::class, 'index'])->name('visitor_stats');
Route::get('/news_leter', [NewsLetterController::class, 'index'])->name('news_leter');
Route::post('/news_leter_store', [NewsLetterController::class, 'store'])->name('news_leter.store');
Route::get('/data', [reportController::class, 'getData'])->name('report.data');
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/calendar', [LibraryCalendarController::class, 'index'])->name('calendar.index');

    Route::middleware('calendar.manager')->prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/create', [LibraryCalendarController::class, 'create'])->name('create');
        Route::post('/', [LibraryCalendarController::class, 'store'])->name('store');
        Route::get('/{event}/edit', [LibraryCalendarController::class, 'edit'])->name('edit');
        Route::put('/{event}', [LibraryCalendarController::class, 'update'])->name('update');
        Route::delete('/{event}', [LibraryCalendarController::class, 'destroy'])->name('destroy');
    });

    Route::get('/proposals/{proposal}/download', [reportController::class, 'downloadAttachment'])->name('proposals.download');
    Route::get('/proposal', [reportController::class, 'indexProposal'])->name('proposal');
    Route::get('/proposals/search', [reportController::class, 'search'])->name('proposals.search');

    Route::middleware('proposal.manager')->group(function () {
        Route::get('/proposals/create', [reportController::class, 'createProposal'])->name('proposals.create');
        Route::post('/proposals/store', [reportController::class, 'storeProposal'])->name('proposals.store');
    });
});


Route::get('/test-script-path', function () {
    $scriptPath = base_path('analyze_text_spacy.py');
    return response()->json(['scriptPath' => $scriptPath]);
});


require __DIR__.'/auth.php';
