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
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/redirect', [RedirectController::class, 'cek']);
});

Route::group(['middleware' => ['auth', 'checkrole:1']], function() {
    Route::get('/administrator', [AdminController::class, 'index'])->name('administrator'); //proses login
    Route::get('/guru', [AdminController::class, 'guru'])->name('guru'); //proses login
    Route::post('/simpan', [AdminController::class, 'simpan'])->name('simpan');
    Route::post('/hapus', [AdminController::class, 'hapus'])->name('hapusguru');
    Route::get('/gantiscan', [AdminController::class, 'gantiscan'])->name('gantiscan');//proses login
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('kelas'); //menu kelas
    Route::post('/simpankelas', [AdminController::class, 'simpankelas'])->name('simpankelas');
    
    
});

Route::group(['middleware' => ['auth', 'checkrole:2']], function() {
    Route::get('/kepsek', [AdminController::class, 'kepsek'])->name('kepsek'); //proses login
       
});

Route::get('/gurujson', [AdminController::class, 'gurujson'])->name('gurujson');
Route::get('/kepsekjson', [AdminController::class, 'kepsekjson'])->name('kepsekjson');
Route::get('/kelasjson', [AdminController::class, 'kelasjson'])->name('kelasjson');
Route::post('/rfid', [AdminController::class, 'rfid']);
Route::get('/absenkeluar/{id}', [AdminController::class, 'absenkeluar']);
Route::post('/loginguru', [AdminController::class, 'loginguru']);
Route::get('/cekjadwal/{id}', [AdminController::class, 'cekjadwal']);
Route::get('/absenjson', [AdminController::class, 'absenjson'])->name('absen.json');
Route::get('/grafik/{id}', [AdminController::class, 'grafik']); //halaman login
Route::get('/grafikdetail/{id}', [AdminController::class, 'grafikdetail']); //halaman login



