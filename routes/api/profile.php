<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->middleware(['auth'])->group(function () {
    Route::patch('/', [ProfileController::class, 'update']);
    Route::patch('/password', [ProfileController::class, 'changePassword']);
});
