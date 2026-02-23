<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;

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

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('auth/verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
Route::post('auth/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.process');