<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorsCounterController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ExpenseController;
use App\Exports\StockAdjustmentTemplateExport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

if (app()->environment('local')) {
    URL::forceRootUrl(config('app.url'));
    URL::forceScheme('http'); // Use https if SSL locally
}

// Public routes
Route::get('/', fn () => view('welcome'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Profile management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Visitors counter and chatbot
    Route::prefix('counter')->group(function () {
        Route::get('/report', [VisitorsCounterController::class, 'show'])->name('counter.report');
        Route::post('/increment', [VisitorsCounterController::class, 'increment'])->name('counter.increment');
        Route::post('/increment-multiple', [VisitorsCounterController::class, 'incrementMultiple'])->name('counter.increment.multiple');
        Route::post('/decrement', [VisitorsCounterController::class, 'decrement'])->name('counter.decrement');
    });

    Route::get('/chatbot', [VisitorsCounterController::class, 'index']);
    Route::post('/chatbot', [VisitorsCounterController::class, 'respond']);
    Route::get('/visitor-summary', [VisitorsCounterController::class, 'getVisitorSummary'])->name('visitor.summary');
    Route::get('/visitor-data', [VisitorsCounterController::class, 'getVisitorDataForChatbot'])->name('visitor.data');
    Route::post('/analyze-text', [VisitorsCounterController::class, 'analyzeTextWithSpacy']);
    Route::get('/analyze-test', [VisitorsCounterController::class, 'testPythonScript']);

    // Newsletter
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::get('/', [NewsLetterController::class, 'index'])->name('index');
        Route::post('/store', [NewsLetterController::class, 'store'])->name('store');
    });

    // Products routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/autocomplete', [ProductController::class, 'autocomplete'])->name('autocomplete');
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::post('/store-product', [ProductController::class, 'storeProduct'])->name('store_product');
        Route::post('/import', [ProductController::class, 'import'])->name('import');
        Route::post('/adjust', [ProductController::class, 'adjustStock'])->name('adjust');
        Route::get('/destroy', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/export', [ProductController::class, 'export'])->name('export');

        // Stock adjustment form
        Route::get('/stock-adjustment', [ProductController::class, 'showAdjustmentForm'])->name('stock.adjust.form');
        Route::post('/stock-adjustment', [ProductController::class, 'adjustStock'])->name('stock.adjust');
        Route::post('/stock/import', [ProductController::class, 'importExcel'])->name('stock.import');
    });

    // Inventory
    Route::prefix('inventory')->group(function () {
        Route::get('/audit-log', [InventoryController::class, 'auditLog'])->name('inventory.audit-log');
        Route::get('/audit-log/export/{format}', [InventoryController::class, 'exportAuditLog'])->name('inventory.audit-log.export');
        Route::get('/stock-template', function () {
            return Excel::download(new StockAdjustmentTemplateExport, 'stock_adjustment_template.xlsx');
        })->name('inventory.export-template');
    });

    // Audit logs
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
    });

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // User roles management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/manage', [UserRoleController::class, 'index'])->name('index');
        Route::post('/manage/{user}', [UserRoleController::class, 'update'])->name('update');
    });

    // Sales routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/today-distribution', [ProfileController::class, 'todayDistribution'])->name('today_distribution');
        Route::post('/decrement', [ProfileController::class, 'decrement'])->name('decrement');
        Route::get('/summary', [ProductController::class, 'salesSummary'])->name('summary');
        Route::get('/recent', [ProductController::class, 'recentSales'])->name('recent');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profitLoss');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/top-selling', [ReportController::class, 'topSelling'])->name('topSelling');
        Route::get('/user-performance', [ReportController::class, 'userPerformance'])->name('userPerformance');
        Route::get('/stock-movement', [ReportController::class, 'stockMovement'])->name('stockMovement');

        // Downloads
        Route::get('/sales/download', [ReportController::class, 'downloadCSV'])->name('sales.download');
        Route::get('/products/download', [ReportController::class, 'downloadProductsCSV'])->name('products.download');
        Route::get('/users/download', [ReportController::class, 'downloadUsersCSV'])->name('users.download');
        Route::get('/profit-loss/download', [ReportController::class, 'downloadProfitLossCSV'])->name('profitLoss.download');
        Route::get('/stock/export', [ReportController::class, 'exportStock'])->name('stock.export');

        // Expenses nested routes (resource-like)
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::get('/create', [ExpenseController::class, 'create'])->name('create');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
            Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });

        Route::get('/summary/pdf', [ReportController::class, 'summaryPdf'])->name('summary.pdf');
    });

});

// Misc
Route::get('/test-script-path', function () {
    return response()->json(['scriptPath' => base_path('analyze_text_spacy.py')]);
});

require __DIR__.'/auth.php';
