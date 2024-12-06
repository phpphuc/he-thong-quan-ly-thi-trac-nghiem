<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class QuestionController extends Controller
{
    // Lấy danh sách câu hỏi
    public function index()
    {
        return Question::all();
    }
// Thêm mới câu hỏi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'questionid' => 'required|integer|unique:questions,questionid',
            'subjectid' => 'required|string',
            'teacherid' => 'required|integer',
            'question' => 'required|string',
            'level' => 'required|string|in:EASY,NORMAL,HARD',
            'rightanswer' => 'required|string',
            'answer_a' => 'required|string',
            'answer_b' => 'required|string',
            'answer_c' => 'required|string',
            'answer_d' => 'required|string',
        ]);

        $question = Question::create($validated);

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
        'subjectid' => 'sometimes|string',
        'teacherid' => 'sometimes|integer',
        'question' => 'sometimes|string',
        'level' => 'sometimes|string|in:EASY,NORMAL,HARD',
        'rightanswer' => 'sometimes|string',
        'answer_a' => 'sometimes|string',
        'answer_b' => 'sometimes|string',
        'answer_c' => 'sometimes|string',
        'answer_d' => 'sometimes|string',
    ]);

    $question->update($validated);

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
