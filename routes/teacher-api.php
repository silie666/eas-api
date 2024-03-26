<?php

Route::middleware(['auth:teacher', 'rbac'])->group(function () {

    Route::patch('courses/{courseId}', [\App\Http\Controllers\TeacherApi\Course\CourseController::class, 'update']);
    Route::apiResource('courses', \App\Http\Controllers\TeacherApi\Course\CourseController::class, [
        'parameters' => ['courses' => 'courseId'],
        'only'       => ['index', 'store', 'destroy', 'show'],
    ]);

    Route::patch('course-bills/{courseBillId}/send',
        [\App\Http\Controllers\TeacherApi\Course\CourseBillController::class, 'sendBill']);
    Route::patch('course-bills/{courseBillId}',
        [\App\Http\Controllers\TeacherApi\Course\CourseBillController::class, 'update']);
    Route::apiResource('course-bills', \App\Http\Controllers\TeacherApi\Course\CourseBillController::class, [
        'parameters' => ['course-bills' => 'courseBillId'],
        'only'       => ['index', 'store', 'destroy', 'show'],
    ]);
});

Route::apiFallback();
