<?php

use App\Http\Controllers\ConversationController;
use Illuminate\Support\Facades\Route;

Route::prefix('conversations')->middleware(['auth'])->group(function () {
    Route::get('/', [ConversationController::class, 'index']);
    Route::get('/{receiverId}', [ConversationController::class, 'show']);

    Route::post('/', [ConversationController::class, 'addMessage']);

    Route::patch('/{conversation}/read', [ConversationController::class, 'markMessageRead']);

    Route::delete('/{conversation}/all', [ConversationController::class, 'deleteAllMessage']);
    Route::delete('/{message}', [ConversationController::class, 'deleteMessage']);
});
