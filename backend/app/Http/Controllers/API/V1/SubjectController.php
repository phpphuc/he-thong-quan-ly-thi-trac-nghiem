<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Exam;

class SubjectController extends Controller
{
    //Tạo một môn học mới
    public function store(Request $request)
    {
        $request->validate([
            'subject_id'   => 'required|string|max:255',
            'name'  => 'required|string|max:255'
        ]);

        $subject = Subject::create([
            'subject_id'  => $request->subject_id,
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Môn học đã được tạo thành công!',
            'subject'  => $subject
        ]);
    }

    //Sửa thông tin môn học
    public function update(Request $request, $subject_id)
    {
        $subject = Subject::findOrFail($subject_id);

        $request->validate([
            'name'  => 'sometimes|string|max:255'
        ]);

        if ($request->has('name')) {
            $subject->name = $request->name;
        }

        $subject->save();

        return response()->json([
            'message' => 'Thông tin môn học đã được cập nhật!',
            'subject'  => $subject
        ]);
    }

    // Xóa một môn học
    public function destroy($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);
        $subject->delete();

        return response()->json([
            'message' => 'Môn học đã được xoá thành công!'
        ]);
    }

    // Liên kết môn học với các lớp học
    public function linkClass(Request $request, $subject_id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,class_id'
        ]);

        $subject = Subject::findOrFail($subject_id);
        $class = Classroom::findOrFail($request->class_id);

        $class->subject_id = $subject->subject_id;
        $class->save();

        return response()->json([
            'message' => 'Môn học đã được liên kết với lớp thành công!',
            'class'   => $class
        ]);
    }

    // Liên kết môn học với kỳ thi
    public function linkExam(Request $request, $subject_id)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,exam_id'
        ]);

        $subject = Subject::findOrFail($subject_id);
        $exam = Exam::findOrFail($request->exam_id);

        $exam->subject_id = $subject->subject_id;
        $exam->save();

        return response()->json([
            'message' => 'Môn học đã được liên kết với kỳ thi thành công!',
            'exam'   => $exam
        ]);
    }
}
