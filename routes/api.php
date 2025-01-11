<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Student\Course\MyCourseController;
use App\Http\Controllers\Student\Invoice\MyInvoiceController;
use App\Http\Controllers\Teacher\Course\CourseController;
use App\Http\Controllers\Teacher\Invoice\InvoiceController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test', TestController::class . '@test');

/**
 * 认证
 */
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::middleware('auth:api')->get('logout', [AuthController::class, 'logout'])->name('logout');
});

/**
 * 教师端
 */
Route::group([
    'middleware' => 'auth:api',
], function () {
    // 课程
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index'); // 获取课程列表
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store'); // 创建课程
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show'); // 获取单个课程信息
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('courses.update'); // 更新课程信息
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy'); // 删除课程

    // 账单
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index'); // 获取账单列表
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store'); // 创建账单
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show'); // 获取单个账单信息
    Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update'); // 更新账单信息
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy'); // 删除账单
    Route::post('/invoices/{id}/send', [InvoiceController::class, 'send'])->name('invoices.send'); // 发送账单 (通知学生)

});

/**
 * 学生端
 */
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'my',
], function () {
    // 课程
    Route::get('/courses', [MyCourseController::class, 'index'])->name('my.courses.index'); // 获取课程列表
    Route::get('/courses/{id}', [MyCourseController::class, 'show'])->name('my.courses.show'); // 获取单个课程信息

    // 账单
    Route::get('/invoices', [MyInvoiceController::class, 'index'])->name('my.invoices.index'); // 获取账单列表
    Route::get('/invoices/{id}', [MyInvoiceController::class, 'show'])->name('my.invoices.show'); // 获取单个账单信息
});
