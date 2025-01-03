<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Classroom;
use App\Models\ClassExam;
use App\Models\ClassStudent;
use App\Http\Controllers\API\V1\ExamController;
use Illuminate\Http\Request;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        $users = file_get_contents(base_path('/database/fakedata/users.json'));
        // $users = json_decode($users);
        $users = json_decode($users, true); // Thêm `true` để trả về mảng
        foreach ($users as $user) {
            $user['password'] = Hash::make('123456');
            $createdUser = User::create($user);

            if ($createdUser->role == 'TEACHER') {
                $createdUser->teacher()->create();
            } else if ($createdUser->role == 'STUDENT') {
                $createdUser->student()->create();
            } else if ($createdUser->role == 'SCHOOLBOARD') {
                $createdUser->schoolboard()->create();
            }
        }

        $subjects = [
    [
        "name" => "Anh văn",
        "teacher_ids" => [1, 2, 3], // Mảng ID giáo viên
    ]
];

$subjectController = new SubjectController();

foreach ($subjects as $subject) {
    // Tạo một request giả lập từ dữ liệu mảng
    $request = new Request([
        'name' => $subject['name'],
        'teacher_ids' => $subject['teacher_ids'],
    ]);

    // Gọi phương thức store và truyền vào request
    $subjectController->store($request);
}

        $questions = file_get_contents(base_path('/database/fakedata/questions.json'));
        $questions = json_decode($questions, true);
        foreach ($questions as $question) {
            \App\Models\Question::create($question);
        }


       $exams = [
    [
        "name" => "Kì thi cuối kì",
        "school_board_id" => 1,
        "examtype" => "NORMAL",
        "subjects" => [
            [
                "id" => 1, // ID môn học
                "time" => 60, // Thời gian làm bài
                "Qtype1" => 1, // Số lượng câu hỏi loại 1
                "Qtype2" => 1, // Số lượng câu hỏi loại 2
                "Qtype3" => 1, // Số lượng câu hỏi loại 3
                "Qnumber" => 3, // Tổng số câu hỏi
            ]
        ],
        "teacher_ids" => [1, 2], // Danh sách ID giáo viên tham gia kỳ thi
    ],
    [
        "name" => "Kì thi giữa kì",
        "school_board_id" => 1,
        "examtype" => "NORMAL",
        "subjects" => [
            [
                "id" => 1, // ID môn học
                "time" => 60, // Thời gian làm bài
                "Qtype1" => 2, // Số lượng câu hỏi loại 1
                "Qtype2" => 2, // Số lượng câu hỏi loại 2
                "Qtype3" => 1, // Số lượng câu hỏi loại 3
                "Qnumber" => 5, // Tổng số câu hỏi
            ]
        ],
        "teacher_ids" => [2], // Danh sách ID giáo viên tham gia kỳ thi
    ]
];

        // foreach ($exams as $exam) {
        //     Exam::create($exam);
        // }

        $examController = new ExamController();

        foreach ($exams as $examData) {
    // Tạo một request mới từ dữ liệu
    $request = new Request($examData);
    
    // Gọi phương thức createExam để tạo kỳ thi
    $examController->createExam($request);
}

        $classes = [
            [
                'name' => 'ENG_2024_1',
                'subject_id' => 1,
                'teacher_ids' => [1, 2]
            ],
            [
                 'name' => 'ENG_2024_2',
                 'subject_id' => 1,
                 'teacher_ids' => [2]
            ]
        ];

        foreach ($classes as $class) {
            $classData = Classroom::create([
        'name' => $class['name'],
        'subject_id' => $class['subject_id']
    ]);

    // Thêm giáo viên vào lớp học
    $classData->teachers()->sync($class['teacher_ids']);
        }
        $classExams = [
            [
                'class_id' => 1,
                'exam_id' => 1,
            ],
            [
                'class_id' => 2,
                'exam_id' => 2,
            ],
        ];

        $examController = new ExamController();

foreach ($classExams as $classExam) {
    // Tạo một request giả lập từ dữ liệu mảng
    $request = new Request([
        'exam_id' => $classExam['exam_id']
    ]);
    
    // Gọi phương thức addExamToClass và truyền vào classId
    $examController->addExamToClass($request, $classExam['class_id']);
}


        $classStudents = [
            [
                'class_id' => 1,
                'student_ids' => [1,2,3],
            ],
            [
                'class_id' => 2,
                'student_ids' => [4,5,6],
            ],
           
        ];

        $classStudentController = new ClassStudentController();

foreach ($classStudents as $classStudent) {
    // Tạo một request giả lập từ dữ liệu mảng
    $request = new Request([
        'class_id' => $classStudent['class_id'],
        'student_ids' => $classStudent['student_ids'],
    ]);

    // Gọi phương thức store và truyền vào request
    $classStudentController->store($request);
}
    }
}
