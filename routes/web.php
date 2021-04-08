<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
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
| GET : Obtenter datos/recursos.
| POST: Guardar datos/recursos y Forms.
| PUT : Actualizar datos/recursos.
| DELETE: Eliminar datos/recursos.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// ****************** TEST ROUTES *********************************************
Route::get('/test', [TestController::class, 'test']);
Route::get('/test-orm', [TestController::class, 'testORM'])->name('test_ORM');
Route::get('/test-json', [TestController::class, 'testJson']);

Route::get('/usuario/pruebas', [UserController::class ,'test']);
Route::get('/post/pruebas', [PostController::class ,'test']);
Route::get('/categoria/pruebas', [CategoryController::class ,'test']);



// ****************** API ROUTES ***********************************************
// Auth.
Route::post('/api/register', [RegisterController::class, 'register']);
Route::post('/api/login', [LoginController::class, 'login']);

// User.
Route::put('/api/user/update', [userController::class, 'update']);

// Posts.


// Categories.
