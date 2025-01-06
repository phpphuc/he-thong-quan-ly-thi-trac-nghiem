<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\ClassStudent;
use App\Models\Student;
use App\Models\Classroom;

class ClassStudentController extends Controller
{
    // Lấy danh sách sinh viên trong lớp học.
    public function index($classId)
    {
        $students = ClassStudent::where('class_id', $classId)
            ->with('student') 
            ->get();

        return response()->json([
            'message' => 'Danh sách sinh viên trong lớp học.',
            'data' => $students,
        ]);
    }

    // Thêm sinh viên vào lớp học.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);
    
        $classId = $validated['class_id'];
        $studentIds = $validated['student_ids'];
    
        $addedStudents = [];
        $alreadyInClass = [];
        $conflictingStudents = []; // Mảng lưu sinh viên có lớp khác cùng môn học
    
        foreach ($studentIds as $studentId) {
            // Kiểm tra nếu sinh viên đã học lớp khác của cùng môn học
            $existingClass = ClassStudent::where('student_id', $studentId)
                ->whereHas('class', function ($query) use ($classId) {
                    $query->where('subject_id', $classId);
                })
                ->exists();
    
            if ($existingClass) {
                // Thông báo và bỏ qua sinh viên này
                $conflictingStudents[] = $studentId;
                continue;
            }
    
            // Kiểm tra xem sinh viên đã tham gia lớp học này chưa
            $exists = ClassStudent::where('class_id', $classId)
                ->where('student_id', $studentId)
                ->exists();
    
            if ($exists) {
                $alreadyInClass[] = $studentId;
            } else {
                $classStudent = ClassStudent::create([
                    'class_id' => $classId,
                    'student_id' => $studentId,
                ]);
                $addedStudents[] = $classStudent;
            }
        }
    
        return response()->json([
            'message' => 'Thêm sinh viên vào lớp học hoàn tất.',
            'added_students' => $addedStudents,
            'already_in_class' => $alreadyInClass,
            'conflicting_students' => $conflictingStudents, // Trả về danh sách sinh viên có lớp khác cùng môn học
        ]);
    }

    // Xóa sinh viên khỏi lớp học.
    public function destroy(Request $request, $classId)
{
    $validated = $request->validate([
        'student_ids' => 'required|array',
        'student_ids.*' => 'exists:students,id',
    ]);

    $deletedCount = ClassStudent::where('class_id', $classId)
        ->whereIn('student_id', $validated['student_ids'])
        ->delete();

    return response()->json([
        'message' => "Xóa thành công {$deletedCount} sinh viên khỏi lớp học.",
    ]);
}

    // Tìm kiếm sinh viên trong lớp học.
    public function search(Request $request, $classId)
    {
        $searchTerm = $request->get('query');

        $students = Student::where('id', 'LIKE', "%{$searchTerm}%")
            ->whereHas('classes', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->get();

        return response()->json([
            'message' => 'Kết quả tìm kiếm sinh viên.',
            'data' => $students,
        ]);
    }
}
