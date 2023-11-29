<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuestController;
use App\Http\Controllers\API\PostcardTemplateController;
use App\Http\Controllers\API\PostcardController;
use App\Http\Controllers\API\IncludedFileController;
use App\Http\Controllers\API\DonationController;
use App\Http\Controllers\API\TransactionController;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

Route::group(['prefix'=>'v1','middleware'=>'mycors'],function () {
    // Auth Routes
    Route::post('admin/login', [AuthController::class, 'login']);
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh-token', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Guest Routes
    Route::get('guests', [GuestController::class, 'index']);
    Route::post('guests', [GuestController::class, 'store']);
    Route::delete('guests/{id}', [GuestController::class, 'destroy'])->middleware('jwt.verify');

    // Postcard Template Routes
    Route::get('postcard-templates', [PostcardTemplateController::class, 'index']);
    Route::post('postcard-templates', [PostcardTemplateController::class, 'store']);
    Route::put('postcard-templates/{id}', [PostcardTemplateController::class, 'update']);
    Route::delete('postcard-templates/{id}', [PostcardTemplateController::class, 'destroy']);

    // Included File Routes
    Route::get('included-files', [IncludedFileController::class, 'index']);
    Route::post('included-files', [IncludedFileController::class, 'store']);
    Route::put('included-files/{id}', [IncludedFileController::class, 'update']);
    Route::delete('included-files/{id}', [IncludedFileController::class, 'destroy']);

    // Postcard
    Route::get('postcards', [PostcardController::class, 'index']);
    Route::post('postcards', [PostcardController::class, 'store']);
    Route::post('postcards/{id}/send-email', [PostcardController::class, 'sendEmail']);

    // Donation
    Route::get('donations', [DonationController::class, 'index']);
    Route::post('donations', [DonationController::class, 'store']);

    // Transaction
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::post('transactions', [TransactionController::class, 'store']);
});

// Test Route
// Route::get('test', function () {
//     try {
//         $subject = "Reset Password Request";
//         $content = [
//             'url' => 'http://localhost:8000/reset-password?token=1234567890',
//         ];

//         Mail::to('ajipunk008@gmail.com')->send(new SendEmail($subject, 'emails.reset-password', $content));

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Email sent successfully',
//         ]);
//     } catch (\Throwable $th) {
//         Log::error($th->getMessage());
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Email sent failed',
//         ], 500);
//     }
// });
