<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    echo env('APP_NAME');
});

Route::get('/search-people', [SearchController::class, 'searchPeople'])
        ->middleware(['auth']);

require __DIR__.'/auth.php';
require __DIR__.'/api/conversations.php';
