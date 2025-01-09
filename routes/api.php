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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', TestController::class . '@test');

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// 教师端接口
// `GET /api/courses`：获取课程列表
// `POST /api/courses`：创建课程
// `GET /api/courses/{id}`：获取单个课程信息
// `PUT /api/courses/{id}`：更新课程信息
// `DELETE /api/courses/{id}`：删除课程
// `GET /api/invoices`：获取账单列表
// `POST /api/invoices`：创建账单
// `GET /api/invoices/{id}`：获取单个账单信息
// `PUT /api/invoices/{id}`：更新账单信息
// `DELETE /api/invoices/{id}`：删除账单
// `POST /api/invoices/{id}/send`：发送账单 (通知学生)
Route::group([
    // 'middleware' => 'auth:api',
], function () {
    // 课程
    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // 账单
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices', [InvoiceController::class, 'store']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
    Route::put('/invoices/{id}', [InvoiceController::class, 'update']);
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy']);
    Route::post('/invoices/{id}/send', [InvoiceController::class, 'send']);

});

// 学生端接口
// `GET /api/courses`：获取课程列表
// `GET /api/courses/{id}`：获取单个课程信息
// `GET /api/invoices`：获取账单列表
// `GET /api/invoices/{id}`：获取单个账单信息
Route::group([
    // 'middleware' => 'auth:api',
    'prefix' => 'my',
], function () {
    // 课程
    Route::get('/courses', [MyCourseController::class, 'index']);
    Route::get('/courses/{id}', [MyCourseController::class, 'show']);

    // 账单
    Route::get('/invoices', [MyInvoiceController::class, 'index']);
    Route::get('/invoices/{id}', [MyInvoiceController::class, 'show']);
});
