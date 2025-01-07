<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Exam;
use App\Models\Student;

class ResultController extends Controller
{
    // Hiển thị danh sách kết quả thi.
    public function index()
    {
        $results = Result::with(['exam', 'student', 'examSubject'])->get();
        return response()->json($results);
    }

    //Hiển thị chi tiết kết quả của một bài thi.
    public function show($id)
    {
        // Lấy kết quả bài thi với các mối quan hệ liên quan
    $result = Result::with(['exam', 'examSubject'])->find($id);

    if (!$result) {
        return response()->json(['message' => 'Result not found'], 404);
    }

    // Lấy thông tin môn học từ bảng subjects thông qua exam_subject
    $examSubject = $result->examSubject;
    $subject = Subject::find($examSubject->subject_id);

    if (!$subject) {
        return response()->json(['message' => 'Subject not found'], 404);
    }

    // Định dạng lại dữ liệu trả về cho frontend
    $formattedResult = [
        'test_name' => $result->exam->name, // Tên bài thi từ bảng exam
        'subject' => $subject->name, // Lấy tên môn học từ bảng subjects
        'create_at' => $result->created_at->format('Y-m-d H:i:s'), // Định dạng ngày tạo
        'type' => $result->exam->type, // Loại bài thi từ bảng exam
        'result' => $result->score, // Điểm số của bài thi
        'notes' => $result->notes, // Ghi chú (nếu có)
    ];

    return response()->json($formattedResult);
    }

    // Lưu kết quả bài thi.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'exam_subject_id' => 'required|exists:exam_subject,id',
            'student_id' => 'required|exists:students,id',
            'score' => 'required|numeric|min:0',
            // Optionally, if start_time is available in the request, add it here
            'start_time' => 'nullable|date', // Validate if start_time is passed
            'notes' => 'nullable|string',
        ]);

        // Include start_time if provided, otherwise default to now
        $validated['start_time'] = $validated['start_time'] ?? now();

        $result = Result::create($validated);

        return response()->json([
            'message' => 'Result created successfully',
            'result' => $result
        ], 201);
    }

    // Cập nhật kết quả bài thi.
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'score' => 'nullable|numeric|min:0',
            'start_time' => 'nullable|date',  // Allow updating start_time
            'notes' => 'nullable|string',
        ]);

        $result = Result::find($id);

        if (!$result) {
            return response()->json(['message' => 'Result not found'], 404);
        }

        $result->update($validated);

        return response()->json([
            'message' => 'Result updated successfully',
            'result' => $result
        ]);
    }

    // Xem lịch sử kết quả của một sinh viên.
    public function studentHistory($studentId)
    {
        $results = Result::with(['examSubject.subject', 'exam'])
            ->where('student_id', $studentId)
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found for this student'], 404);
        }

        return response()->json($results);
    }

    // Tạo báo cáo kết quả kỳ thi.
    public function examReport($examId)
    {
        $results = Result::with('student')
            ->where('exam_id', $examId)
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found for this exam'], 404);
        }

        $averageScore = $results->avg('score');
        $highestScore = $results->max('score');
        $lowestScore = $results->min('score');

        return response()->json([
            'exam_id' => $examId,
            'total_results' => $results->count(),
            'average_score' => $averageScore,
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,
            'results' => $results
        ]);
    }
}
