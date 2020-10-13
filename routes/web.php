<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Redirect;
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
    return redirect('/login');
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/user/password', [UserController::class, 'showPassword'])->name('password.show');

    Route::get('/user/detail', [UserController::class, 'showDetail'])->name('profile.show-detail');
    Route::put('/user/detail', [UserController::class, 'updateUserDetail'])->name('user-detail.update');

    Route::get('/admin/agent', [AdminController::class, 'showAgents'])->name('admin.show-agents');
});
