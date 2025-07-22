<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\ProfileController; // Biarkan ini

Route::get('/whoami', function () {
    if (Auth::check()) {
        return 'Anda login sebagai: ' . Auth::user()->email;
    }
    return 'Anda TIDAK LOGIN.';
})->middleware('auth'); // <-- Tetapkan middleware 'auth' di sini

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute default '/'
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('supervisor.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Komentar atau hapus rute dashboard bawaan Breeze ini
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Rute profil (dari Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Grup rute untuk supervisor
Route::middleware(['auth'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/tasks', [SupervisorDashboardController::class, 'storeTask'])->name('tasks.store');
    Route::get('/task-log/{pekerja_id?}/{task_id?}', [SupervisorDashboardController::class, 'taskLog'])->name('task.log');
     Route::post('/tasks/{task_id}/done', [SupervisorDashboardController::class, 'markTaskDone'])->name('task.done');
});

// PASTIKAN BARIS INI ADA DI BAGIAN PALING BAWAH
require __DIR__.'/auth.php';