<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\ProductController;

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


Route::post('login',[UserController::class,'loginUser']);
Route::get('bilan',[UserController::class,'user_bilan']);
Route::get('arrondissement',[UserController::class,'best_arrond']);
Route::get('commune',[UserController::class,'best_comm']);
Route::get('department',[UserController::class,'best_depart']);
Route::get('groupement',[UserController::class,'best_gv']);
Route::get('general',[UserController::class,'best_general']);





Route::group(['middleware' => 'auth:sanctum'],function(){
    Route::get('user',[UserController::class,'userDetails']);
    Route::get('logout',[UserController::class,'logout']);
});

Route::resource('role', RoleController::class);
Route::resource('user', UserController::class);
Route::resource('field', FieldController::class);
Route::resource('product', ProductController::class);
Route::resource('harvest', HarvestController::class);




