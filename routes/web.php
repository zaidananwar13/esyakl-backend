<?php

use App\Http\Controllers\API\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| API-only application. Web routes kept minimal.
|
*/

Route::get('/', function () {
    return response()->json(['message' => 'E-Syakl API']);
});

// Email verification callback (used by API email verification flow)
Route::get('mail-verify/{token}', [LoginController::class, 'verifyEmail']);
