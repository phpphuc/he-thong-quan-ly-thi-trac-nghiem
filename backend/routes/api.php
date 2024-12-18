<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\QuestionController;
use App\Http\Controllers\API\V1\SubjectController;
use App\Http\Controllers\API\V1\ExamController;
use App\Http\Controllers\API\V1\ResultController;
use App\Http\Controllers\API\V1\SchoolBoardController;
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

    Route::get('/results', [ResultController::class, 'index']);
    Route::get('/results/{id}', [ResultController::class, 'show']);
    Route::post('/results', [ResultController::class, 'store']);
    Route::put('/results/{id}', [ResultController::class, 'update']);
    Route::get('/results/student/{studentId}', [ResultController::class, 'studentHistory']);
    Route::get('/results/exam/{examId}/report', [ResultController::class, 'examReport']);

    
    Route::get('/schoolboards', [SchoolBoardController::class, 'index']);
    Route::get('/schoolboards/{id}', [SchoolBoardController::class, 'show']);
    Route::post('/schoolboards', [SchoolBoardController::class, 'store']);
    Route::put('/schoolboards/{id}', [SchoolBoardController::class, 'update']);
    Route::delete('/schoolboards/{id}', [SchoolBoardController::class, 'destroy']);



   

});
