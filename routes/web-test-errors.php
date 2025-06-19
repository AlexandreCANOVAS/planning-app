<?php

use Illuminate\Support\Facades\Route;

// Routes temporaires pour tester les pages d'erreur
// IMPORTANT: À supprimer en production

Route::prefix('test-errors')->middleware(['auth'])->group(function () {
    Route::get('/404', function () {
        return response()->view('errors.404', [], 404);
    })->name('test.404');
    
    Route::get('/403', function () {
        return response()->view('errors.403', [], 403);
    })->name('test.403');
    
    Route::get('/500', function () {
        return response()->view('errors.500', [], 500);
    })->name('test.500');
    
    Route::get('/419', function () {
        return response()->view('errors.419', [], 419);
    })->name('test.419');
    
    Route::get('/503', function () {
        return response()->view('errors.503', [], 503);
    })->name('test.503');
});
