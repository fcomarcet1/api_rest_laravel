<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuth;
use App\Http\Middleware\RoleAdminAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Http Methods
|--------------------------------------------------------------------------
|
| GET : Obtener datos/recursos.
| POST: Guardar datos/recursos y Forms.
| PUT : Actualizar datos/recursos.
| DELETE: Eliminar datos/recursos.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// ****************** TEST ROUTES *********************************************
// Route::get('/test', [TestController::class, 'test']);
// Route::get('/test-orm', [TestController::class, 'testORM'])->name('test_ORM');
// Route::get('/test-json', [TestController::class, 'testJson']);
Route::get('/post-list', [TestController::class, 'index']);

// Route::get('/usuario/pruebas', [UserController::class ,'test']);
// Route::get('/post/pruebas', [PostController::class ,'test']);
// Route::get('/categoria/pruebas', [CategoryController::class ,'test']);




// ****************** API ROUTES ***********************************************
// Auth.
Route::post('/api/register', [RegisterController::class, 'register']);
Route::post('/api/login', [LoginController::class, 'login']);

// User.
Route::put('/api/user/update', [UserController::class, 'update'])
    ->middleware(ApiAuth::class);;
Route::post('api/user/upload', [UserController::class, 'upload'])
    ->middleware(ApiAuth::class);
Route::get('/api/user/avatar/{filename}', [UserController::class, 'getImage']); // avatar
Route::get('/api/user/profile/{id}', [UserController::class, 'profile']);

// Categories && Posts.
Route::resources([
    '/api/category' => CategoryController::class,
    '/api/post' => PostController::class,
]);

Route::post('api/post/upload/{post}', [PostController::class, 'upload'])
    ->middleware(ApiAuth::class, RoleAdminAuth::class);

Route::get('api/post/image/{filename}', [PostController::class, 'getImage']);





