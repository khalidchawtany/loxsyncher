<?php

use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\ControlCenterController;
use App\Http\Controllers\CustomsProductController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\PermissionsDescriptionController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\UserManager\ActivityController;
use App\Http\Controllers\UserManager\PermissionController;
use App\Http\Controllers\UserManager\ReviewRoleController;
use App\Http\Controllers\UserManager\RoleController;
use App\Http\Controllers\UserManager\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Authentication Routes...

Route::get('/chgpwd', [HomeController::class, 'showChgpwdForm']);
Route::post('/chgpwd', [HomeController::class, 'chgpwd'])->name('chgpwd');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// User
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/list', [UserController::class, 'list']);
Route::get('/users/json_list', [UserController::class, 'jsonList']);
Route::get('/users/roles/list', [UserController::class, 'listRoles']);
Route::get('/users/departments/list', [UserController::class, 'listDepartments']);
Route::post('/users/create', [UserController::class, 'create']);
Route::post('/users/update', [UserController::class, 'update']);
Route::post('/users/destroy', [UserController::class, 'destroy']);
Route::post('/users/toggle-status', [UserController::class, 'toggleStatus']);
Route::post('/users/reset-password', [UserController::class, 'resetPassword']);
Route::post('/users/impersonate', [UserController::class, 'impersonateUser']);
Route::get('/users/leave-impersonation', [UserController::class, 'leaveImpersonation']);

// Permissions
Route::get('permissions', [PermissionController::class, 'index']);
Route::get('users/permissions/list-users', [PermissionController::class, 'listUsers']);
Route::get('users/permissions/list-user-permissions', [PermissionController::class, 'listUserPermissions']);
Route::post('users/permissions/{user_id}/save', [PermissionController::class, 'savePermissions']);

Route::get('permissions/review', [PermissionController::class, 'reviewPermissions']);
Route::get('permissions/list-all', [PermissionController::class, 'listAllPermissions']);
Route::get('permissions/download-permissions', [PermissionController::class, 'downloadPermissions'])->name('download_permissions');

// Roles
Route::get('/roles', [RoleController::class, 'index']);
Route::get('roles/permissions', [RoleController::class, 'permissions']);
Route::post('roles/permissions/{role_id}/save', [RoleController::class, 'savePermissions']);
Route::get('/roles/list', [RoleController::class, 'list']);
Route::post('/roles/insert', [RoleController::class, 'insert']);
Route::post('/roles/update', [RoleController::class, 'update']);
Route::post('/roles/destroy', [RoleController::class, 'destroy']);
Route::get('/roles/permissions/show', [RoleController::class, 'showPermissions']);
Route::get('/roles/permissions/list', [RoleController::class, 'listRolePermissions']);
Route::post('/roles/permissions/set', [RoleController::class, 'setPermission']);

//Review
Route::get('roles/review', [ReviewRoleController::class, 'index']);
Route::get('roles/review/list', [ReviewRoleController::class, 'list']);
Route::get('roles/review/download', [ReviewRoleController::class, 'downloadRoles'])->name('download_roles');

Route::match(['get', 'post'], 'control-center', [ControlCenterController::class, 'index']);

// Activities
Route::get('/activities', [ActivityController::class, 'index']);
Route::get('/activities/list', [ActivityController::class, 'list']);
Route::get('/activities/list/model-activity-name', [ActivityController::class, 'listModelActivityNames']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/list', [CategoryController::class, 'list']);
Route::get('/categories/json_list', [CategoryController::class, 'jsonList']);
Route::post('/categories/create', [CategoryController::class, 'create']);
Route::post('/categories/update', [CategoryController::class, 'update']);
Route::post('/categories/destroy', [CategoryController::class, 'destroy']);

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/list', [ProductController::class, 'list']);
Route::get('/products/departments/list', [ProductController::class, 'listDepartments']);
Route::get('/products/categories/list', [ProductController::class, 'listCategories']);
Route::get('/products/json_list', [ProductController::class, 'jsonList']);
Route::post('/products/create', [ProductController::class, 'create']);
Route::post('/products/update', [ProductController::class, 'update']);
Route::post('/products/destroy', [ProductController::class, 'destroy']);

// Departments
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/list', [DepartmentController::class, 'list']);
Route::post('/departments/create', [DepartmentController::class, 'create']);
Route::post('/departments/update', [DepartmentController::class, 'update']);
Route::post('/departments/destroy', [DepartmentController::class, 'destroy']);

// app_setting routes
Route::get('/app_settings', [AppSettingController::class, 'index']);
Route::get('/app_settings/list', [AppSettingController::class, 'list']);
Route::post('/app_settings/create', [AppSettingController::class, 'create']);
Route::post('/app_settings/update', [AppSettingController::class, 'update']);
Route::post('/app_settings/destroy', [AppSettingController::class, 'destroy']);

// print
Route::get('/print', PrintController::class)->name('print');

// specification routes
Route::get('/specifications', [SpecificationController::class, 'index']);
Route::get('/specifications/list', [SpecificationController::class, 'list']);
Route::post('/specifications/create', [SpecificationController::class, 'create']);
Route::post('/specifications/update', [SpecificationController::class, 'update']);
Route::post('/specifications/destroy', [SpecificationController::class, 'destroy']);
Route::get('/specifications/category/list', [SpecificationController::class, 'listCategories']);
Route::get('/specifications/show_attach_document_dialog/{specification_id}', [SpecificationController::class, 'showAttachDocumentDialog']);
Route::post('/specifications/attach_document', [SpecificationController::class, 'attachDocument']);
Route::get('/specifications/show_document_dialog/{specification_id}', [SpecificationController::class, 'showDocumentDialog']);

// permissions_description routes
Route::get('permissions/descriptions', [PermissionsDescriptionController::class, 'index']);
Route::get('permissions/descriptions/list', [PermissionsDescriptionController::class, 'list']);
Route::post('permissions/descriptions/create', [PermissionsDescriptionController::class, 'create']);
Route::post('permissions/descriptions/update', [PermissionsDescriptionController::class, 'update']);
Route::post('permissions/descriptions/destroy', [PermissionsDescriptionController::class, 'destroy']);

// permission_request routes
Route::get('permission_requests', [PermissionRequestController::class, 'index']);
Route::get('permission_requests/list', [PermissionRequestController::class, 'list']);
Route::post('permission_requests/create', [PermissionRequestController::class, 'create']);
Route::post('permission_requests/update', [PermissionRequestController::class, 'update']);
Route::post('permission_requests/destroy', [PermissionRequestController::class, 'destroy']);
Route::get('permission_requests/dialog', [PermissionRequestController::class, 'showPermissionRequestDialog']);
Route::get('permission_requests/view_dialog', [PermissionRequestController::class, 'showPermissionRequestViewDialog']);
Route::post('permission_requests/toggle-status', [PermissionRequestController::class, 'togglePermissionStatus']);

// change_request routes
Route::get('change_requests', [ChangeRequestController::class, 'index']);
Route::get('change_requests/list', [ChangeRequestController::class, 'list']);
Route::post('change_requests/create', [ChangeRequestController::class, 'create']);
Route::post('change_requests/update', [ChangeRequestController::class, 'update']);
Route::post('change_requests/destroy', [ChangeRequestController::class, 'destroy']);
Route::get('change_requests/dialog', [ChangeRequestController::class, 'showChangeRequestDialog']);
Route::get('change_requests/view_dialog', [ChangeRequestController::class, 'showChangeRequestViewDialog']);
Route::post('change_requests/toggle-status', [ChangeRequestController::class, 'toggleChangeRequestStatus']);

// CustomsProducts
Route::get('/customs_products', [CustomsProductController::class, 'index']);
Route::get('/customs_products/list', [CustomsProductController::class, 'list']);
Route::get('/customs_products/json_list', [CustomsProductController::class, 'jsonList']);
Route::post('/customs_products/create', [CustomsProductController::class, 'create']);
Route::post('/customs_products/update', [CustomsProductController::class, 'update']);
Route::post('/customs_products/destroy', [CustomsProductController::class, 'destroy']);
route::get('/customs_products/show_import_from_excel_file_dialog', [CustomsProductController::class, 'showImportFromExcelFileDialog'])->name('showImportCustomsProductFromExcelFileDialog');
Route::post('/customs_products/import_customs_product_from_file', [CustomsProductController::class, 'importFromExcelFile'])->name('importCustomsProductFromFile');

// Brands
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/list', [BrandController::class, 'list']);
Route::get('/brands/json_list', [BrandController::class, 'jsonList']);
Route::get('/brands/dialog', [BrandController::class, 'showBrandDialog']);
Route::post('/brands/create', [BrandController::class, 'create']);
Route::post('/brands/update', [BrandController::class, 'update']);
Route::post('/brands/destroy', [BrandController::class, 'destroy']);
