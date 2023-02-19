<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MainCore\ProjectController;
use App\Http\Controllers\MainCore\SiteInfoController;
use App\Http\Controllers\MainCore\UserController;
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
Route::get('/userInfo', [LoginController::class, 'userInfo']);



Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/project/{id}', [ProjectController::class, 'show']);


Route::get('/states', [SiteInfoController::class, 'states']);
Route::get('/categories', [SiteInfoController::class, 'categories']);
Route::get('/cities', [SiteInfoController::class, 'cities']);
Route::get('/subcategories', [SiteInfoController::class, 'subCategories']);


Route::group([
    'prefix' => 'user',
    'middleware' => [
        'loginStatus'
    ]
], function () {
    Route::get('/companies', [UserController::class, 'companies']);
    Route::post("/mark", [UserController::class, 'mark']);
    Route::post("/unmark", [UserController::class, 'unMark']);
    Route::get("/markprojects", [UserController::class, 'markProjects']);
    Route::get("/tikets", [UserController::class, 'tikets']);
    Route::post("/tiket", [UserController::class, 'tiket']);
    Route::post("/untiket", [UserController::class, 'untiket']);
    Route::get("/tiketprojects", [UserController::class, 'tiketProjects']);



    Route::post("/test", [UserController::class, 'test']);
});
