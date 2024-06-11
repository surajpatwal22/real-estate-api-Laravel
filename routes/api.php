<?php

use App\Http\Controllers\Admin\Api\FavoritePropertyController;
use App\Http\Controllers\Admin\Api\PropertyController;
use App\Http\Controllers\Admin\Api\UserController;
use App\Http\Controllers\Admin\CategoryController;
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
    Route::post('like',[PropertyController::class,'like']);
    Route::get('likedProperty', [PropertyController::class,'userLikedPropertyList']);
    Route::post('addFavorite/{id}', [FavoritePropertyController::class, 'add']);
    Route::get('getFavorites', [FavoritePropertyController::class, 'getAll']);
});

Route::post('signup',[UserController::class,'SignUp'])->name("signup");
Route::post('login',[UserController::class,'login'])->name("login");
Route::get('allPropertry', [PropertyController::class,'getallproperty']);

Route::get('allPropertry/{id}', [PropertyController::class,'getproperty']);
Route::get('rentedProperty', [PropertyController::class,'getrentproperty']);
Route::get('buyProperty', [PropertyController::class,'getbuyproperty']);
Route::get('searchProperty', [PropertyController::class,'searchproperty']);
Route::get('property',[PropertyController::class,'sortproperty']);
Route::get('category/{id}', [CategoryController::class,'getCategorySub'] );

