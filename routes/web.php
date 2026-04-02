<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\BookingController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Pure AJAX Booking API Routes
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/staffs', [BookingController::class, 'getStaffs'])->name('staffs');
    Route::get('/slots', [BookingController::class, 'getSlots'])->name('slots');
    Route::post('/submit', [BookingController::class, 'submitBooking'])->name('submit');
});

// Services
Route::get('/dich-vu', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/dich-vu/{slug}', [App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');
// Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');

/* =========================================================
 * ADMIN PANEL ROUTES
 * ========================================================= */
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Auth Routes (Guest)
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected (Admin Middleware)
    Route::middleware(['web', 'admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // System Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // CRUD Services & Portfolio (Tạm thời map các route cơ bản)
        Route::resource('services', AdminServiceController::class);
        Route::patch('services/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle_status'); // Quick Action
        
        Route::resource('portfolios', AdminPortfolioController::class);

        // Media Library Routes
        Route::prefix('media')->name('media.')->group(function() {
            Route::get('/', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
            Route::post('/upload', [App\Http\Controllers\Admin\MediaController::class, 'store'])->name('upload');
            Route::patch('/update/{media}', [App\Http\Controllers\Admin\MediaController::class, 'update'])->name('update');
            Route::delete('/delete/{media}', [App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [App\Http\Controllers\Admin\MediaController::class, 'bulkDelete'])->name('bulk_delete');
            Route::post('/move', [App\Http\Controllers\Admin\MediaController::class, 'moveFolder'])->name('move');
            Route::get('/folders', [App\Http\Controllers\Admin\MediaController::class, 'listFolders'])->name('folders');
            
            // View Thư viện chính
            Route::get('/view', function() {
                return view('admin.media.index');
            })->name('view');
        });
    });
});
