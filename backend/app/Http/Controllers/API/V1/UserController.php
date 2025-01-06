<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\V1\Controller;
use App\Http\Resources\API\V1\UserResource;

class UserController
{
    // Lấy danh sách tất cả người dùng
    public function index()
    {
        $users = User::all();
        return response()->json(UserResource::collection($users), 200);
    }

    // Lấy thông tin chi tiết của người dùng
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return new UserResource($user);
    }

    // Cập nhật thông tin người dùng
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Kiểm tra và xác nhận dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);
    
        // Cập nhật thông tin người dùng
        $user->update($validated);
    
        // Kiểm tra lại sau khi cập nhật
        return new UserResource($user);
    }

    // Thay đổi mật khẩu người dùng
    public function changePassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        // Kiểm tra mật khẩu hiện tại có đúng không
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    // Xóa người dùng
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Nếu người dùng có vai trò đặc biệt (Teacher, Student, SchoolBoard), xóa các bản ghi liên quan
        if ($user->role == 'TEACHER') {
            $user->teacher()->delete();
        } elseif ($user->role == 'STUDENT') {
            $user->student()->delete();
        } elseif ($user->role == 'SCHOOLBOARD') {
            $user->schoolboard()->delete();
        }

        // Xóa người dùng
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
