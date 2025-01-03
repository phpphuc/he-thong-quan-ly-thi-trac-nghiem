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
use App\Http\Controllers\API\V1\ClassStudentController;
use App\Http\Controllers\API\V1\TeacherController;
use App\Http\Controllers\API\V1\StudentController;
use App\Http\Controllers\API\V1\ClassExamController;
use App\Http\Controllers\API\V1\UserController;

Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/users', [App\Http\Controllers\API\V1\AuthController::class, 'index']);
    Route::post('/register', [App\Http\Controllers\API\V1\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\API\V1\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\API\V1\AuthController::class, 'logout'])->middleware('auth:sanctum');
    
    Route::get('/users', [UserController::class, 'index']);// Lấy danh sách tất cả người dùng
    Route::get('/users/{id}', [UserController::class, 'show']);// Lấy thông tin chi tiết của người dùng
    Route::put('/users/{id}', [UserController::class, 'update']);// Cập nhật thông tin người dùng
    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword']);// Thay đổi mật khẩu người dùng
    Route::delete('/users/{id}', [UserController::class, 'destroy']);// Xóa người dùng
    
    Route::get('/questions', [QuestionController::class, 'index']);// Lấy danh sách câu hỏi
    Route::post('/questions', [QuestionController::class, 'store']);// Thêm mới câu hỏi
    Route::put('/questions/{id}', [QuestionController::class, 'update']);// Cập nhật câu hỏi
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);// Xóa câu hỏi
    
    Route::get('/exams', [ExamController::class, 'index']);// Lấy tất cả các kỳ thi
    Route::post('/exams', [ExamController::class, 'createExam']);// Tạo một kỳ thi mới
    Route::get('/students/{id}/exams', [ExamController::class, 'getExamsForStudent']);// Lấy danh sách bài thi dành cho sinh viên
    Route::get('/exams/{id}', [ExamController::class, 'showExam']);// Lấy thông tin chi tiết kỳ thi
    Route::post('/exams/{id}/submit', [ExamController::class, 'submitExam']);// Nộp bài thi và tính điểm
    Route::get('/exams/{examId}/subjects-teachers', [ExamController::class, 'getExamSubjectsAndTeachers']);// Lấy các môn học và giáo viên dạy môn trong kỳ thi

    Route::get('/results', [ResultController::class, 'index']);// Hiển thị danh sách kết quả thi
    Route::get('/results/{id}', [ResultController::class, 'show']);// Hiển thị chi tiết kết quả của một bài thi
    Route::post('/results', [ResultController::class, 'store']);// Lưu kết quả bài thi
    Route::put('/results/{id}', [ResultController::class, 'update']);// Cập nhật kết quả bài thi
    Route::get('/students/{studentId}/results', [ResultController::class, 'studentHistory']);// Xem lịch sử kết quả của một sinh viên
    Route::get('/exams/{examId}/report', [ResultController::class, 'examReport']);// Tạo báo cáo kết quả kỳ thi

    Route::get('/school-boards', [SchoolBoardController::class, 'index']);// Hiển thị danh sách tất cả các thành viên Ban giám hiệu
    Route::get('/school-boards/{id}', [SchoolBoardController::class, 'show']);// Xem thông tin chi tiết một thành viên Ban giám hiệu
    Route::put('/school-boards/{id}', [SchoolBoardController::class, 'update']);// Cập nhật thông tin của một thành viên Ban giám hiệu
    Route::delete('/school-boards/{id}', [SchoolBoardController::class, 'destroy']);// Xóa một thành viên Ban giám hiệu
    Route::get('/school-boards/{schoolBoardId}/exams', [SchoolBoardController::class, 'exams']);// Lấy danh sách các kỳ thi mà thành viên Ban giám hiệu giám sát
    Route::get('/school-boards/{schoolBoardId}/report', [SchoolBoardController::class, 'report']);// Tạo báo cáo kết quả kỳ thi của Ban giám hiệu

    Route::post('/exams/{exam_id}/attach-questions', [ExamQuestionController::class, 'attachQuestions']);// Liên kết câu hỏi với kỳ thi
    Route::get('/exams/{exam_id}/show-questions', [ExamQuestionController::class, 'showQuestions']);// Xem các câu hỏi đã được liên kết với kỳ thi
    Route::post('/exams/{exam_id}/detach-questions', [ExamQuestionController::class, 'detachQuestions']);// Xóa liên kết câu hỏi khỏi kỳ thi

    Route::post('/classes', [ClassController::class, 'store']); // Tạo một lớp học mới
    Route::put('/classes/{class_id}', [ClassController::class, 'update']); // Sửa thông tin lớp học
    Route::delete('/classes/{class_id}', [ClassController::class, 'destroy']); // Xóa một lớp học
    Route::get('/classes/{class_id}/members', [ClassController::class, 'showMembers']); // Xem thành viên của lớp
    Route::post('/classes/{class_id}/add-teachers', [ClassController::class, 'addTeachersToClass']); // Thêm giáo viên vào lớp

    Route::post('/subjects', [SubjectController::class, 'store']);// Tạo một môn học mới và gán nhiều giáo viên
    Route::put('/subjects/{id}', [SubjectController::class, 'update']);// Cập nhật thông tin môn học
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);// Xóa một môn học
    Route::get('/subjects/{id}/teachers', [SubjectController::class, 'getTeachers']);// Lấy danh sách các giáo viên giảng dạy một môn học
    Route::post('/subjects/{id}/link-classes', [SubjectController::class, 'linkClass']);// Liên kết môn học với các lớp học
    Route::post('/subjects/{id}/link-exams', [SubjectController::class, 'linkExam']);// Liên kết môn học với kỳ thi

    Route::get('/classes/{classId}/students', [ClassStudentController::class, 'index']);// Lấy danh sách sinh viên trong lớp học
    Route::post('/classes/students', [ClassStudentController::class, 'store']);// Thêm sinh viên vào lớp học
    Route::delete('/classes/{classId}/students', [ClassStudentController::class, 'destroy']);// Xóa sinh viên khỏi lớp học
    Route::get('/classes/{classId}/students/search', [ClassStudentController::class, 'search']);// Tìm kiếm sinh viên trong lớp học
    
    Route::get('/teachers', [TeacherController::class, 'index']);// Lấy danh sách giáo viên
    Route::put('/teachers/{teacherId}', [TeacherController::class, 'update']);// Cập nhật thông tin giáo viên
    Route::delete('/teachers/{teacherId}', [TeacherController::class, 'destroy']);// Xóa giáo viên
    Route::get('/teachers/{teacherId}/classes', [TeacherController::class, 'getClasses']);// Lấy danh sách lớp học của giáo viên
    Route::get('/teachers/{teacherId}/subjects', [TeacherController::class, 'getSubjects']);// Lấy danh sách môn học của giáo viên
    Route::post('/teachers/organize-exam', [TeacherController::class, 'organizeExam']);// Tổ chức kỳ thi (chuyển tiếp request đến ExamController)
    Route::post('/teachers/{teacherId}/add-classes', [TeacherController::class, 'addClassesToTeacher']);// Thêm lớp học vào giáo viên

    Route::get('/students', [StudentController::class, 'index']);// Hiển thị danh sách tất cả sinh viên
    Route::get('/students/{id}', [StudentController::class, 'show']);// Hiển thị thông tin sinh viên cụ thể
    Route::put('/students/{id}', [StudentController::class, 'update']);// Cập nhật thông tin sinh viên
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);// Xóa sinh viên
    Route::get('/students/{studentId}/exam-history', [StudentController::class, 'examHistory']);// Xem lịch sử kết quả thi của sinh viên
    Route::get('/students/{studentId}/classes', [StudentController::class, 'getClasses']);// Lấy danh sách các lớp học mà sinh viên thuộc về

    Route::get('/classes/{classId}/exams', [ClassExamController::class, 'getExamsForClass']);// Lấy danh sách tất cả các kỳ thi của lớp học
    Route::post('/classes/{classId}/exams', [ClassExamController::class, 'addExamToClass']);// Thêm kỳ thi vào lớp học
    Route::delete('/classes/{classId}/exams', [ClassExamController::class, 'removeExamFromClass']);// Xóa kỳ thi khỏi lớp học
    Route::get('/classes/{classId}/exams/details', [ClassExamController::class, 'showExamsForClass']);// Hiển thị thông tin về các kỳ thi của lớp học




});
