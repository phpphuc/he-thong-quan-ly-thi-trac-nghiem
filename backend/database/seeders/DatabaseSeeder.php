<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
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
            }
            else if ($createdUser->role == 'SCHOOLBOARD') {
                $createdUser->schoolboard()->create();
            }
        }

        $subjects = [
            [
                "subject_name" => "Anh văn 1",
                "teacher_id" => "1",
            ],
            [
                "subject_name" => "Anh văn 2",
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
    }
}
