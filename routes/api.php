<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Matches "/api/register
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
// Auth
Route::get('profile', [UserController::class, 'profile']);


//Products
Route::controller(ProductController::class)->group(function () {
    Route::get('products/latest', 'latest');
    Route::get('products/featured', 'featured');
    Route::get('products/catalog', 'catalog');
    Route::post('products', 'create');
    Route::put('products/{id}', 'update');
    Route::get('products/{id}', 'show');
});

//Categories
Route::controller(CategoryController::class)->group(function () {
    Route::get('categories','index');
    Route::post('categories','create');
    Route::delete('categories/{id}','delete');
    Route::put('categories/{id}', 'update');
    Route::post('categories/{id}/visible','visible');
});
