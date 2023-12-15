<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SliderController;
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

Route::get('/', function () {
    return view('welcome');
});

// Route::group(['middleware' => ['auth']], function(){
//   Route::get('/slider/list',[SliderController::class,'index']);
// });

// Route::get('/slider/list',[SliderController::class,'index']);
// Route::get('/slider/list',[SliderController::class,'index']);
Route::get('/verify', [RegisterController::class, 'verifyUser'])->name('verify.user');

//Password Reset
Route::get('/passwordforgot', [ResetPasswordController::class, 'passwordforgot']);
Route::post('/forgot/password', [ResetPasswordController::class, 'forgetpassword']);
Route::get('/reset-password', [ResetPasswordController::class, 'resetpassword']);
Route::get('/return', [ResetPasswordController::class, 'home'])->name("home");
Route::get('/error', [ResetPasswordController::class, 'error'])->name("error");
Route::post('/updateresetpassword', [ResetPasswordController::class, 'updatepassword']);

// Route::get('/password-reset', function () {
//     return view('password-reset');
// });

Route::get('test', function () {
    dd('Git deploy success test.');
});
