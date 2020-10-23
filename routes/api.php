<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\RankController;
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
Route::put('/admin/verify/{userId}', [AdminController::class, 'verify'])->name('admin.verify');

Route::get('/bank', [ReferenceController::class, 'getBanks']);
Route::get('/province', [ReferenceController::class, 'getProvinces']);
Route::get('/city', [ReferenceController::class, 'getCities']);
Route::get('/city/{provinceId}', [ReferenceController::class, 'getCitiesByProvince']);

Route::get('/point', [PointController::class, 'getPoints'])->name('admin.get-points');
Route::post('/point/claim', [PointController::class, 'claimPoints'])->name('admin.claim-points');
Route::put('/point/verify/{id}', [PointController::class, 'verifyPoint'])->name('admin.verify-point');
Route::delete('/point/{id}', [PointController::class, 'deleteClaim'])->name('admin.delete-claim');
Route::get('/point/calculate', [PointController::class, 'calculatePoint'])->name('admin.calculate-point');
Route::post('/point/withdrawal', [PointController::class, 'withdrawal'])->name('admin.withdrawal');

Route::get('/rank', [RankController::class, 'getRanks'])->name('admin.get-ranks');

Route::get('/export', [AdminController::class, 'export'])->name('admin.export');
