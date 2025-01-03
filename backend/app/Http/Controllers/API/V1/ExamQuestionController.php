<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class ExamQuestionController extends Controller
{
    //Liên kết câu hỏi với kỳ thi
    public function attachQuestions(Request $request, $exam_id)
    {
        $exam = Exam::findOrFail($exam_id);

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);
    
        // Lấy danh sách ID các môn học liên kết với kỳ thi và số lượng câu hỏi của mỗi loại
        $subjects = $exam->subjects()->withPivot('Qtype1', 'Qtype2', 'Qtype3', 'Qnumber')->get();
    
        // Tạo một mảng để theo dõi số lượng câu hỏi đã gán cho từng loại câu hỏi của mỗi môn học
        $questionsCountByType = [];
    
        foreach ($subjects as $subject) {
            // Khởi tạo các loại câu hỏi
            $questionsCountByType[$subject->id] = [
                'Qtype1' => 0,
                'Qtype2' => 0,
                'Qtype3' => 0,
            ];
        }
    
        // Lấy danh sách câu hỏi cần liên kết
        $questions = Question::whereIn('id', $request->question_ids)->get();
    
        // Kiểm tra xem câu hỏi có thuộc về các môn học liên quan không và đếm số lượng theo loại
        $invalidQuestions = [];
        foreach ($questions as $question) {
            if (!in_array($question->subject_id, $subjects->pluck('id')->toArray())) {
                $invalidQuestions[] = $question->id;
            } else {
                // Tăng số lượng câu hỏi cho loại phù hợp
                $subjectId = $question->subject_id;
                if ($question->level == 'Nhận biết') {
                    $questionsCountByType[$subjectId]['Qtype1']++;
                } elseif ($question->level == 'Thông hiểu') {
                    $questionsCountByType[$subjectId]['Qtype2']++;
                } elseif ($question->level == 'Vận dụng') {
                    $questionsCountByType[$subjectId]['Qtype3']++;
                }
            }
        }
    
        // Kiểm tra số lượng câu hỏi cho mỗi loại không vượt quá giới hạn
        foreach ($subjects as $subject) {
            $subjectId = $subject->id;
            if ($questionsCountByType[$subjectId]['Qtype1'] > $subject->pivot->Qtype1) {
                return response()->json([
                    'error' => 'Số câu hỏi Qtype1 vượt quá số lượng cho môn học: ' . $subject->name
                ], 422);
            }
            if ($questionsCountByType[$subjectId]['Qtype2'] > $subject->pivot->Qtype2) {
                return response()->json([
                    'error' => 'Số câu hỏi Qtype2 vượt quá số lượng cho môn học: ' . $subject->name
                ], 422);
            }
            if ($questionsCountByType[$subjectId]['Qtype3'] > $subject->pivot->Qtype3) {
                return response()->json([
                    'error' => 'Số câu hỏi Qtype3 vượt quá số lượng cho môn học: ' . $subject->name
                ], 422);
            }
        }
    
        // Nếu có câu hỏi không hợp lệ, trả về lỗi
        if (!empty($invalidQuestions)) {
            return response()->json([
                'error' => 'Một hoặc nhiều câu hỏi không thuộc về các môn học liên quan đến kỳ thi.',
                'invalid_question_ids' => $invalidQuestions,
            ], 422);
        }
    
        // Liên kết các câu hỏi với kỳ thi
        $exam->questions()->sync($request->question_ids);
    
        // Trả về thông tin kỳ thi và các câu hỏi theo môn học
        return response()->json([
            'message' => 'Câu hỏi đã được liên kết với kỳ thi thành công!',
            'exam' => $exam,
            'subjects_with_questions' => $exam->subjects->map(function ($subject) {
                return [
                    'subject_name' => $subject->name,
                    'questions' => $subject->questions->map(function ($question) {
                        return [
                            'question_id' => $question->id,
                            'question_text' => $question->question,
                            'level' => $question->level,
                            'answer_a' => $question->answer_a,
                            'answer_b' => $question->answer_b,
                            'answer_c' => $question->answer_c,
                            'answer_d' => $question->answer_d,
                            'rightanswer' => $question->rightanswer,
                        ];
                    })
                ];
            })
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
        $exam = Exam::findOrFail($exam_id);

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);
    
        // Lấy danh sách ID các môn học liên kết với kỳ thi
        $subjectIds = $exam->subjects()->pluck('subjects.id')->toArray();
    
        // Lấy danh sách câu hỏi cần xóa
        $questions = Question::whereIn('id', $request->question_ids)->get();
    
        // Kiểm tra xem câu hỏi có thuộc về các môn học liên quan không
        $invalidQuestions = $questions->filter(function ($question) use ($subjectIds) {
            return !in_array($question->subject_id, $subjectIds);
        });
    
        if ($invalidQuestions->isNotEmpty()) {
            return response()->json([
                'error' => 'Một hoặc nhiều câu hỏi không thuộc về các môn học liên quan đến kỳ thi.',
                'invalid_question_ids' => $invalidQuestions->pluck('id')->toArray(),
            ], 422);
        }
    
        // Sử dụng giao dịch để đảm bảo tính toàn vẹn
        DB::transaction(function () use ($exam, $request) {
            $exam->questions()->detach($request->question_ids);
        });
    
        return response()->json([
            'message' => 'Câu hỏi đã được xoá khỏi kỳ thi!'
        ]);
    }
}
