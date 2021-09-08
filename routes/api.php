<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\NewPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\EmailVerificationController;

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
//default route in api file
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//to send verification link
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifiy'])->middleware(['auth:sanctum'])->name('verification.verify');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('forget-password', [NewPasswordController::class, 'forgetPassword']);
Route::post('reset-password', [NewPasswordController::class, 'reset']);
Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');


Route::resource('/users', UsersController::class);
Route::get('/users/search/{name}',[UsersController::class,'search']);

//protected routes
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
    Route::post('/logout',[AuthController::class,'logout']);
});
