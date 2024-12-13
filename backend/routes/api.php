<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\QuestionController;
use App\Http\Controllers\API\V1\SubjectController;
use App\Http\Controllers\API\V1\ExamController;

Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/users', [App\Http\Controllers\API\V1\AuthController::class, 'index']);

    Route::post('/register', [App\Http\Controllers\API\V1\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\API\V1\AuthController::class, 'login']);


    Route::get('questions', [QuestionController::class, 'index']);
    Route::post('questions', [QuestionController::class, 'store']);
    Route::put('questions/{id}', [QuestionController::class, 'update']);

    Route::post('subjects', [SubjectController::class, 'store']);

    Route::get('teachers', [App\Http\Controllers\API\V1\TeacherController::class, 'index']);
    Route::post('teachers', [App\Http\Controllers\API\V1\TeacherController::class, 'store']);


    Route::get('/exams', [ExamController::class, 'index']);
    Route::get('/exams/{id}', [ExamController::class, 'showExam']);
    Route::post('/exams', [ExamController::class, 'createExam']);

    Route::post('/exams/{id}/submit', [ExamController::class, 'submitExam']);
});
