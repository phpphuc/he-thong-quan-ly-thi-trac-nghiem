<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;

class ClassController extends Controller
{
    //Tạo một lớp học mới
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
    $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'classes' => 'required|array', // Mảng lớp học
        'classes.*.name' => 'required|string|max:255', // Tên lớp học
        'classes.*.teacher_ids' => 'required|array', // Mảng ID giáo viên cho mỗi lớp
        'classes.*.teacher_ids.*' => 'exists:teachers,id', // Kiểm tra từng giáo viên có tồn tại
    ]);

    $createdClasses = [];

    // Lặp qua từng lớp học để tạo mới
    foreach ($request->classes as $classData) {
        // Tạo lớp học mới cho mỗi môn học
        $class = Classroom::create([
            'name' => $classData['name'],
            'subject_id' => $request->subject_id, // Gán môn học cho lớp
        ]);

        // Thêm giáo viên vào lớp học
        $class->teachers()->sync($classData['teacher_ids']);

        // Lưu lớp học mới vào mảng, bao gồm id của lớp học
        $createdClasses[] = [
            'id' => $class->id, // Thêm ID của lớp học vào
            'name' => $class->name, // Tên lớp học
            'subject_id' => $class->subject_id, // Môn học của lớp
            'teachers' => $class->teachers, // Danh sách giáo viên
        ];
    }

    return response()->json([
        'message' => 'Các lớp học và giáo viên đã được tạo thành công!',
        'classes' => $createdClasses,
    ]);
    }

    //Sửa thông tin lớp học
    public function update(Request $request, $class_id)
    {
        $class = Classroom::findOrFail($class_id);

    $request->validate([
        'name' => 'sometimes|string|max:255',
        'subject_id' => 'sometimes|exists:subjects,id',
        'teacher_ids' => 'sometimes|array',
        'teacher_ids.*' => 'exists:teachers,id',
    ]);

    if ($request->has('name')) {
        $class->name = $request->name;
    }

    if ($request->has('subject_id')) {
        $class->subject_id = $request->subject_id;
    }

    if ($request->has('teacher_ids')) {
        // Đồng bộ giáo viên mới vào lớp
        $class->teachers()->sync($request->teacher_ids);
    }

    $class->save();

    return response()->json([
        'message' => 'Thông tin lớp học đã được cập nhật!',
        'class' => $class,
        'teachers' => $class->teachers,
    ]);
    }

    // Xóa một lớp học
    public function destroy($class_id)
    {
        $class = Classroom::findOrFail($class_id);

        // Kiểm tra lớp học có sinh viên hay không
        if ($class->students()->count() > 0) {
            return response()->json([
                'message' => 'Lớp học này có sinh viên, không thể xóa!',
            ], 400);
        }
    
        // Xóa mối quan hệ giáo viên với lớp học
        $class->teachers()->detach();
    
        // Xóa lớp học
        $class->delete();
    
        return response()->json([
            'message' => 'Lớp học đã được xoá thành công!',
        ]);
    }

    // Xem thông tin thành viên trong lớp (sinh viên, giáo viên)
    public function showMembers($class_id)
    {
        $class = Classroom::findOrFail($class_id);

        // Lấy danh sách giáo viên của lớp học
        $teachers = $class->teachers;
    
        // Lấy danh sách sinh viên của lớp học
        $students = $class->students;
    
        return response()->json([
            'class' => $class,
            'teachers' => $teachers,
            'students' => $students,
        ]);
    }
    public function addTeachersToClass(Request $request, $class_id)
{
    $request->validate([
        'teacher_ids' => 'required|array',
        'teacher_ids.*' => 'exists:teachers,id', // Kiểm tra tất cả teacher_id có tồn tại trong bảng teachers
    ]);

    $class = Classroom::findOrFail($class_id);

    // Đồng bộ các giáo viên vào lớp học
    $class->teachers()->sync($request->teacher_ids);

    return response()->json([
        'message' => 'Các giáo viên đã được thêm vào lớp học thành công.',
        'class' => $class,
        'teachers' => $class->teachers,
    ]);
}
}
