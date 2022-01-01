<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\AdminsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'customers'], function () {
    Route::post('/create', [CustomersController::class, 'register']);
    Route::post('/login', [CustomersController::class, 'login']);
    Route::get('/login/google', [CustomersController::class,'redirectToProvider']);
    Route::get('/callback', [CustomersController::class,'handleProviderCallback']);
    Route::post('/logout', [CustomersController::class, 'logout'])->middleware(['auth:sanctum', 'type.customer']);
    Route::get('/getData', [CustomersController::class, 'getData'])->middleware(['auth:sanctum', 'type.customer']);
    Route::post('/uploadImage', [CustomersController::class, 'uploadImage'])->middleware(['auth:sanctum', 'type.customer']);
    Route::post('/password/email',[CustomersController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [CustomersController::class, 'reset']);
});

Route::group(['prefix' => 'admins'], function () {
    Route::post('/create', [AdminsController::class, 'createAdmin']);
});