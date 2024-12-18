<?php

namespace App\Http\Controllers;

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
     public function store(Request $request)
     {
         $validated = $request->validate([
             'id' => 'required|uuid|exists:users,id',
         ]);
 
         $schoolBoard = SchoolBoard::create($validated);
         return response()->json($schoolBoard, 201);
     }
 
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

}
