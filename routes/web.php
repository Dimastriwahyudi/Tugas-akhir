<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\Maps\WarungController;


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

Route::middleware(['auth', 'role:superadmin|admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('users/{user}/reset-password',  [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('activity-logs', [UserController::class, 'activityLog'])->name('activity.log');   
    });

Route::middleware(['auth'])->group(function () {
    Route::get('maps',                              [WarungController::class, 'index'])->name('maps.index');
    Route::post('maps/warung',                      [WarungController::class, 'store'])->name('maps.store');
    Route::put('maps/warung/{warung}',              [WarungController::class, 'update'])->name('maps.update');
    Route::post('maps/warung/{warung}/kunjungan',   [WarungController::class, 'tambahKunjungan'])->name('maps.kunjungan');
    Route::get('maps/warung/{warung}/riwayat',      [WarungController::class, 'riwayat'])->name('maps.riwayat');
});

Route::get('maps-test', function() {
    return response()->json(['ok' => true]);
})->middleware('auth');

require __DIR__.'/auth.php';
