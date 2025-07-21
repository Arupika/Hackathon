<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupervisorDashboardController;

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

Route::get('/', function () {
    return view('welcome'); // Biarkan dulu jika belum dihapus
});

// Grup rute untuk supervisor, bisa ditambahkan middleware 'auth' nanti
Route::prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/tasks', [SupervisorDashboardController::class, 'storeTask'])->name('tasks.store');
});