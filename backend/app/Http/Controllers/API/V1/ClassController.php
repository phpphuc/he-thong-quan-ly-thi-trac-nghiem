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
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classes' => 'required|array',
            'classes.*.name' => 'required|string|max:255',
            'classes.*.teacher_ids' => 'required|array',
            'classes.*.teacher_ids.*' => 'exists:teachers,id',
        ]);
    
        $subject = Subject::findOrFail($request->subject_id);
    
        // Lấy danh sách ID giáo viên đang dạy môn học
        $subjectTeacherIds = $subject->teachers->pluck('id')->toArray();
    
        $createdClasses = [];
    
        foreach ($request->classes as $classData) {
            // Kiểm tra giáo viên có thuộc môn học không
            foreach ($classData['teacher_ids'] as $teacherId) {
                if (!in_array($teacherId, $subjectTeacherIds)) {
                    return response()->json([
                        'error' => "Giáo viên ID $teacherId không dạy môn học này",
                    ], 422);
                }
            }
    
            // Kiểm tra tên lớp học có bị trùng không
            if (Classroom::where('name', $classData['name'])->exists()) {
                return response()->json([
                    'error' => "Tên lớp học '{$classData['name']}' đã tồn tại, vui lòng chọn tên khác.",
                ], 422);
            }
    
            // Tạo lớp học mới
            $class = Classroom::create([
                'name' => $classData['name'],
                'subject_id' => $request->subject_id,
            ]);
    
            // Thêm giáo viên vào lớp học
            $class->teachers()->sync($classData['teacher_ids']);
    
            // Lưu lớp học vào mảng kết quả
            $createdClasses[] = [
                'id' => $class->id,
                'name' => $class->name,
                'subject_id' => $class->subject_id,
                'teachers' => $class->teachers,
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
        // Kiểm tra tên lớp không trùng
        $duplicate = Classroom::where('name', $request->name)
            ->where('subject_id', $class->subject_id)
            ->where('id', '!=', $class_id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'error' => 'Tên lớp đã tồn tại, vui lòng chọn tên khác.',
            ], 422);
        }

        $class->name = $request->name;
    }

    if ($request->has('subject_id')) {
        $class->subject_id = $request->subject_id;

        // Kiểm tra giáo viên phù hợp với môn học mới
        if ($request->has('teacher_ids')) {
            $validTeachers = Teacher::whereIn('id', $request->teacher_ids)
                ->whereHas('subjects', function ($query) use ($request) {
                    $query->where('id', $request->subject_id);
                })->count();

            if ($validTeachers !== count($request->teacher_ids)) {
                return response()->json([
                    'error' => 'Một hoặc nhiều giáo viên không dạy môn học này.',
                ], 422);
            }
        }
    }

    if ($request->has('teacher_ids')) {
        // Đồng bộ giáo viên vào lớp
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
        'teacher_ids.*' => 'exists:teachers,id', // Kiểm tra teacher_id tồn tại
    ]);

    $class = Classroom::findOrFail($class_id);

    // Lấy môn học của lớp
    $subjectId = $class->subject_id;

    // Lọc danh sách giáo viên theo môn học của lớp
    $validTeacherIds = Teacher::whereHas('subjects', function ($query) use ($subjectId) {
        $query->where('id', $subjectId);
    })->pluck('id')->toArray();

    // Tìm các giáo viên không hợp lệ
    $invalidTeachers = array_diff($request->teacher_ids, $validTeacherIds);

    if (!empty($invalidTeachers)) {
        return response()->json([
            'error' => 'Một số giáo viên không dạy môn học của lớp này.',
            'invalid_teacher_ids' => $invalidTeachers,
        ], 422);
    }

    // Đồng bộ các giáo viên hợp lệ vào lớp học
    $class->teachers()->sync($request->teacher_ids);

    return response()->json([
        'message' => 'Các giáo viên đã được thêm vào lớp học thành công.',
        'class' => $class,
        'teachers' => $class->teachers,
    ]);
}

  
}
