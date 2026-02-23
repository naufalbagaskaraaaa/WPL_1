<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PdfController;

Route::redirect('/', '/login');

Route::get('/test', function(){
    return view('test');
});

Auth::routes(); 

Route::middleware(['auth'])->group(function () {
Route::get('/dashboard', function () { 
    return view('dashboard'); })->name('dashboard');

Route::get('/kategori', [KategoriController::class, 'create'])
->name('kategori.create');
Route::post('/kategori', [KategoriController::class, 'store'])
->name('kategori.store');

Route::get('/buku', [BukuController::class, 'create'])
->name('buku.create');
Route::post('/buku', [BukuController::class, 'store'])
->name('buku.store');
});

Route::get('/generate-pdf', [PdfController::class, 'generatePDFLandscape'])->name('generate.pdf.landscape');
Route::get('/generate-undangan', [PdfController::class, 'generatePDFPortrait'])->name('generate.pdf.portrait');