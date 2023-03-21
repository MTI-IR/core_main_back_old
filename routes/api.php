<?php

use App\Http\Controllers\AdminPanel\CategoryController;
use App\Http\Controllers\AdminPanel\CityController;
use App\Http\Controllers\AdminPanel\CompanyController;
use App\Http\Controllers\AdminPanel\PermissionController;
use App\Http\Controllers\AdminPanel\ProjectController as AdminPanelProjectController;
use App\Http\Controllers\AdminPanel\RoleController;
use App\Http\Controllers\AdminPanel\StateController;
use App\Http\Controllers\AdminPanel\SubCategoryController;
use App\Http\Controllers\AdminPanel\TicketController;
use App\Http\Controllers\AdminPanel\UserController as AdminPanelUserController;
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





//===============================================================================================
//admin panel ===================================================================================





Route::group([
    'prefix' => 'admin/user',
], function () {
    Route::get("/all", [AdminPanelUserController::class, 'index']);
    Route::post("/destroy/users", [AdminPanelUserController::class, 'destroyUsers']);
    Route::get("/destroy", [AdminPanelUserController::class, 'destroy']);
    Route::post("/validate/users", [AdminPanelUserController::class, 'validateUsers']);
    Route::post("/edit", [AdminPanelUserController::class, 'edit']);
    Route::post("/assignRole", [AdminPanelUserController::class, 'assignRole']);
    Route::post("/addPermission", [AdminPanelUserController::class, 'addPermission']);
    Route::post("/removePermission", [AdminPanelUserController::class, 'removePermission']);
    Route::post("/changePermissions", [AdminPanelUserController::class, 'changePermissions']);
    Route::post("/create", [AdminPanelUserController::class, 'create']);
    Route::get("/{id}", [AdminPanelUserController::class, 'show']);
});

Route::group([
    'prefix' => 'admin/company',
], function () {
    Route::get("/all", [CompanyController::class, 'index']);
    Route::get("/projects", [CompanyController::class, 'projects']);
    Route::post("/edit", [CompanyController::class, 'edit']);
    Route::post("/create", [CompanyController::class, 'create']);
    Route::post("/destroy", [CompanyController::class, 'destroy']);
    Route::post("/validate", [CompanyController::class, 'validateCompanies']);
    Route::get("/document/{id}", [CompanyController::class, 'document']);
    Route::get("/{id}", [CompanyController::class, 'show']);
});


Route::group([
    'prefix' => 'admin/project',
], function () {
    Route::get("/all", [AdminPanelProjectController::class, 'index']);
    Route::post("/edit", [AdminPanelProjectController::class, 'edit']);
    Route::post("/destroy", [AdminPanelProjectController::class, 'destroy']);
    Route::post("/create", [AdminPanelProjectController::class, 'create']);
    Route::post("/validate", [AdminPanelProjectController::class, 'validateProjects']);
    Route::get("/document/{id}", [AdminPanelProjectController::class, 'document']);
    Route::get("/{id}", [AdminPanelProjectController::class, 'show']);
});


Route::group([
    'prefix' => 'admin/state',
], function () {
    Route::get("/all", [StateController::class, 'index']);
    Route::post("/create", [StateController::class, 'create']);
    Route::post("/destroy", [StateController::class, 'destroy']);
    Route::get("/{id}", [StateController::class, 'show']);
});


Route::group([
    'prefix' => 'admin/city',
], function () {
    Route::get("/all", [CityController::class, 'index']);
    Route::post("/create", [CityController::class, 'create']);
    Route::post("/destroy", [CityController::class, 'destroy']);
    Route::get("/{id}", [CityController::class, 'show']);
});


Route::group([
    'prefix' => 'admin/category',
], function () {
    Route::get("/all", [CategoryController::class, 'index']);
    Route::post("/create", [CategoryController::class, 'create']);
    Route::post("/destroy", [CategoryController::class, 'destroy']);
    Route::get("/{id}", [CategoryController::class, 'show']);
});


Route::group([
    'prefix' => 'admin/subcategory',
], function () {
    Route::get("/all", [SubCategoryController::class, 'index']);
    Route::post("/create", [SubCategoryController::class, 'create']);
    Route::post("/destroy", [SubCategoryController::class, 'destroy']);
    Route::get("/{id}", [SubCategoryController::class, 'show']);
});


Route::group([
    'prefix' => 'admin/ticket',
], function () {
    Route::get("/all", [TicketController::class, 'index']);
    Route::post("/create", [TicketController::class, 'create']);
    Route::post("/destroy", [TicketController::class, 'destroy']);
    Route::get("/{id}", [TicketController::class, 'show']);
});


Route::group([
    'prefix' => 'admin/permission',
], function () {
    Route::get("/all", [PermissionController::class, 'index']);
    Route::post("/create", [PermissionController::class, 'create']);
    Route::post("/destroy", [PermissionController::class, 'destroy']);
    Route::get("/destroyAll", [PermissionController::class, 'deleteAllPermissions']);
    Route::post("/syncRoles", [PermissionController::class, 'syncRoles']);
    Route::post("/removeRole", [PermissionController::class, 'removeRole']);
});

Route::group([
    'prefix' => 'admin/role',
], function () {
    Route::get("/all", [RoleController::class, 'index']);
    Route::get("/show", [RoleController::class, 'show']);
    Route::post("/create", [RoleController::class, 'create']);
    Route::post("/destroy", [RoleController::class, 'destroy']);
    Route::get("/destroyAll", [RoleController::class, 'deleteAllPermissions']);
    Route::post("/syncPermissions", [RoleController::class, 'syncPermissions']);
    Route::post("/change", [RoleController::class, 'changeRole']);
});
