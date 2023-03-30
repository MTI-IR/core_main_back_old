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

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    });

Route::post('/login', [LoginController::class, 'login']);
Route::get('/userInfo', [LoginController::class, 'userInfo']);
Route::get('/', function () {
    return response()->json([
        "message" => "server is up",
        "status" => "200"
    ], 200);
});



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
    'middleware' => [
        'adminLoginStatus'
    ]

], function () {
    Route::get("/all", [AdminPanelUserController::class, 'index'])
        ->middleware(['permission:user.index']);
    Route::post("/destroy/users", [AdminPanelUserController::class, 'destroyUsers'])
        ->middleware(['permission:user.destroy.users']);
    Route::get("/destroy", [AdminPanelUserController::class, 'destroy'])
        ->middleware(['permission:user.destroy.user']);
    Route::post("/validate/users", [AdminPanelUserController::class, 'validateUsers'])
        ->middleware(['permission:user.validate']);
    Route::post("/edit", [AdminPanelUserController::class, 'edit'])
        ->middleware(['permission:user.edit']);
    Route::post("/assignRole", [AdminPanelUserController::class, 'assignRole'])
        ->middleware(['permission:user.assign-role']);
    Route::post("/addPermission", [AdminPanelUserController::class, 'addPermission'])
        ->middleware(['permission:user.permission.add']);
    Route::post("/removePermission", [AdminPanelUserController::class, 'removePermission'])
        ->middleware(['permission:user.permission.remove']);
    Route::post("/changePermissions", [AdminPanelUserController::class, 'changePermissions'])
        ->middleware(['permission:user.permission.change']);
    Route::post("/create", [AdminPanelUserController::class, 'create'])
        ->middleware(['permission:user.create']);
    Route::get("/{id}", [AdminPanelUserController::class, 'show'])
        ->middleware(['permission:user.show']);
});

Route::group([
    'prefix' => 'admin/company',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [CompanyController::class, 'index'])
        ->middleware(['permission:company.index']);
    Route::get("/projects", [CompanyController::class, 'projects'])
        ->middleware(['permission:company.projects']);
    Route::post("/edit", [CompanyController::class, 'edit'])
        ->middleware(['permission:company.edit']);
    Route::post("/create", [CompanyController::class, 'create'])
        ->middleware(['permission:company.cerate']);
    Route::post("/destroy", [CompanyController::class, 'destroy'])
        ->middleware(['permission:company.destroy']);
    Route::post("/validate", [CompanyController::class, 'validateCompanies'])
        ->middleware(['permission:company.validate']);
    Route::get("/document/{id}", [CompanyController::class, 'document'])
        ->middleware(['permission:company.document']);
    Route::get("/{id}", [CompanyController::class, 'show'])
        ->middleware(['permission:company.show']);
});


Route::group([
    'prefix' => 'admin/project',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [AdminPanelProjectController::class, 'index'])
        ->middleware(['permission:project.index']);
    Route::post("/edit", [AdminPanelProjectController::class, 'edit'])
        ->middleware(['permission:project.edit']);
    Route::post("/destroy", [AdminPanelProjectController::class, 'destroy'])
        ->middleware(['permission:project.destroy']);
    Route::post("/create", [AdminPanelProjectController::class, 'create'])
        ->middleware(['permission:project.create']);
    Route::post("/validate", [AdminPanelProjectController::class, 'validateProjects'])
        ->middleware(['permission:project.validate']);
    Route::get("/document/{id}", [AdminPanelProjectController::class, 'document'])
        ->middleware(['permission:project.document']);
    Route::get("/{id}", [AdminPanelProjectController::class, 'show'])
        ->middleware(['permission:project.show']);
});


Route::group([
    'prefix' => 'admin/state',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [StateController::class, 'index'])
        ->middleware(['permission:state.index']);
    Route::post("/create", [StateController::class, 'create'])
        ->middleware(['permission:state.create']);
    Route::post("/destroy", [StateController::class, 'destroy'])
        ->middleware(['permission:state.destroy']);
    Route::get("/{id}", [StateController::class, 'show'])
        ->middleware(['permission:state.show']);
});


Route::group([
    'prefix' => 'admin/city',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [CityController::class, 'index'])
        ->middleware(['permission:city.index']);
    Route::post("/create", [CityController::class, 'create'])
        ->middleware(['permission:city.create']);
    Route::post("/destroy", [CityController::class, 'destroy'])
        ->middleware(['permission:city.destroy']);
    Route::get("/{id}", [CityController::class, 'show'])
        ->middleware(['permission:city.show']);
});


Route::group([
    'prefix' => 'admin/category',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [CategoryController::class, 'index'])
        ->middleware(['permission:category.index']);
    Route::post("/create", [CategoryController::class, 'create'])
        ->middleware(['permission:category.create']);
    Route::post("/destroy", [CategoryController::class, 'destroy'])
        ->middleware(['permission:category.destroy']);
    Route::get("/{id}", [CategoryController::class, 'show'])
        ->middleware(['permission:category.show']);
});


Route::group([
    'prefix' => 'admin/subcategory',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [SubCategoryController::class, 'index'])
        ->middleware(['permission:sun-category.index']);
    Route::post("/create", [SubCategoryController::class, 'create'])
        ->middleware(['permission:sun-category.create']);
    Route::post("/destroy", [SubCategoryController::class, 'destroy'])
        ->middleware(['permission:sun-category.destroy']);
    Route::get("/{id}", [SubCategoryController::class, 'show'])
        ->middleware(['permission:sun-category.show']);
});


Route::group([
    'prefix' => 'admin/ticket',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [TicketController::class, 'index'])
        ->middleware(['permission:ticket.index']);
    Route::post("/create", [TicketController::class, 'create'])
        ->middleware(['permission:ticket.create']);
    Route::post("/destroy", [TicketController::class, 'destroy'])
        ->middleware(['permission:ticket.destroy']);
    Route::get("/{id}", [TicketController::class, 'show'])
        ->middleware(['permission:ticket.show']);
});


Route::group([
    'prefix' => 'admin/permission',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [PermissionController::class, 'index'])
        ->middleware(['permission:permission.index']);
    Route::post("/create", [PermissionController::class, 'create'])
        ->middleware(['permission:permission.create']);
    Route::post("/destroy", [PermissionController::class, 'destroy'])
        ->middleware(['permission:permission.destroy']);
    Route::get("/destroyAll", [PermissionController::class, 'deleteAllPermissions'])
        ->middleware(['permission:permission.destroy.all']);
    Route::post("/syncRoles", [PermissionController::class, 'syncRoles'])
        ->middleware(['permission:permission.sync-roles']);
    Route::post("/removeRole", [PermissionController::class, 'removeRole'])
        ->middleware(['permission:permission.remove-roles']);
});

Route::group([
    'prefix' => 'admin/role',
    'middleware' => [
        'adminLoginStatus'
    ]
], function () {
    Route::get("/all", [RoleController::class, 'index'])
        ->middleware(['permission:role.index']);
    Route::get("/show", [RoleController::class, 'show'])
        ->middleware(['permission:role.show']);
    Route::post("/create", [RoleController::class, 'create'])
        ->middleware(['permission:role.create']);
    Route::post("/destroy", [RoleController::class, 'destroy'])
        ->middleware(['permission:role.destroy']);
    Route::get("/destroyAll", [RoleController::class, 'deleteAllPermissions'])
        ->middleware(['permission:role.destroy.all']);
    Route::post("/syncPermissions", [RoleController::class, 'syncPermissions'])
        ->middleware(['permission:role.sync-permissions']);
    Route::post("/change", [RoleController::class, 'changeRole'])
        ->middleware(['permission:role.change-role']);
});
