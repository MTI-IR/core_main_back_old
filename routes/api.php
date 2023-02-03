<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MainCore\ProjectController;
use App\Http\Controllers\MainCore\SiteInfoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'login']);



Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/project/{id}', [ProjectController::class, 'show']);


Route::get('/states', [SiteInfoController::class, 'states']);
Route::get('/state/{state_id}', [SiteInfoController::class, 'states']);
