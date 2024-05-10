<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [NoteController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('notes', NoteController::class)->middleware(['auth', 'verified']);

Route::get('toggle_theme', function (){
    if(auth()->user()->theme == 'dark'){
        auth()->user()->update(['theme' => 'light']);
    }else{
        auth()->user()->update(['theme' => 'dark']);
    }
    return json_encode(['theme' => auth()->user()->theme]);
});

require __DIR__.'/auth.php';
