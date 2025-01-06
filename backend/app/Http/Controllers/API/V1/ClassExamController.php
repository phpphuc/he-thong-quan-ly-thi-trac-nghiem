<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\ClassExam;
use App\Models\Classroom;
use App\Models\Exam;
class ClassExamController
{
    

    /**
 * Thêm lớp học vào kỳ thi.
 */
public function addClassToExam(Request $request, $examId)
{
    // Kiểm tra dữ liệu đầu vào
    $request->validate([
        'class_id' => 'required|exists:classes,id',
    ]);

    // Lấy kỳ thi theo ID
    $exam = Exam::findOrFail($examId);

    // Lấy lớp học theo ID
    $class = Classroom::findOrFail($request->class_id);

    // Kiểm tra xem môn học của lớp học có nằm trong kỳ thi hay không
    $classSubjectId = $class->subject_id; 
    $examSubjectIds = $exam->subjects()->pluck('id')->toArray(); // Các môn học trong kỳ thi

    if (!in_array($classSubjectId, $examSubjectIds)) {
        return response()->json([
            'error' => 'Lớp học không thuộc môn học nào trong kỳ thi và không thể được thêm vào.',
        ], 422);
    }
    // Kiểm tra xem lớp học đã được thêm vào kỳ thi hay chưa
    $exists = $exam->classrooms()->where('class_id', $class->id)->exists();
    if ($exists) {
        return response()->json([
            'error' => 'Lớp học đã được liên kết với kỳ thi này.',
        ], 422);
    }

    // Thêm lớp học vào kỳ thi (quan hệ nhiều-nhiều)
    $exam->classrooms()->attach($class);

    return response()->json([
        'message' => 'Lớp học đã được thêm vào kỳ thi.',
        'exam' => $exam,
        'class' => $class,
    ]);
}

    /**
 * Xóa lớp học khỏi kỳ thi.
 */
public function removeClassFromExam(Request $request, $examId)
{
    // Kiểm tra dữ liệu đầu vào
    $request->validate([
        'class_id' => 'required|exists:classes,id',
    ]);

    // Lấy kỳ thi theo ID
    $exam = Exam::findOrFail($examId);

    // Lấy lớp học theo ID
    $class = Classroom::findOrFail($request->class_id);

    // Kiểm tra xem lớp học có được liên kết với kỳ thi hay không
    $exists = $exam->classes()->where('class_id', $class->id)->exists();
    if (!$exists) {
        return response()->json([
            'error' => 'Lớp học không được liên kết với kỳ thi này.',
        ], 422);
    }

    // Xóa lớp học khỏi kỳ thi
    $exam->classrooms()->detach($class->id);

    return response()->json([
        'message' => 'Lớp học đã được xóa khỏi kỳ thi thành công.',
        'class' => $class,
        'exam' => $exam,
    ]);
}

    /**
     * Hiển thị thông tin về các kỳ thi của lớp học.
     */
    public function showExamsForClass($classId)
    {
        // Lấy lớp học theo ID
    $class = Classroom::findOrFail($classId);

    // Lấy danh sách kỳ thi liên kết với lớp học
    $exams = $class->exams()->with(['subject', 'teachers'])->get();

    if ($exams->isEmpty()) {
        return response()->json([
            'message' => 'Lớp học không có kỳ thi nào được liên kết.',
            'data' => [],
        ], 404);
    }

    return response()->json([
        'message' => 'Danh sách các kỳ thi cho lớp học.',
        'data' => $exams,
    ]);
    }
}
