<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\QuestionController;
use App\Http\Controllers\API\V1\SubjectController;
use App\Http\Controllers\API\V1\ExamController;
use App\Http\Controllers\API\V1\ResultController;
use App\Http\Controllers\API\V1\SchoolBoardController;
use App\Http\Controllers\API\V1\ExamQuestionController;
use App\Http\Controllers\API\V1\ClassController;
use App\Http\Controllers\API\V1\SubjectController;
use App\Http\Controllers\API\V1\ClassStudentController;
use App\Http\Controllers\API\V1\TeacherController;
Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/users', [App\Http\Controllers\API\V1\AuthController::class, 'index']);

    Route::post('/register', [App\Http\Controllers\API\V1\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\API\V1\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\API\V1\AuthController::class, 'logout'])->middleware('auth:sanctum');


    Route::get('questions', [QuestionController::class, 'index']);
    Route::post('questions', [QuestionController::class, 'store']);
    Route::put('questions/{id}', [QuestionController::class, 'update']);

    //Route::post('subjects', [SubjectController::class, 'store']);

    //Route::get('teachers', [App\Http\Controllers\API\V1\TeacherController::class, 'index']);
    //Route::post('teachers', [App\Http\Controllers\API\V1\TeacherController::class, 'store']);


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
    Route::post('/schoolboards/exams', [SchoolBoardController::class, 'createExam']);
    Route::get('/schoolboards/exams', [SchoolBoardController::class, 'exams']);
    Route::get('/schoolboards/exams/{examId}/report', [SchoolBoardController::class, 'examReport']);

    
    Route::post('/exam/{exam_id}/attach-questions', [ExamQuestionController::class, 'attachQuestions']);
    Route::get('/exam/{exam_id}/questions', [ExamQuestionController::class, 'showQuestions']);
    Route::post('/exam/{exam_id}/detach-questions', [ExamQuestionController::class, 'detachQuestions']);

    Route::post('/class/store', [ClassController::class, 'store']);
    Route::put('/class/update/{class_id}', [ClassController::class, 'update']);
    Route::delete('/class/delete/{class_id}', [ClassController::class, 'destroy']);
    Route::get('/class/{class_id}/members', [ClassController::class, 'showMembers']);


    Route::post('/subject/store', [SubjectController::class, 'store']);
    Route::put('/subject/update/{subject_id}', [SubjectController::class, 'update']);
    Route::delete('/subject/delete/{subject_id}', [SubjectController::class, 'destroy']);
    Route::post('/subject/{subject_id}/link-class', [SubjectController::class, 'linkClass']);
    Route::post('/subject/{subject_id}/link-exam', [SubjectController::class, 'linkExam']);

    Route::get('/class-students', [ClassStudentController::class, 'index']); 
    Route::post('/class-students', [ClassStudentController::class, 'store']); 
    Route::get('/class-students/{classId}', [ClassStudentController::class, 'search']); 
    Route::delete('/class-students/{classId}/{studentId}', [ClassStudentController::class, 'destroy']); 

    
    Route::get('/teachers', [TeacherController::class, 'index']); 
    Route::post('/teachers', [TeacherController::class, 'store']);  
    Route::put('/teachers/{id}', [TeacherController::class, 'update']); 
    Route::delete('/teachers/{id}', [TeacherController::class, 'destroy']); 
    Route::post('/teachers/assign-to-class', [TeacherController::class, 'assignToClass']); 
    Route::post('/teachers/assign-to-subject', [TeacherController::class, 'assignToSubject']);
    Route::post('/teachers/organize-exam', [TeacherController::class, 'organizeExam']);



   

});
