<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'halamanlogin'])->name('login'); //halaman login
Route::post('/', [LoginController::class, 'proseslogin'])->name('proseslogin'); //proses login

Route::group(['middleware' => ['auth', 'checkrole:1,2,3']], function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/redirect', [RedirectController::class, 'cek']);
});

Route::group(['middleware' => ['auth', 'checkrole:1']], function() {
    Route::get('/administrator', [AdminController::class, 'index'])->name('administrator'); //proses login
    Route::get('/guru', [AdminController::class, 'guru'])->name('guru'); //proses login
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('kelas'); //proses login
    Route::post('/simpan', [AdminController::class, 'simpan'])->name('simpan');
    Route::post('/simpankelas', [AdminController::class, 'simpankelas'])->name('simpankelas');
    Route::get('/gantiscan', [AdminController::class, 'gantiscan'])->name('gantiscan');//proses login
});

Route::get('/gurujson', [AdminController::class, 'gurujson'])->name('gurujson');
Route::get('/cekhari', [AdminController::class, 'cekhari'])->name('cekhari');
Route::get('/kelasjson', [AdminController::class, 'kelasjson'])->name('kelasjson');
Route::post('/rfid/', [AdminController::class, 'rfid']);



