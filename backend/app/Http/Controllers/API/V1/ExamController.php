<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Student;
use App\Models\Subject;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::all(); // Lấy tất cả các bài thi
        return response()->json([
            'exams' => $exams,
        ], 200);
    }
    public function createExam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'time' => 'required|integer|min:1',
            'examtype' => 'required|in:NORMAL,GENERAL EXAM',
            'Qtype1' => 'required|integer|min:0',
            'Qtype2' => 'required|integer|min:0',
            'Qtype3' => 'required|integer|min:0',
            'Qnumber' => 'required|integer|min:1',
        ]);

        $totalQuestions = $validated['Qtype1'] + $validated['Qtype2'] + $validated['Qtype3'];
        if ($totalQuestions != $validated['Qnumber']) {
            return response()->json(['error' => 'Tổng số câu hỏi không khớp với số lượng câu hỏi đã chỉ định.'], 422);
        }

        // Lấy danh sách câu hỏi
        $questions = Question::where('subject_id', $validated['subject_id'])
            ->whereIn('level', ['Nhận biết', 'Thông hiểu', 'Vận dụng'])
            ->get()
            ->groupBy('level');

        $selectedQuestions = collect();

        $selectedQuestions = $selectedQuestions->merge(
            $questions->get('Nhận biết', collect())->random(min($validated['Qtype1'], $questions->get('Nhận biết', collect())->count()))
        );

        $selectedQuestions = $selectedQuestions->merge(
            $questions->get('Thông hiểu', collect())->random(min($validated['Qtype2'], $questions->get('Thông hiểu', collect())->count()))
        );

        $selectedQuestions = $selectedQuestions->merge(
            $questions->get('Vận dụng', collect())->random(min($validated['Qtype3'], $questions->get('Vận dụng', collect())->count()))
        );

        $selectedQuestions = $selectedQuestions->shuffle();

        $subject = Subject::find($validated['subject_id']);

        if (!$subject) {
            return response()->json(['error' => 'Subject không tồn tại.'], 404);
        }

        $exam = Exam::create([
            'name' => $validated['name'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'time' => $validated['time'],
            'examtype' => $validated['examtype'],
            'Qtype1' => $validated['Qtype1'],
            'Qtype2' => $validated['Qtype2'],
            'Qtype3' => $validated['Qtype3'],
            'Qnumber' => $validated['Qnumber'],
        ]);

        $exam->questions()->attach($selectedQuestions->pluck('id')->toArray());

        return response()->json([
            'message' => 'Kỳ thi đã được tạo thành công!',
            'exam' => $exam,
        ], 201);
    }
    // danh sách bài thi dành cho sinh viên
    public function getExamsForStudent($id)
    {
        $student = Student::findOrFail($id);

        // Lấy danh sách lớp học của sinh viên
        $classrooms = $student->classes()->pluck('classes.id');

        // Lấy danh sách bài thi thuộc các lớp học đó
        $exams = Exam::whereHas('classrooms', function ($query) use ($classrooms) {
            $query->whereIn('classes.id', $classrooms);
        })->get();

        $examDetails = $exams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'name' => $exam->name,
                'subject' => [
                    'name' => $exam->subject->name,
                ],
                'teacher' => [
                    'name' => $exam->teacher->user->name,
                    'email' => $exam->teacher->user->email,
                ],
                'time' => $exam->time,
                'examtype' => $exam->examtype,
                // 'questions' => $exam->questions->shuffle(),
            ];
        });

        return response()->json([
            'exams' => $examDetails,
        ]);
    }

    public function showExam($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);

        $shuffledQuestions = $exam->questions->shuffle();

        return response()->json([
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'subject' => [
                    'id' => $exam->subject->id,
                    'name' => $exam->subject->name,
                ],
                'teacher' => [
                    'id' => $exam->teacher->id,
                    'name' => $exam->teacher->user->name,
                    'email' => $exam->teacher->user->email,
                ],
                'time' => $exam->time,
                'examtype' => $exam->examtype,
                'questions' => $shuffledQuestions,
            ],
        ], 200);
        return response()->json([
            'exam' => $exam,
        ], 200);
    }

    public function submitExam(Request $request, $id)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $exam = Exam::with('questions')->findOrFail($id);

        $score = 0;
        foreach ($validated['answers'] as $answer) {
            $question = $exam->questions->find($answer['question_id']);
            if ($question && strtolower($question->rightanswer) === strtolower($answer['answer'])) {
                $score++;
            }
        }

        $result = Result::create([
            'exam_id' => $exam->id,
            'student_id' => $validated['student_id'],
            'score' => $score,
        ]);

        return response()->json([
            'message' => 'Exam submitted successfully!',
            'score' => $score,
            'total' => $exam->questions->count(),
            'result' => $result,
        ], 200);
    }
}