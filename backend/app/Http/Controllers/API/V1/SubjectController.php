<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Result;
use App\Models\Classroom;
use App\Models\Exam;

class SubjectController extends Controller
{
    // Tạo một môn học mới và gán nhiều giáo viên
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'teacher_ids' => 'required|array',  // Nhận mảng các teacher_ids
            'teacher_ids.*' => 'exists:teachers,id',  // Mỗi teacher_id phải tồn tại trong bảng teachers
        ]);

        // Tạo môn học mới
        $subject = Subject::create([
            'name' => $request->name,
        ]);

        // Gán các giáo viên cho môn học
        $subject->teachers()->sync($request->teacher_ids);  // sync để thêm hoặc cập nhật mối quan hệ

        return response()->json([
            'message' => 'Môn học đã được tạo và gán giáo viên thành công!',
            'subject' => $subject,
        ]);
    }

    // Cập nhật thông tin môn học
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'teacher_ids' => 'nullable|array',  // Mảng các teacher_ids để cập nhật giáo viên
            'teacher_ids.*' => 'exists:teachers,id',  // Mỗi teacher_id phải tồn tại trong bảng teachers
        ]);

        // Cập nhật tên môn học nếu có
        if ($request->has('name')) {
            $subject->name = $request->name;
        }

        // Cập nhật các giáo viên liên kết với môn học
        if ($request->has('teacher_ids')) {
            $subject->teachers()->sync($request->teacher_ids);  // Đồng bộ các giáo viên
        }

        $subject->save();

        return response()->json([
            'message' => 'Thông tin môn học đã được cập nhật!',
            'subject' => $subject,
        ]);
    }

    // Xoá một môn học
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
    $subject->examQuestions()->delete(); // Xóa các câu hỏi trong các kỳ thi
    Result::where('exam_subject_id', $subject->id)->delete(); // Xóa kết quả thi của môn học này
        // Xóa tất cả các câu hỏi liên quan đến môn học này
    $subject->questions()->delete();

    // Xóa tất cả các lớp học thuộc môn học này
    $subject->classes()->each(function ($class) {
        $class->teachers()->detach(); // Xóa mối quan hệ giữa lớp và giáo viên
        $class->delete(); // Xóa lớp học
    });
    // Xóa môn học khỏi tất cả kỳ thi
    $subject->exams()->detach();

    // Xóa mối quan hệ giữa môn học và giáo viên
    $subject->teachers()->detach();

    // Xóa môn học
    $subject->delete();

    return response()->json([
        'message' => 'Môn học và các lớp học liên quan đã được xoá thành công!',
    ]);
    }

    // Lấy danh sách các giáo viên giảng dạy một môn học
    public function getTeachers($id)
    {
        $subject = Subject::with('teachers')->findOrFail($id);

        return response()->json([
            'message' => 'Danh sách giáo viên giảng dạy môn học.',
            'teachers' => $subject->teachers,
        ]);
    }

    

    // Liên kết môn học với nhiều kỳ thi
    public function linkExam(Request $request, $subjectId)
{
    $validated = $request->validate([
        'exam_ids' => 'required|array',
        'exam_ids.*' => 'exists:exams,id',
        'details' => 'required|array',
        'details.*.exam_id' => 'required|exists:exams,id',
        'details.*.time' => 'required|integer|min:1',
        'details.*.Qtype1' => 'required|integer|min:0',
        'details.*.Qtype2' => 'required|integer|min:0',
        'details.*.Qtype3' => 'required|integer|min:0',
        'details.*.Qnumber' => 'required|integer|min:1',
    ]);

    $subject = Subject::findOrFail($subjectId);

    // Chuẩn bị dữ liệu để liên kết môn học với kỳ thi
    $examData = [];
    $existingExams = []; // Lưu kỳ thi đã có
    $newExams = []; // Lưu kỳ thi hợp lệ để thêm mới

    foreach ($validated['details'] as $detail) {
        $examId = $detail['exam_id'];

        // Kiểm tra nếu môn học đã được liên kết với kỳ thi này
        $isLinked = $subject->exams()->where('exams.id', $examId)->exists();
        if ($isLinked) {
            $existingExams[] = $examId;
            continue; // Bỏ qua nếu đã liên kết
        }

        // Kiểm tra tổng số câu hỏi
        $totalQuestions = $detail['Qtype1'] + $detail['Qtype2'] + $detail['Qtype3'];
        if ($totalQuestions != $detail['Qnumber']) {
            return response()->json([
                'error' => 'Tổng số câu hỏi không khớp với số lượng câu hỏi đã chỉ định cho kỳ thi: ' . $examId
            ], 422);
        }

        // Chuẩn bị dữ liệu liên kết mới
        $examData[$examId] = [
            'time' => $detail['time'],
            'Qtype1' => $detail['Qtype1'],
            'Qtype2' => $detail['Qtype2'],
            'Qtype3' => $detail['Qtype3'],
            'Qnumber' => $detail['Qnumber'],
        ];
        $newExams[] = $examId;
    }

    // Liên kết môn học với các kỳ thi mới
    $subject->exams()->syncWithoutDetaching($examData);

    return response()->json([
        'message' => count($newExams) > 0 
            ? 'Môn học đã được liên kết với các kỳ thi mới thành công!'
            : 'Không có kỳ thi mới nào được liên kết.',
        'existing_exams' => $existingExams,
        'linked_exams' => $subject->exams()->withPivot('time', 'Qtype1', 'Qtype2', 'Qtype3', 'Qnumber')->get(),
    ]);
}
}
