<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/posts', function () {
//     return response()->json([
//         'posts' => [
//             ['id' => 1, 'title' => 'Post 1'],
//             ['id' => 2, 'title' => 'Post 2'],
//             ['id' => 3, 'title' => 'Post 3'],
//             ['id' => 4, 'title' => 'Post 4'],
//             ['id' => 5, 'title' => 'Post 5'],
//         ]
//     ]);
// });

// Route::apiResource('posts', 'PostController');


Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/register', [App\Http\Controllers\API\V1\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\API\V1\AuthController::class, 'login']);
});
