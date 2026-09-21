<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Admin CMS Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest authentication routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Authenticated admin routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminPageController::class, 'index'])->name('dashboard');
        Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
        Route::post('/cache/clear', [AdminPageController::class, 'clearCache'])->name('cache.clear');
        Route::get('/password', [AdminPageController::class, 'showPassword'])->name('password');
        Route::put('/password', [AdminPageController::class, 'updatePassword'])->name('password.update');
    });
});

// Public Website Routes
Route::redirect('/login', '/admin/login')->name('login');
Route::redirect('/contacts', '/kontakti/');
Route::get('/', [PageController::class, 'show']);
Route::get('/{any}', [PageController::class, 'show'])->where('any', '.*');


