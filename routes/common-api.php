<?php

Route::namespace('Auth')->group(function () {
    // 登录
    Route::post('login', [\App\Http\Controllers\CommonApi\Auth\AuthController::class, 'login']);
});


Route::middleware(['rbac'])->group(function () {
    // 我的信息
    Route::get('me', [\App\Http\Controllers\Common\User\MeController::class, 'index']);
    Route::patch('me', [\App\Http\Controllers\Common\User\MeController::class, 'update']);
    // 用户路由
    Route::get('nodes', [\App\Http\Controllers\Common\User\MeController::class, 'nodes']);

    // 退出登录
    Route::delete('login', [\App\Http\Controllers\CommonApi\Auth\AuthController::class, 'logout']);
});

Route::middleware(['auth:teacher', 'rbac'])->group(function () {
    Route::get('students', [\App\Http\Controllers\CommonApi\Basic\SimpleController::class, 'student']);
    Route::get('courses', [\App\Http\Controllers\CommonApi\Basic\SimpleController::class, 'courses']);
});


Route::apiFallback();
