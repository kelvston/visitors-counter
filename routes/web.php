<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorsCounterController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\reportController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ExpenseController;
use App\Exports\StockAdjustmentTemplateExport;
use Illuminate\Support\Facades\URL;

use Maatwebsite\Excel\Facades\Excel;



Route::get('/products/autocomplete', [ProductController::class, 'autocomplete'])->name('products.autocomplete');
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');

Route::get('/admin/audit-logs', function () {
    return view('admin.audit_logs', [
        'logs' => \App\Models\AuditLog::with('user')->latest()->paginate(50)
    ]);
})->middleware('auth');

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
    Route::get('/stock-adjustment', [ProductController::class, 'showAdjustmentForm'])->name('stock.adjust.form');
    Route::post('/stock-adjustment', [ProductController::class, 'adjustStock'])->name('stock.adjust');
    Route::get('/inventory/audit-log', [InventoryController::class, 'auditLog'])->name('inventory.audit-log');
    Route::get('/inventory/audit-log/export/{format}', [InventoryController::class, 'exportAuditLog'])->name('inventory.audit-log.export');
    Route::post('/stock/import', [ProductController::class, 'importExcel'])->name('stock.import');


    //report//
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profitLoss');
        Route::get('/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
        Route::get('/reports/expenses', [ReportController::class, 'expensesReport'])->name('reports.expensess');
        Route::get('/top-selling', [ReportController::class, 'topSelling'])->name('reports.topSelling');
        Route::get('/user-performance', [ReportController::class, 'userPerformance'])->name('reports.userPerformance');
        Route::get('/reports/sales/download', [ReportController::class, 'downloadCSV'])->name('reports.sales.download');
        Route::get('/reports/products/download', [ReportController::class, 'downloadProductsCSV'])->name('reports.products.download');
        Route::get('/reports/users/download', [ReportController::class, 'downloadUsersCSV'])->name('reports.users.download');
        Route::get('/profit-loss/download', [ReportController::class, 'downloadProfitLossCSV'])->name('reports.profitLoss.download');
        Route::get('/stock/export', [ReportController::class, 'exportStock'])->name('reports.stock.export');
        Route::get('/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stockMovement');

        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');           // List with filters & pagination
            Route::get('/create', [ExpenseController::class, 'create'])->name('create');  // Show create form
            Route::post('/', [ExpenseController::class, 'store'])->name('store');         // Save new expense
            Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit'); // Show edit form
            Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');  // Update expense
            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy'); // Delete expense
        });
    });
    Route::get('/report-summary/pdf', [ReportController::class, 'summaryPdf'])->name('reports.summary.pdf');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

});
Route::get('/inventory/stock-template', function () {
    return Excel::download(new StockAdjustmentTemplateExport, 'stock_adjustment_template.xlsx');
})->name('inventory.export-template');
//Route::get('/products/autocomplete', [ProductController::class, 'autocomplete'])->name('products.autocomplete');







Route::get('/test-script-path', function () {
    $scriptPath = base_path('analyze_text_spacy.py');
    return response()->json(['scriptPath' => $scriptPath]);
});



Route::middleware(['auth'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit_logs.export');

});


Route::get('/manage-users', [UserRoleController::class, 'index'])->name('users.index');
Route::post('/manage-users/{user}', [UserRoleController::class, 'update'])->name('users.update');
Route::get('/sales/today-distribution', [ProfileController::class, 'todayDistribution'])->name('sales.today_distribution');
Route::post('/sales/decrement', [ProfileController::class, 'decrement'])->name('sales.decrement');
Route::get('/sales/summary', [ProductController::class, 'salesSummary'])->name('sales.summary');
Route::get('/sales/recent', [ProductController::class, 'recentSales'])->name('sales.recent');
Route::get('/products/index', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::get('/products/destroy', [ProductController::class, 'destroy'])->name('products.destroy');
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::post('/products/store_product', [ProductController::class, 'storeProduct'])->name('products.store_product');
Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
Route::post('/products/adjust', [ProductController::class, 'adjustStock'])->name('products.adjust');






require __DIR__.'/auth.php';
