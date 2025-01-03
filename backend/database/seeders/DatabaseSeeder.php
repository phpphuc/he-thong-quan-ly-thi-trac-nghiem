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

       // Seed subjects
       $subjects = [
        ['name' => 'Anh văn'],
    ];
    foreach ($subjects as $subject) {
        Subject::create($subject);
    }

        $questions = file_get_contents(base_path('/database/fakedata/questions.json'));
        $questions = json_decode($questions, true);
        foreach ($questions as $question) {
            \App\Models\Question::create($question);
        }


        // Seed exams
        $exams = [
            [
                'name' => 'Kì thi cuối kì',
                'school_board_id' => 1,
                'examtype' => 'NORMAL',
            ],
            [
                'name' => 'Kì thi giữa kì',
                'school_board_id' => 1,
                'examtype' => 'NORMAL',
            ],
        ];
        foreach ($exams as $exam) {
            Exam::create($exam);
        }


        // Seed classrooms
        $classes = [
            ['name' => 'ENG_2024_1', 'subject_id' => 1],
            ['name' => 'ENG_2024_2', 'subject_id' => 1],
        ];
        foreach ($classes as $class) {
            Classroom::create($class);
        }
        // Seed relationships
        $classExams = [
            ['class_id' => 1, 'exam_id' => 1],
            ['class_id' => 2, 'exam_id' => 2],
        ];
        foreach ($classExams as $classExam) {
            \DB::table('class_exam')->insert($classExam);
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

        $classStudents = [
            ['class_id' => 1, 'student_ids' => [1, 2, 3]],
            ['class_id' => 2, 'student_ids' => [4, 5, 6]],
        ];
        foreach ($classStudents as $classStudent) {
            foreach ($classStudent['student_ids'] as $studentId) {
                \DB::table('class_student')->insert([
                    'class_id' => $classStudent['class_id'],
                    'student_id' => $studentId,
                ]);
            }
        }
    }
}
