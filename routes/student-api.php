<?php


Route::middleware(['auth:student', 'rbac'])->group(function () {
    Route::get('courses', [\App\Http\Controllers\StudentApi\Course\CourseController::class, 'index']);

    Route::get('course-bills', [\App\Http\Controllers\StudentApi\Course\CourseController::class, 'bills']);
    Route::patch('course-bills/{courseBillId}/pay',
        [\App\Http\Controllers\StudentApi\Course\CourseController::class, 'pay']);

    Route::get('me-cards', [\App\Http\Controllers\StudentApi\User\MeController::class, 'cards']);
    Route::post('me-cards', [\App\Http\Controllers\StudentApi\User\MeController::class, 'storeCard']);
    Route::patch('me-cards/{cardId}', [\App\Http\Controllers\StudentApi\User\MeController::class, 'updateCard']);
    Route::delete('me-cards/{cardId}', [\App\Http\Controllers\StudentApi\User\MeController::class, 'destroyCard']);
});

Route::apiFallback();
