<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\SchoolBoard;


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
        $schoolBoard = SchoolBoard::with('user')->find($id);

        if (!$schoolBoard) {
            return response()->json(['error' => 'SchoolBoard not found'], 404);
        }

        return response()->json($schoolBoard, 200);
    }

    

    // Cập nhật thông tin của một thành viên Ban giám hiệu
    public function update(Request $request, $id)
    {
         // Tìm kiếm thành viên trong ban giám hiệu
    $schoolBoard = SchoolBoard::find($id);

    if (!$schoolBoard) {
        return response()->json(['error' => 'SchoolBoard member not found'], 404);
    }

    // Kiểm tra và xác nhận dữ liệu đầu vào cho người dùng liên kết
    $validatedData = $request->validate([
        'user.name' => 'nullable|string',
        'user.email' => 'nullable|email|unique:users,email,' . $schoolBoard->user_id,
        'user.phone' => 'nullable|string',
        'user.address' => 'nullable|string',
        'user.birth_date' => 'nullable|date',
    ]);

    // Cập nhật thông tin người dùng liên kết với SchoolBoard
    if (isset($validatedData['user'])) {
        // Lấy người dùng liên kết với SchoolBoard
        $user = $schoolBoard->user;

        // Cập nhật thông tin người dùng
        $user->update($validatedData['user']);

        // Lưu thông tin người dùng sau khi cập nhật
        $user->save();
    }

    // Trả về dữ liệu sau khi cập nhật
    return response()->json(['success' => true, 'data' => $schoolBoard->load('user')], 200);
    }

    // Xóa một thành viên Ban giám hiệu
    public function destroy($id)
    {
        $schoolBoard = SchoolBoard::findOrFail($id);
    
    // Xóa kỳ thi liên kết với school board
    $schoolBoard->exams()->each(function ($exam) {
        // Xóa mối quan hệ giữa kỳ thi và môn học, giáo viên, học sinh
        $exam->subjects()->detach();
        $exam->teachers()->detach();
        $exam->classrooms()->detach();
        $exam->results()->delete();
        $exam->questions()->detach();
        
        // Xóa kỳ thi
        $exam->delete();
    });
    // Xóa giáo viên khỏi bảng users
    $schoolBoard->user()->delete();
    // Xóa school board
    $schoolBoard->delete();

    return response()->json([
        'message' => 'School Board và các kỳ thi liên quan đã được xoá thành công!',
    ]);
    }
    
    public function exams($schoolBoardId)
{
    $schoolBoard = SchoolBoard::find($schoolBoardId);

    if (!$schoolBoard) {
        return response()->json(['error' => 'SchoolBoard not found'], 404);
    }

    $exams = $schoolBoard->exams;  

    return response()->json([
        'message' => 'Danh sách các kỳ thi',
        'exams' => $exams
    ]);
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
        $results = $schoolBoard->exams()->with('results.student')->get()->pluck('results')->flatten();

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
