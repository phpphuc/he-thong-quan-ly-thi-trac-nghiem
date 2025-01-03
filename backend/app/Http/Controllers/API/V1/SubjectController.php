<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Subject;
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
        $subject->teachers()->detach();  // Xóa mối quan hệ giữa môn học và giáo viên trước khi xóa môn học
        $subject->delete();

        return response()->json([
            'message' => 'Môn học đã được xoá thành công!',
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

    // Liên kết môn học với các lớp học
    public function linkClass(Request $request, $id)
    {
        // Xác thực dữ liệu
    $request->validate([
        'class_ids' => 'required|array',  // Dữ liệu đầu vào là mảng các class_id
        'class_ids.*' => 'exists:classes,id',  // Kiểm tra từng class_id có tồn tại trong bảng classes
    ]);

    // Tìm môn học theo ID
    $subject = Subject::findOrFail($id);

    // Lặp qua từng lớp học và cập nhật trường subject_id
    foreach ($request->class_ids as $class_id) {
        $class = Classroom::findOrFail($class_id);
        $class->subject_id = $subject->id;  // Gán subject_id cho lớp học
        $class->save();  // Lưu lại thay đổi
    }

    return response()->json([
        'message' => 'Các lớp học đã được liên kết với môn học thành công!',
        'subject' => $subject,
        'classes' => Classroom::whereIn('id', $request->class_ids)->get(),  // Trả về danh sách các lớp học đã liên kết
    ]);
    }

    // Liên kết môn học với kỳ thi
    public function linkExam(Request $request, $id)
    {
        $request->validate([
            'exam_ids' => 'required|array',
            'exam_ids.*' => 'exists:exams,id',
        ]);

        $subject = Subject::findOrFail($id);
        $subject->exams()->sync($request->exam_ids);
        

        return response()->json([
            'message' => 'Môn học đã được liên kết với kỳ thi thành công!',
            'subject' => $subject,
            'exams'   => $subject->exams,
        ]);
    }
}
