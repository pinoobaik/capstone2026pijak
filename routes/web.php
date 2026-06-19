<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\AuthController;

// ============================================================
//  RUTE UMUM (Bisa diakses siapa saja tanpa perlu login)
// ============================================================

// Halaman Landing Page Utama
Route::get('/', function () { return view('landing'); })->name('landing');

// Halaman & Proses Pencarian Rekomendasi Resep AI (Menggunakan GET sesuai Form di Blade)
Route::get('/rekomendasi', [RecipeController::class, 'dapatkanRekomendasi'])->name('rekomendasi.cari');

// Rute untuk Fitur Autentikasi (Register & Login)
Route::get('/register', [AuthController::class, 'tampilkanRegister'])->name('register');
Route::post('/register', [AuthController::class, 'prosesRegister'])->name('register.proses');

Route::get('/login', [AuthController::class, 'tampilkanLogin'])->name('login');
Route::post('/login', [AuthController::class, 'prosesLogin'])->name('login.proses');

Route::get('/lupa-password', function () { return view('auth.lupa-password'); })->name('password.request');
Route::post('/lupa-password', [AuthController::class, 'prosesLupaPassword'])->name('password.email');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman untuk menginput password baru (Rute yang dicari oleh Laravel)
Route::get('/reset-password/{token}', [AuthController::class, 'tampilkanResetPassword'])->name('password.reset');

// Proses pembaruan password ke database MySQL
Route::post('/reset-password', [AuthController::class, 'prosesResetPassword'])->name('password.update');

Route::get('/ping', function () {
    return response('OK', 200);
});

//  RUTE PROTEKSI (Hanya bisa diakses oleh USER YANG SUDAH LOGIN)
Route::middleware(['auth'])->group(function () {
    
    // Halaman daftar resep favorit milik user
    Route::get('/favorit', [RecipeController::class, 'indexFavorit'])->name('favorit.index');
    
    // Proses backend untuk menyimpan resep hasil AI ke database favorit
    Route::post('/favorit/tambah', [RecipeController::class, 'tambahFavorit'])->name('favorit.tambah');
    
    // Halaman detail isi bahan dan langkah memasak resep
    Route::get('/resep/{id}', [RecipeController::class, 'showDetail'])->name('resep.detail');
    
    // Halaman histori pencarian user (SUDAH DIPERBAIKI: Mengarah ke Controller)
    Route::get('/histori', [RecipeController::class, 'indexHistori'])->name('histori.index');

});