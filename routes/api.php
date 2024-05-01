<?php

use App\Http\Controllers\Admin\Api\PropertyController;
use App\Http\Controllers\Admin\Api\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function (){
    Route::get('get_profile', [UserController::class,'getProfile']);
    Route::post('update_profile', [UserController::class,'updateProfile']);
    Route::post('logout', [UserController::class,'logout']);
    Route::post('addProperty', [PropertyController::class,'addProperty']);
    Route::post('updateProperty/{id}', [PropertyController::class,'update']);
    Route::delete('deleteProperty/{id}', [PropertyController::class,'destroy']);






});

Route::post('signup',[UserController::class,'SignUp'])->name("signup");
Route::post('login',[UserController::class,'login'])->name("login");
Route::get('allPropertry', [PropertyController::class,'getallproperty']);
Route::get('allPropertry/{id}', [PropertyController::class,'getproperty']);



