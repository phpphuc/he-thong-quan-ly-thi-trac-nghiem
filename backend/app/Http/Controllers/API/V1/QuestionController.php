<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Subject;
use App\Http\Controllers\API\V1\Controller;

class QuestionController extends Controller
{
   // Lấy danh sách câu hỏi
    public function index()
    {
        // Load câu hỏi kèm thông tin môn học
    $questions = Question::with('subject')->get();

    // Trả về câu hỏi mà không kèm thông tin môn học đầy đủ
    $questions = $questions->map(function ($question) {
        $question->subject_name = $question->subject->name;
        // Loại bỏ thông tin môn học đầy đủ
        $question->makeHidden(['subject']);
        return $question;
    });

    return response()->json(['data' => $questions], 200);
    }
    // Thêm mới câu hỏi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'question' => 'required|string',
            'level' => 'required|string|in:Nhận biết,Thông hiểu,Vận dụng',
            'rightanswer' => 'required|string',
            'answer_a' => 'required|string',
            'answer_b' => 'required|string',
            'answer_c' => 'required|string',
            'answer_d' => 'required|string',
        ]);

        // Lấy subject_name từ bảng subjects
        $subject = Subject::find($validated['subject_id']);

        if (!$subject) {
            return response()->json(['error' => 'Subject không tồn tại.'], 404);
     }

        $question = Question::create([
            'subject_id' => $validated['subject_id'],
            'subject_name' => $subject->name,
            'teacher_id' => $validated['teacher_id'],
            'question' => $validated['question'],
            'level' => $validated['level'],
            'rightanswer' => $validated['rightanswer'],
            'answer_a' => $validated['answer_a'],
            'answer_b' => $validated['answer_b'],
            'answer_c' => $validated['answer_c'],
            'answer_d' => $validated['answer_d'],
            
        ]);
        
        $question->subject_name = $subject->name;
        return response()->json(['message' => 'Question created successfully', 'data' => $question], 201);
    }
    //cập nhật câu hỏi
    public function update(Request $request, $id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $validated = $request->validate([
            'subject_id' => 'sometimes|exists:subjects,id',
            'teacher_id' => 'sometimes|exists:teachers,id',
            'question' => 'sometimes|string',
            'level' => 'sometimes|string|in:Nhận biết,Thông hiểu,Vận dụng',
            'rightanswer' => 'sometimes|string',
            'answer_a' => 'sometimes|string',
            'answer_b' => 'sometimes|string',
            'answer_c' => 'sometimes|string',
            'answer_d' => 'sometimes|string',
        ]);

        $subject = Subject::find($validated['subject_id']);

        if (!$subject) {
            return response()->json(['error' => 'Subject không tồn tại.'], 404);
     }


        $question->update([
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'question' => $validated['question'],
            'level' => $validated['level'],
            'rightanswer' => $validated['rightanswer'],
            'answer_a' => $validated['answer_a'],
            'answer_b' => $validated['answer_b'],
            'answer_c' => $validated['answer_c'],
            'answer_d' => $validated['answer_d'],
            
        ]);

        // Thêm tên môn học vào dữ liệu trả về
        //$question->subject_name = $validated['subject_name'] ?? $question->subject_name;
        $question->subject_name = isset($subject) ? $subject->name : $question->subject_name;
        return response()->json(['message' => 'Question updated successfully', 'data' => $question], 200);
    }
    //xóa câu hỏi
    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $question->delete();

        return response()->json(['message' => 'Question deleted successfully'], 200);
    }

}
