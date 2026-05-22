<?php

use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\PairController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('exchanges', ExchangeController::class);
    Route::post('exchanges/getData', [ExchangeController::class, 'getData'])->name('exchanges.getData');
    
    Route::resource('pairs', PairController::class);
    Route::post('pairs/getData', [PairController::class, 'getData'])->name('pairs.getData');
});

require __DIR__.'/auth.php';
