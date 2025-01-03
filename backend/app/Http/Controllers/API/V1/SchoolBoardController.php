<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\SchoolBoard;
use App\Models\Exam;

class SchoolBoardController extends Controller
{
    // Hiển thị danh sách tất cả các thành viên Ban giám hiệu
    public function index()
    {
        $schoolBoards = SchoolBoard::all();
        return response()->json($schoolBoards, 200);
    }

    // Xem thông tin chi tiết một thành viên Ban giám hiệu
    public function show($id)
    {
        $schoolBoard = SchoolBoard::find($id);

        if (!$schoolBoard) {
            return response()->json(['error' => 'SchoolBoard not found'], 404);
        }

        return response()->json($schoolBoard, 200);
    }

    // Thêm mới một thành viên Ban giám hiệu
 //public function store(Request $request)
   // {
      //  $validated = $request->validate([
        //    'id' => 'required|uuid|exists:users,id',
      //  ]);

      //  $schoolBoard = SchoolBoard::create($validated);
      //  return response()->json($schoolBoard, 201);
  //  }

    // Cập nhật thông tin của một thành viên Ban giám hiệu
    public function update(Request $request, $id)
    {
        $schoolBoard = SchoolBoard::find($id);

        if (!$schoolBoard) {
            return response()->json(['error' => 'SchoolBoard not found'], 404);
        }

        $validated = $request->validate([
            'id' => 'required|uuid|exists:users,id',
        ]);

        $schoolBoard->update($validated);
        return response()->json($schoolBoard, 200);
    }

    // Xóa một thành viên Ban giám hiệu
    public function destroy($id)
    {
        $schoolBoard = SchoolBoard::find($id);

        if (!$schoolBoard) {
            return response()->json(['error' => 'SchoolBoard not found'], 404);
        }

        $schoolBoard->delete();
        return response()->json(['message' => 'SchoolBoard deleted successfully'], 200);
    }
    

    public function exams($schoolBoardId)
{
    $schoolBoard = SchoolBoard::find($schoolBoardId);

    if (!$schoolBoard) {
        return response()->json(['error' => 'SchoolBoard not found'], 404);
    }

    $exams = $schoolBoard->exams;  // Truy xuất các kỳ thi mà Ban Giám Hiệu này giám sát

    return response()->json($exams);
}

    // Tạo báo cáo kết quả kỳ thi của Ban Giám Hiệu
    public function report($schoolBoardId)
    {
        // Tìm SchoolBoard theo ID
        $schoolBoard = SchoolBoard::find($schoolBoardId);

        if (!$schoolBoard) {
            return response()->json(['error' => 'SchoolBoard not found'], 404);
        }

        // Lấy kết quả thi từ tất cả các kỳ thi mà SchoolBoard giám sát
        $results = $schoolBoard->results()->with('exam', 'student')->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found for this school board'], 404);
        }

        // Tính toán báo cáo kết quả
        $averageScore = $results->avg('score');
        $highestScore = $results->max('score');
        $lowestScore = $results->min('score');

        return response()->json([
            'school_board_id' => $schoolBoardId,
            'total_results' => $results->count(),
            'average_score' => $averageScore,
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,
            'results' => $results
        ]);
    }
}
