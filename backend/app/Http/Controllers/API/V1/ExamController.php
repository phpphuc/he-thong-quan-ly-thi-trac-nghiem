<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Http; 
use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

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
        $user = auth()->user();

    // Kiểm tra quyền người dùng
    if (!$user->hasRole('SCHOOLBOARD') && !$user->hasRole('TEACHER')) {
        return response()->json(['error' => 'Bạn không có quyền tạo kỳ thi'], 403);
    }

    // Lấy danh sách môn học của giáo viên nếu người dùng là giáo viên
    $teacherSubjects = [];
    if ($user->hasRole('TEACHER')) {
        $teacherSubjects = $user->teacher->subjects->pluck('id')->toArray();
    }

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

    // Kiểm tra tổng số câu hỏi cho từng môn học
    foreach ($validated['subjects'] as $subject) {
        $totalQuestions = $subject['Qtype1'] + $subject['Qtype2'] + $subject['Qtype3'];
        if ($totalQuestions != $subject['Qnumber']) {
            return response()->json(['error' => 'Tổng số câu hỏi không khớp với số lượng câu hỏi đã chỉ định cho môn học: ' . $subject['id']], 422);
        }
    }

    // Nếu người dùng là giáo viên, chỉ cho phép chọn môn học mà họ dạy
    foreach ($validated['subjects'] as $subject) {
        if ($user->hasRole('TEACHER') && !in_array($subject['id'], $teacherSubjects)) {
            return response()->json(['error' => 'Bạn không dạy môn học này, không thể thêm vào kỳ thi.'], 403);
        }
    }

    // Tạo kỳ thi
    $exam = Exam::create([
        'name' => $validated['name'],
        'school_board_id' => $validated['school_board_id'],
        'examtype' => $validated['examtype'],
    ]);

    // Liên kết môn học với thời gian làm bài
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
        'subjects' => $exam->subjects()->withPivot('time', 'Qtype1', 'Qtype2', 'Qtype3', 'Qnumber')->get(),
        'teachers' => $exam->teachers()->distinct()->get(), // Sử dụng distinct() để tránh trùng lặp
    ], 201);
    }
    // danh sách bài thi dành cho sinh viên
    public function getExamsForStudent($id)
{
    $student = Student::findOrFail($id);

    // Lấy danh sách lớp học mà sinh viên tham gia
    $classrooms = $student->classes()->pluck('classes.id')->toArray(); 

    // Lấy danh sách kỳ thi liên quan đến các lớp học mà sinh viên tham gia
    $exams = Exam::whereHas('classrooms', function ($query) use ($classrooms) {
        $query->whereIn('classes.id', $classrooms); 
    })
    ->with(['classrooms.subject']) // Eager load môn học cho lớp học
    ->get();

    // Nhóm dữ liệu theo kỳ thi
    $groupedExams = $exams->groupBy('name')->map(function ($examGroup, $examName) use ($classrooms) {
        return [
            'name' => $examName, // Tên kỳ thi
            'exams' => $examGroup->flatMap(function ($exam) use ($classrooms) {
                return $exam->classrooms->filter(function ($classroom) use ($classrooms) {
                    return in_array($classroom->id, $classrooms); // Dùng toArray() để chuyển thành mảng
                })->map(function ($classroom) use ($exam) {
                    // Truy vấn bảng exam_subject để lấy thời gian thi
                    $examSubject = DB::table('exam_subject')
                        ->where('exam_id', $exam->id)
                        ->where('subject_id', $classroom->subject_id)
                        ->first(); // Lấy một bản ghi đầu tiên

                    return [
                        'exam_name' => $classroom->name, // Tên lớp học
                        'subject_name' => $classroom->subject->name ?? 'Không xác định', // Tên môn học
                        'exam_time' => $examSubject->time ?? 'Chưa có thời gian', // Thời gian thi của môn
                    ];
                });
            }),
        ];
    })->values();

    return response()->json([
        'exams' => $groupedExams,
    ]);
}

/**
     * Lấy thông tin kỳ thi.
     */

    public function showExam($id)
    {
        // Lấy kỳ thi với các thông tin liên quan (môn học, giáo viên, câu hỏi)
    $exam = Exam::with(['subjects', 'subjects.teachers', 'questions.subject', 'schoolBoard'])->findOrFail($id);

    // Lấy thông tin môn học, thời gian và giáo viên dạy môn học
    $subjectsWithDetails = $exam->subjects->map(function ($subject) {
        // Lấy số lượng câu hỏi Qtype từ bảng pivot
        $Qtype1 = $subject->pivot->Qtype1;
        $Qtype2 = $subject->pivot->Qtype2;
        $Qtype3 = $subject->pivot->Qtype3;
        
        // Lấy thời gian từ bảng pivot
        $time = $subject->pivot->time ?? 'Chưa có thời gian';
        
        // Lấy danh sách câu hỏi Qtype1 cho môn học
        $questionsQtype1 = $subject->questions()->where('level', 'Nhận biết')->take($Qtype1)->get();
        $questionsQtype2 = $subject->questions()->where('level', 'Thông hiểu')->take($Qtype2)->get();
        $questionsQtype3 = $subject->questions()->where('level', 'Vận dụng')->take($Qtype3)->get();

        return [
            'subject_name' => $subject->name,
            'time' => $time,  // Trả về thời gian từ pivot
            'Qtype1_count' => $Qtype1,  
            'questions_Qtype1' => $questionsQtype1,
            'Qtype2_count' => $Qtype2,  
            'questions_Qtype2' => $questionsQtype2,
            'Qtype3_count' => $Qtype3,  
            'questions_Qtype3' => $questionsQtype3, 
            'Qnumber' => $subject->pivot->Qnumber,  // Trả về tổng số câu hỏi
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
    public function submitExam(Request $request, $examId)
{
    $validated = $request->validate([
        'student_id' => 'required|exists:students,id',
        'class_id' => 'required|exists:classes,id',
        'subject_id' => 'required|exists:subjects,id',
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|exists:questions,id',
        'answers.*.answer' => 'required|string',
    ]);

    // Lấy thông tin kỳ thi và môn học
    $exam = Exam::findOrFail($examId);
    $subjectId = $validated['subject_id'];

    // Lấy thông tin bài thi thuộc kỳ thi và môn học
    $examSubject = DB::table('exam_subject')
        ->where('exam_id', $examId)
        ->where('subject_id', $subjectId)
        ->first();

    if (!$examSubject) {
        return response()->json([
            'message' => 'Không có bài thi cho môn học này trong kỳ thi.',
        ], 400);
    }

    // Kiểm tra nếu sinh viên đã nộp bài cho bài thi này rồi
    $existingResult = Result::where('exam_subject_id', $examSubject->id)
        ->where('student_id', $validated['student_id'])
        ->first();

    if ($existingResult) {
        return response()->json([
            'message' => 'Bạn đã nộp bài thi này rồi, không thể nộp lại.',
        ], 400);
    }

    // Kiểm tra thời gian làm bài
    $startTime = now(); // Thời gian hiện tại
    $examStartTime = $examSubject->pivot->start_time ?? $startTime;
    $examTimeLimit = $examSubject->time; // Thời gian làm bài (minutes)

    // Tính thời gian còn lại (chưa hết thời gian)
    $timeElapsed = $startTime->diffInMinutes($examStartTime, false);

    if ($timeElapsed > $examTimeLimit) {
        // Nếu thời gian đã hết, tự động nộp bài
        $this->autoSubmitExam($examSubject, $validated['student_id'], $validated['answers']);
        return response()->json([
            'message' => 'Thời gian làm bài đã hết. Hệ thống tự động nộp bài!',
        ], 200);
    }

    // Tiến hành tính điểm và lưu kết quả
    $this->calculateAndSubmitResult($examSubject, $validated, $exam);
}

protected function calculateAndSubmitResult($examSubject, $validated, $exam)
{
    $examQuestions = DB::table('exam_question')
        ->where('exam_id', $exam->id)
        ->where('subject_id', $validated['subject_id'])
        ->pluck('question_id');

    $totalQuestions = $examQuestions->count();
    $correctAnswers = 0;

    foreach ($validated['answers'] as $answer) {
        if (!$examQuestions->contains($answer['question_id'])) {
            return response()->json([
                'message' => 'Câu hỏi không thuộc bài thi này.',
            ], 403);
        }

        $question = Question::find($answer['question_id']);
        if ($question && strtolower($question->rightanswer) === strtolower($answer['answer'])) {
            $correctAnswers++;
        }
    }

    // Tính điểm (chỉ chấm điểm câu hỏi thuộc bài thi)
    $score = ($correctAnswers / $totalQuestions) * 10;
    $score = round($score, 2);

    // Lưu kết quả vào bảng Result
    Result::create([
        'exam_subject_id' => $examSubject->id,
        'exam_id' => $exam->id,
        'student_id' => $validated['student_id'],
        'subject_id' => $validated['subject_id'],
        'score' => $score,
    ]);
}

protected function autoSubmitExam($examSubject, $studentId, $answers)
{
    // Logic tự động tính điểm và nộp bài nếu hết thời gian
    $correctAnswers = 0;
    $totalQuestions = count($answers);

    foreach ($answers as $answer) {
        $question = Question::find($answer['question_id']);
        if ($question && strtolower($question->rightanswer) === strtolower($answer['answer'])) {
            $correctAnswers++;
        }
    }

    // Tính điểm và lưu kết quả
    $score = ($correctAnswers / $totalQuestions) * 10;
    $score = round($score, 2);

    // Lưu kết quả vào bảng Result
    Result::create([
        'exam_subject_id' => $examSubject->id,
        'exam_id' => $examSubject->exam_id,
        'student_id' => $studentId,
        'subject_id' => $examSubject->subject_id,
        'score' => $score,
    ]);
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
// Lấy chi tiết bài thi cho sinh viên để làm bài
public function startExam($examId,$studentId)
{
    $exam = Exam::with(['subjects'])->findOrFail($examId);
    $student = Student::findOrFail($studentId);

    // Kiểm tra xem sinh viên có thuộc lớp học liên quan đến kỳ thi không
    $classrooms = $student->classes()->pluck('classes.id');
    $examClassrooms = DB::table('class_exam')
        ->where('exam_id', $examId)
        ->pluck('class_id');

    // Lọc các lớp học hợp lệ
    $validClassrooms = $classrooms->intersect($examClassrooms);

    if ($validClassrooms->isEmpty()) {
        return response()->json([
            'message' => 'Sinh viên không thuộc lớp học nào trong kỳ thi này.',
        ], 400);
    }

    // Lấy danh sách câu hỏi liên kết với kỳ thi từ bảng exam_subject
    $examSubjects = DB::table('exam_subject')
        ->where('exam_id', $examId)
        ->get();

    // Map các môn học và câu hỏi
    $examDetails = $examSubjects->map(function ($examSubject) {
        // Lấy tên môn học từ bảng 'subjects'
        $subject = Subject::findOrFail($examSubject->subject_id);

        // Lấy danh sách câu hỏi từ môn học
        $questions = Question::where('subject_id', $examSubject->subject_id)
            ->take($examSubject->Qnumber) // Giới hạn số câu hỏi theo Qnumber
            ->get()
            ->map(function ($question, $index) {
                return [
                    'question_id' => $question->id,
                    'question_number' => $index + 1, // Đánh số thứ tự
                    'question_text' => $question->question,
                    'level' => $question->level,
                    'answer_a' => $question->answer_a,
                    'answer_b' => $question->answer_b,
                    'answer_c' => $question->answer_c,
                    'answer_d' => $question->answer_d,
                ];
            });

        return [
            'subject_name' => $subject->name,
            'time_limit' => $examSubject->time, // Thời gian làm bài của môn học
            'questions' => $questions,
        ];
    });

    return response()->json([
        'exam_id' => $exam->id,
        'exam_name' => $exam->name,
        'subjects' => $examDetails,
    ]);
}
}
