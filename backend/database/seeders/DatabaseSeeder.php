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
                "name" => "Anh văn 1",
                "teacher_id" => "1",
            ],
            [
                "name" => "Anh văn 2",
                "teacher_id" => "1",
            ]
        ];
        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        $questions = file_get_contents(base_path('/database/fakedata/questions.json'));
        $questions = json_decode($questions, true);
        foreach ($questions as $question) {
            \App\Models\Question::create($question);
        }


        $exams = [
            [
                "name" => "Final Exam",
                "subject_id" => 1,
                "teacher_id" => 1,
                "time" => 60,
                "examtype" => "NORMAL",
                "Qtype1" => 1,
                "Qtype2" => 1,
                "Qtype3" => 1,
                "Qnumber" => 3

            ],
            [
                "name" => "Final Exam",
                "subject_id" => 1,
                "teacher_id" => 1,
                "time" => 60,
                "examtype" => "NORMAL",
                "Qtype1" => 1,
                "Qtype2" => 1,
                "Qtype3" => 1,
                "Qnumber" => 3

            ]
        ];

        // foreach ($exams as $exam) {
        //     Exam::create($exam);
        // }

        $examController = new ExamController();

        foreach ($exams as $exam) {
            $request = new Request($exam);
            $examController->createExam($request);
        }

        $classes = [
            [
                'name' => 'ENG_2024_1',
                'subject_id' => 1,
                'teacher_id' => 1,
            ],
            [
                'name' => 'ENG_2024_2',
                'subject_id' => 2,
                'teacher_id' => 1
            ]
        ];

        foreach ($classes as $class) {
            Classroom::create($class);
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

        foreach ($classExams as $classExam) {
            ClassExam::create($classExam);
        }


        $classStudents = [
            [
                'class_id' => 1,
                'student_id' => 1,
            ],
            [
                'class_id' => 1,
                'student_id' => 2,
            ],
            [
                'class_id' => 2,
                'student_id' => 3,
            ],
            [
                'class_id' => 2,
                'student_id' => 4,
            ],
        ];

        foreach ($classStudents as $classStudent) {
            ClassStudent::create($classStudent);
        }
    }
}
