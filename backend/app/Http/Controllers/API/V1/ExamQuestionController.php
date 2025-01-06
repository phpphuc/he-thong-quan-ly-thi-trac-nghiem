<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamQuestion;
use Illuminate\Support\Facades\DB;

class ExamQuestionController extends Controller
{
    //Liên kết câu hỏi với kỳ thi
    public function attachQuestions(Request $request, $exam_id)
    {
        $exam = Exam::findOrFail($exam_id);

    // Kiểm tra nếu user là giáo viên
    if (!auth()->user()->hasRole('TEACHER')) {
        return response()->json(['error' => 'Bạn không có quyền thêm câu hỏi'], 403);
    }

    // Lấy danh sách các môn học mà giáo viên đang dạy
    $teacherSubjects = auth()->user()->subjects->pluck('id')->toArray();

    // Kiểm tra các môn học trong kỳ thi
    $examSubjects = $exam->subjects()->pluck('id')->toArray();

    // Xác nhận giáo viên có quyền thêm câu hỏi vào kỳ thi
    if (!array_intersect($teacherSubjects, $examSubjects)) {
        return response()->json(['error' => 'Bạn không có quyền thêm câu hỏi vào các môn học trong kỳ thi này'], 403);
    }

    $request->validate([
        'question_ids' => 'required|array',
        'question_ids.*' => 'exists:questions,id'
    ]);

    // Lấy danh sách câu hỏi cần liên kết
    $questions = Question::whereIn('id', $request->question_ids)->get();

    $invalidQuestions = [];
    $newExamQuestions = [];
    $questionsCountByType = [];

    // Khởi tạo bộ đếm số lượng câu hỏi theo loại
    foreach ($exam->subjects as $subject) {
        $questionsCountByType[$subject->id] = [
            'Qtype1' => 0,
            'Qtype2' => 0,
            'Qtype3' => 0,
        ];
    }

    foreach ($questions as $question) {
        $subjectId = $question->subject_id;

        // Kiểm tra câu hỏi thuộc môn học trong kỳ thi và giáo viên đang dạy
        if (!in_array($subjectId, $examSubjects) || !in_array($subjectId, $teacherSubjects)) {
            $invalidQuestions[] = $question->id;
            continue;
        }

        // Xác định loại câu hỏi
        $questionType = match ($question->level) {
            'Nhận biết' => 'Qtype1',
            'Thông hiểu' => 'Qtype2',
            'Vận dụng' => 'Qtype3',
            default => null,
        };

        // Tăng bộ đếm số lượng câu hỏi
        if ($questionType) {
            $questionsCountByType[$subjectId][$questionType]++;
        }

        // Kiểm tra giới hạn số lượng câu hỏi
        $subjectPivot = $exam->subjects()->where('subjects.id', $subjectId)->first()->pivot;
        if ($questionsCountByType[$subjectId][$questionType] > $subjectPivot->{$questionType}) {
            return response()->json([
                'error' => 'Số câu hỏi ' . $questionType . ' vượt quá giới hạn cho môn ' . $subjectPivot->name,
            ], 422);
        }

        // Thêm câu hỏi hợp lệ vào danh sách
        $newExamQuestions[] = [
            'exam_id' => $exam_id,
            'subject_id' => $subjectId,
            'question_id' => $question->id,
        ];
    }

    // Xử lý câu hỏi không hợp lệ
    if (!empty($invalidQuestions)) {
        return response()->json([
            'error' => 'Một hoặc nhiều câu hỏi không hợp lệ.',
            'invalid_question_ids' => $invalidQuestions,
        ], 422);
    }

    // Lưu vào bảng exam_question
    foreach ($newExamQuestions as $examQuestion) {
        ExamQuestion::create($examQuestion);
    }

    return response()->json([
        'message' => 'Câu hỏi đã được liên kết thành công!',
        'exam_id' => $exam_id,
        'questions_linked' => $newExamQuestions,
    ]);
    }

    // Xem các câu hỏi đã được liên kết với kỳ thi
    public function showQuestions($exam_id)
    {
        $exam = Exam::findOrFail($exam_id);

        // Lấy thông tin kỳ thi và các câu hỏi đã liên kết
        $subjectsWithQuestions = $exam->subjects->map(function ($subject) {
            // Lấy số lượng câu hỏi Qtype từ bảng pivot
        $Qtype1 = $subject->pivot->Qtype1;
        $Qtype2 = $subject->pivot->Qtype2;
        $Qtype3 = $subject->pivot->Qtype3;

        // Lấy danh sách câu hỏi Qtype cho môn học
        $questionsQtype1 = $subject->questions()->where('level', 'Nhận biết')->take($Qtype1)->get();
        $questionsQtype2 = $subject->questions()->where('level', 'Thông hiểu')->take($Qtype2)->get();
        $questionsQtype3 = $subject->questions()->where('level', 'Vận dụng')->take($Qtype3)->get();
            return [
                'subject_name' => $subject->name,
                'Qtype1_count' => $Qtype1,  
                'questions_Qtype1' => $questionsQtype1,
                'Qtype2_count' => $Qtype2,  
                'questions_Qtype2' => $questionsQtype2,
                'Qtype3_count' => $Qtype3,  
                'questions_Qtype3' => $questionsQtype3,
            ];
        });

        return response()->json([
            'exam' => $exam->name,
            'subjects_with_questions' => $subjectsWithQuestions
        ]);
    }

    // Xóa liên kết câu hỏi khỏi kỳ thi
    public function detachQuestions(Request $request, $exam_id)
    {
        // Kiểm tra đầu vào
    $request->validate([
        'question_ids' => 'required|array',
        'question_ids.*' => 'exists:questions,id'
    ]);

    // Xóa liên kết câu hỏi với kỳ thi
    ExamQuestion::where('exam_id', $exam_id)
        ->whereIn('question_id', $request->question_ids)
        ->delete();

    return response()->json([
        'message' => 'Liên kết giữa các câu hỏi và kỳ thi đã được xóa thành công!'
    ]);
    }
}
