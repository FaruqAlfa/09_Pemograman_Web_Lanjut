<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Models\Mahasiswa;
use Illuminate\Request;
use Illuminate\Routing\RouteRegistrar;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('mahasiswas/nilai/{Nim}', [MahasiswaController::class, 'khs']);

Route::resource('mahasiswas', MahasiswaController::class);

Route::get('mahasiswas/cetakKHS/{Nim}', [MahasiswaController::class, 'cetak_khs'])->name('print');
