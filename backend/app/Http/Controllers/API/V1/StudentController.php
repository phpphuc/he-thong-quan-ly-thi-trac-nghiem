<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassStudent;
use App\Models\Result;

class StudentController extends Controller
{
    // Lấy danh sách tất cả sinh viên
    public function index()
    {
        $students = Student::with('user')->get();
        return response()->json(['success' => true, 'data' => $students], 200);
    }

    // Lấy thông tin sinh viên cụ thể
    public function show($id)
    {
        $student = Student::with('user')->find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $student], 200);
    }

    

    // Cập nhật thông tin sinh viên
    public function update(Request $request, $id)
    {
        $student = Student::find($id);

    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }

    $validatedData = $request->validate([
        'user.name' => 'nullable|string',
        'user.email' => 'nullable|email|unique:users,email,' . $student->user_id,
        'user.phone' => 'nullable|string',
        'user.address' => 'nullable|string',
        'user.birth_date' => 'nullable|date',
    ]);

    // Cập nhật thông tin sinh viên
    if (isset($validatedData['user'])) {
        
        $user = $student->user;

        // Cập nhật thông tin người dùng
        $user->update($validatedData['user']);

        // Lưu thông tin người dùng sau khi cập nhật
        $user->save();
    }

    return response()->json(['success' => true, 'data' => $student->load('user')], 200);
    }

    // Xoá sinh viên
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }
        // Xóa giáo viên khỏi bảng users
    $student->user()->delete();
        // Xóa các kết quả của sinh viên
    $student->results()->delete();
        // Xóa sinh viên khỏi tất cả lớp học
        $student->classes()->detach();
    
        $student->delete();
        return response()->json(['success' => true, 'message' => 'Student deleted successfully'], 200);
    }

    

    // Xem lịch sử kết quả thi của sinh viên
    public function examHistory($studentId)
    {
        $results = Result::with(['exam.subjects:id,name', 'exam.teachers:id,name']) // Bổ sung thông tin chi tiết
        ->where('student_id', $studentId)
        ->paginate(10); // Phân trang

    if ($results->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No results found for this student'], 404);
    }

    return response()->json(['success' => true, 'data' => $results], 200);
    }

    // Lấy danh sách các lớp học mà sinh viên thuộc về
public function getClasses($studentId)
{
    $student = Student::find($studentId);

    if (!$student) {
        return response()->json(['success' => false, 'message' => 'Student not found'], 404);
    }

    $classes = $student->classes()->get(); // Quan hệ giữa Student và Class
    return response()->json(['success' => true, 'data' => $classes], 200);
}
}
