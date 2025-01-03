<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Classroom; 
use App\Models\Subject;
use App\Models\Exam;
class TeacherController extends Controller
{
    /**
     * Lấy danh sách giáo viên.
     */
    public function index()
    {
        $teachers = Teacher::with(['classes', 'subjects'])->get();

        return response()->json([
            'message' => 'Danh sách giáo viên.',
            'data' => $teachers,
        ]);
    }

    /**
     * Tạo mới giáo viên.
     
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|uuid|unique:teachers,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'nullable|string|max:15',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json([
            'message' => 'Tạo giáo viên thành công.',
            'data' => $teacher,
        ]);
    }*/

    /**
     * Cập nhật thông tin giáo viên.
     */
    public function update(Request $request, $teacherId)
    {
       // Tìm giáo viên theo teacherId
    $teacher = Teacher::findOrFail($teacherId);

    // Xác thực các trường cần thiết
    $validated = $request->validate([
        'user_id' => 'sometimes|exists:users,id',  // Nếu có user_id thì cần tồn tại trong bảng users
        'subject_ids' => 'nullable|array', // Cập nhật các môn học, nếu có
        'subject_ids.*' => 'exists:subjects,id', // Mỗi subject_id phải tồn tại trong bảng subjects
        'class_ids' => 'nullable|array', // Cập nhật các lớp học, nếu có
        'class_ids.*' => 'exists:classes,id', // Mỗi class_id phải tồn tại trong bảng classes
    ]);

    // Cập nhật user_id nếu có
    if ($request->has('user_id')) {
        $teacher->user_id = $validated['user_id'];
    }

    // Cập nhật các lớp học nếu có
    if ($request->has('class_ids')) {
        // Đồng bộ lại mối quan hệ với các lớp học mà không xóa lớp đã có
        $teacher->classes()->syncWithoutDetaching($validated['class_ids']);
    }

    // Cập nhật các môn học nếu có
    if ($request->has('subject_ids')) {
        // Đồng bộ lại mối quan hệ với các môn học mà không xóa môn đã có
        $teacher->subjects()->syncWithoutDetaching($validated['subject_ids']);
    }

    // Lưu các thay đổi cho trường thông tin giáo viên
    $teacher->save();

    return response()->json([
        'message' => 'Cập nhật thông tin giáo viên thành công.',
        'data' => $teacher,
    ]);
    }

    /**
     * Xoá giáo viên.
     */
    public function destroy($teacherId)
    {
        // Kiểm tra sự tồn tại của giáo viên trước khi xóa
    $teacher = Teacher::findOrFail($teacherId);

    // Kiểm tra nếu giáo viên đang dạy các lớp học, môn học, hoặc kỳ thi để thông báo cho người dùng
    if ($teacher->classes()->count() > 0 || $teacher->subjects()->count() > 0) {
        return response()->json([
            'message' => 'Không thể xóa giáo viên vì họ đang giảng dạy các lớp học hoặc môn học.',
        ], 400);
    }

    // Xóa giáo viên
    $teacher->delete();

    return response()->json([
        'message' => 'Xoá giáo viên thành công.',
    ]);
    }

    

    // Lấy danh sách lớp học của giáo viên
public function getClasses($teacherId)
{
    $teacher = Teacher::with('classes')->findOrFail($teacherId);

    return response()->json([
        'message' => 'Danh sách lớp học của giáo viên.',
        'classes' => $teacher->classes,
    ]);
}

// Lấy danh sách môn học của giáo viên
public function getSubjects($teacherId)
{
    $teacher = Teacher::with('subjects')->findOrFail($teacherId);

    return response()->json([
        'message' => 'Danh sách môn học của giáo viên.',
        'subjects' => $teacher->subjects,
    ]);
}

    /**
     * Tổ chức kỳ thi.
     */
    public function organizeExam(Request $request)
    {
        // Gọi đến ExamController để thực hiện việc tổ chức kỳ thi
    $examController = new ExamController();

    // Chuyển tiếp request đến phương thức createExam
    return $examController->createExam($request);
    }

    public function addClassesToTeacher(Request $request, $teacherId)
{
    $request->validate([
        'class_ids' => 'required|array',
        'class_ids.*' => 'exists:classes,id', // Kiểm tra tất cả class_id có tồn tại trong bảng classes
    ]);

    $teacher = Teacher::findOrFail($teacherId);

    // Đồng bộ các lớp học vào giáo viên
    $teacher->classes()->sync($request->class_ids);

    return response()->json([
        'message' => 'Các lớp học đã được thêm vào giáo viên thành công.',
        'teacher' => $teacher,
        'classes' => $teacher->classes,
    ]);
}
}
