<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\ClassExam;
use App\Models\Classroom;
use App\Models\Exam;
class ClassExamController
{
    /**
     * Danh sách tất cả các kỳ thi của lớp học.
     */
    public function getExamsForClass($classId)
    {
        // Lấy lớp học theo ID
        $class = Classroom::findOrFail($classId);

        // Lấy tất cả các kỳ thi liên kết với lớp học
        $exams = $class->exams()->with('subjects')->get();

        return response()->json([
            'message' => 'Danh sách các kỳ thi của lớp học.',
            'data' => $exams,
        ]);
    }

    /**
     * Thêm kỳ thi vào lớp học.
     */
    public function addExamToClass(Request $request, $classId)
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        // Lấy lớp học theo ID
        $class = Classroom::findOrFail($classId);

        // Lấy kỳ thi theo ID
        $exam = Exam::findOrFail($request->exam_id);

        // Thêm kỳ thi vào lớp học (quan hệ nhiều-nhiều)
        $class->exams()->attach($exam);

        return response()->json([
            'message' => 'Kỳ thi đã được thêm vào lớp học.',
            'class' => $class,
            'exam' => $exam,
        ]);
    }

    /**
     * Xóa kỳ thi khỏi lớp học.
     */
    public function removeExamFromClass(Request $request, $classId)
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        // Lấy lớp học theo ID
        $class = Classroom::findOrFail($classId);

        // Lấy kỳ thi theo ID
        $exam = Exam::findOrFail($request->exam_id);

        // Xóa kỳ thi khỏi lớp học
        $class->exams()->detach($exam);

        return response()->json([
            'message' => 'Kỳ thi đã được xóa khỏi lớp học.',
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
        $exams = $class->exams()->with(['subjects', 'teachers'])->get();

        return response()->json([
            'message' => 'Danh sách các kỳ thi cho lớp học.',
            'data' => $exams,
        ]);
    }
}
