<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MainCore\ProjectController;
use App\Http\Controllers\MainCore\SiteInfoController;
use App\Http\Controllers\MainCore\UserController;
use App\Http\Controllers\MainCore\UserProjectController;
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
    Route::get('/info', [UserController::class, 'userInfo']);
    Route::Post('/info/edit', [UserController::class, 'editInfo']);
    Route::get('/companies', [UserController::class, 'companies']);
    Route::post("/mark", [UserController::class, 'mark']);
    Route::post("/unmark", [UserController::class, 'unMark']);
    Route::get("/markprojects", [UserController::class, 'markProjects']);
    Route::get("/tickets", [UserController::class, 'tickets']);
    Route::post("/ticket", [UserController::class, 'ticket']);
    Route::post("/unticket", [UserController::class, 'unticket']);
    Route::get("/ticketprojects", [UserController::class, 'ticketProjects']);
    Route::get("/cancreate", [UserController::class, 'canCreateProject']);
    Route::get("/projects", [UserProjectController::class, 'userProjects']);


    Route::post("/test", [UserProjectController::class, 'userProjects']);
});
Route::group([
    'prefix' => 'user/projects',
    'middleware' => [
        'loginStatus'
    ]
], function () {
    Route::get("/all", [UserProjectController::class, 'index']);
    Route::get("/{id}", [UserProjectController::class, 'show']);
    Route::post("/{id}/edit", [UserProjectController::class, 'edit']);
    Route::post("/create", [UserProjectController::class, 'create']);
    Route::delete("/{id}/edit", [UserProjectController::class, 'destroy']);
});
