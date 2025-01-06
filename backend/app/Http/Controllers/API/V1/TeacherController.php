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
     * Cập nhật thông tin giáo viên.
     */
    public function update(Request $request, $id)
    {
       // Tìm giáo viên theo teacherId
    $teacher = Teacher::findOrFail($id);
    if (!$teacher) {
        return response()->json(['error' => 'Teacher not found'], 404);
    }
    $validatedData = $request->validate([
        'user.name' => 'nullable|string',
        'user.email' => 'nullable|email|unique:users,email,' . $teacher->user_id,
        'user.phone' => 'nullable|string',
        'user.address' => 'nullable|string',
        'user.birth_date' => 'nullable|date',
    ]);

    // Cập nhật thông tin sinh viên
    if (isset($validatedData['user'])) {
        
        $user = $teacher->user;

        // Cập nhật thông tin người dùng
        $user->update($validatedData['user']);

        // Lưu thông tin người dùng sau khi cập nhật
        $user->save();
    }

    return response()->json(['success' => true, 'data' => $teacher->load('user')], 200);
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
    // Xóa giáo viên khỏi bảng users
    $teacher->user()->delete();

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

    
}
