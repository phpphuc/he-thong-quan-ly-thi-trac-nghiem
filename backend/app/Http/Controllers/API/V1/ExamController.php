<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;

class ExamController extends Controller
{
       public function index()
    {
        $exams = Exam::with('subjects')->get(); // Lấy tất cả các bài thi và môn học liên quan

        // Thêm tên môn học vào mỗi bài thi
        $exams->transform(function ($exam) {
            $exam->subject_names = $exam->subjects->pluck('name'); // Lấy danh sách tên môn học
            unset($exam->subjects); // Loại bỏ thông tin chi tiết về môn học
            return $exam;
        });

        return response()->json([
            'exams' => $exams,
        ], 200);
    }
    //tạo một kì thi
    public function createExam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'school_board_id' => 'required|exists:school_boards,id',
            'examtype' => 'required|in:NORMAL,GENERAL EXAM',
            'subjects' => 'required|array',
            'subjects.*.id' => 'required|exists:subjects,id',
            'subjects.*.time' => 'required|integer|min:1',
            'subjects.*.Qtype1' => 'required|integer|min:0',
            'subjects.*.Qtype2' => 'required|integer|min:0',
            'subjects.*.Qtype3' => 'required|integer|min:0',
            'subjects.*.Qnumber' => 'required|integer|min:1',
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:teachers,id',
            
        ]);
        
        // Kiểm tra tổng số câu hỏi với Qtype1, Qtype2, Qtype3
    foreach ($validated['subjects'] as $subject) {
        $totalQuestions = $subject['Qtype1'] + $subject['Qtype2'] + $subject['Qtype3'];
        if ($totalQuestions != $subject['Qnumber']) {
            return response()->json(['error' => 'Tổng số câu hỏi không khớp với số lượng câu hỏi đã chỉ định cho môn học: ' . $subject['id']], 422);
        }
    }

        // Tạo kỳ thi
        $exam = Exam::create([
        'name' => $validated['name'],
        'school_board_id' => $validated['school_board_id'],
        'examtype' => $validated['examtype'],
    ]);

        // Liên kết môn học và thời gian làm bài
        $subjects = collect($validated['subjects'])->mapWithKeys(function ($subject) {
            return [
            $subject['id'] => [
                'time' => $subject['time'],
                'Qtype1' => $subject['Qtype1'],
                'Qtype2' => $subject['Qtype2'],
                'Qtype3' => $subject['Qtype3'],
                'Qnumber' => $subject['Qnumber'],
            ],
        ];
    });
        $exam->subjects()->sync($subjects);

        // Liên kết giáo viên với kỳ thi   
        $exam->teachers()->sync($validated['teacher_ids']);
        // Trả về thông tin kỳ thi 
        return response()->json([
            'message' => 'Kỳ thi đã được tạo thành công!',
            'exam' => $exam,
            'subjects' => $exam->subjects()->withPivot('time', 'Qtype1', 'Qtype2', 'Qtype3', 'Qnumber' )->get(),
            'teachers' => $exam->teachers,
            
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

    return response()->json([
        'exams' => $exams,
    ]);
}

/**
     * Lấy thông tin kỳ thi.
     */

    public function showExam($id)
    {
        // Lấy kỳ thi với các thông tin liên quan (môn học, giáo viên, câu hỏi)
    $exam = Exam::with(['subjects.teachers', 'questions.subject', 'schoolBoard'])->findOrFail($id);
        
    // Lấy thông tin môn học, thời gian và giáo viên dạy môn học
    $subjectsWithDetails = $exam->subjects->map(function ($subject) {
        // Lấy số lượng câu hỏi Qtype
        $Qtype1 = $subject->pivot->Qtype1;
        $Qtype2 = $subject->pivot->Qtype2;
        $Qtype3 = $subject->pivot->Qtype3;
        // Lấy danh sách câu hỏi Qtype1 cho môn học
        $questionsQtype1 = $subject->questions()->where('level', 'Nhận biết')->take($Qtype1)->get();
        $questionsQtype2 = $subject->questions()->where('level', 'Thông hiểu')->take($Qtype2)->get();
        $questionsQtype3 = $subject->questions()->where('level', 'Vận dụng')->take($Qtype3)->get();
        return [
            'subject_name' => $subject->name,
            'time' => $subject->pivot->time, // Lấy thời gian làm bài cho môn học từ bảng pivot
            'Qtype1_count' => $Qtype1,  
            'questions_Qtype1' => $questionsQtype1,
            'Qtype2_count' => $Qtype2,  
            'questions_Qtype2' => $questionsQtype2,
            'Qtype3_count' => $Qtype3,  
            'questions_Qtype3' => $questionsQtype3, 
            'Qnumber' => $subject->pivot->Qnumber,
            'teachers' => $subject->teachers->map(function ($teacher) {
                return [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->user->name, // Assuming teacher has a relation with user
                ];
            }),
            
        ];
    });

    // Trả về thông tin kỳ thi, bao gồm môn học, giáo viên và câu hỏi
    return response()->json([
        'exam' => [
            'id' => $exam->id,
            'name' => $exam->name,
            'examtype' => $exam->examtype,
            'school_board_name' => $exam->schoolBoard->user->name, // Tên của Ban Giám Hiệu
            'subjects_with_details' => $subjectsWithDetails, // Môn học, thời gian giáo viên và câu hỏi
        ],
    ], 200);
        
    }
/**
     * Nộp bài thi và tính điểm.
     */
    public function submitExam(Request $request, $id)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $exam = Exam::with('questions')->findOrFail($id);

        $totalQuestions = $exam->questions->count();
        $correctAnswers = 0;
        foreach ($validated['answers'] as $answer) {
            $question = $exam->questions->find($answer['question_id']);
            if ($question && strtolower($question->rightanswer) === strtolower($answer['answer'])) {
                $correctAnswers++;
            }
        }
        $score = ($correctAnswers / $totalQuestions) * 10;
        $score = round($score, 2);

        $result = Result::create([
            'exam_id' => $exam->id,
            'student_id' => $validated['student_id'],
            'score' => $score,
        ]);

        return response()->json([
            'message' => 'Exam submitted successfully!',
            'score' => $score,
            'total' => $totalQuestions,
            'result' => $result,
        ], 200);
    }
    //Giáo viên dạy các môn học trong kỳ thi
    public function getExamSubjectsAndTeachers($examId)
{
    $exam = Exam::with(['subjects.teachers'])->findOrFail($examId);

    $subjectsWithTeachers = $exam->subjects->map(function ($subject) {
        return [
            'subject_name' => $subject->name,
            'teachers' => $subject->teachers->map(function ($teacher) {
                return [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->user->name, // assuming teacher has a relation with user
                ];
            }),
        ];
    });

    return response()->json([
        'exam_name' => $exam->name,
        'subjects_with_teachers' => $subjectsWithTeachers,
    ]);
}
}
