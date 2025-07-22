<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\ProfileController;

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

// Rute '/dashboard' bawaan Breeze (dikomentari/dihapus)
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Rute untuk manajemen profil pengguna (bawaan Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Grup rute untuk supervisor, dilindungi oleh middleware 'auth'
Route::middleware(['auth'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/tasks', [SupervisorDashboardController::class, 'storeTask'])->name('tasks.store');
    
    // Rute untuk menampilkan log tugas
    Route::get('/task-log', [SupervisorDashboardController::class, 'taskLog'])->name('task.log');
    
    // Rute untuk menandai tugas sebagai 'Done'
    Route::post('/tasks/{task_id}/done', [SupervisorDashboardController::class, 'markTaskDone'])->name('task.done');

    // Rute untuk halaman detail pekerja
    Route::get('/pekerja/{id_pekerja}/detail', [SupervisorDashboardController::class, 'showPekerjaDetail'])->name('pekerja.detail');

    // RUTE INI ADALAH UNTUK MENGAKSES pejerja_list.blade.php
    Route::get('/pekerja', [SupervisorDashboardController::class, 'listAllPekerja'])->name('pekerja.list');
});

// Ini adalah rute-rute autentikasi yang disediakan oleh Laravel Breeze.
require __DIR__.'/auth.php';