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
            'student_ids' => 'required|array', // Cho phép danh sách ID sinh viên
            'student_ids.*' => 'exists:students,id', // Kiểm tra từng ID
        ]);
    
        $classId = $validated['class_id'];
        $studentIds = $validated['student_ids'];
    
        $addedStudents = [];
        $alreadyInClass = [];
    
        foreach ($studentIds as $studentId) {
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
