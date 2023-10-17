<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuestController;
use App\Http\Controllers\API\PostcardTemplateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('admin/login', [AuthController::class, 'login']);
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh-token', [AuthController::class, 'refresh'])->middleware('auth:api');

    // Guest Routes
    Route::get('guests', [GuestController::class, 'index']);
    Route::post('guests', [GuestController::class, 'store']);
    Route::delete('guests/{id}', [GuestController::class, 'destroy']);

    // Postcard Template Routes
    Route::get('postcard-templates', [PostcardTemplateController::class, 'index']);
    Route::post('postcard-templates', [PostcardTemplateController::class, 'store']);
    Route::put('postcard-templates/{id}', [PostcardTemplateController::class, 'update']);
    Route::delete('postcard-templates/{id}', [PostcardTemplateController::class, 'destroy']);
});

// Test Route
Route::get('test', function () {
    return response()->json([
        'message' => generateUuid()
    ]);
});
