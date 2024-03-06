<?php

use App\Http\Controllers\FieldController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::resource('role', RoleController::class);
Route::resource('user', UserController::class);
Route::resource('field', FieldController::class);
Route::resource('product', ProductController::class);
Route::resource('harvest', HarvestController::class);




