<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReferenceController;
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

Route::get('/admin/user', [AdminController::class, 'getUserList'])->name('admin.get-user-list');
Route::post('/admin/agent/{userId}/verify', [AdminController::class, 'verifyAgent'])->name('admin.verify-agent');

Route::get('/bank', [ReferenceController::class, 'getBanks']);
Route::get('/province', [ReferenceController::class, 'getProvinces']);
Route::get('/city', [ReferenceController::class, 'getCities']);
Route::get('/city/{provinceId}', [ReferenceController::class, 'getCitiesByProvince']);
