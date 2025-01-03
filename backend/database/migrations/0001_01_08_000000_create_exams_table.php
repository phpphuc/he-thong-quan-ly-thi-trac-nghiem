<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('school_board_id')->nullable();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->enum('examtype', ['NORMAL', 'GENERAL EXAM']);
            $table->timestamps();
            $table->foreign('school_board_id')->references('id')->on('school_boards');
        });
        // Tạo bảng trung gian liên kết kỳ thi với môn học
        Schema::create('exam_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->integer('time');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('Qtype1')->default(0); // Số câu hỏi loại 1 (Nhận biết)
            $table->integer('Qtype2')->default(0); // Số câu hỏi loại 2 (Thông hiểu)
            $table->integer('Qtype3')->default(0); // Số câu hỏi loại 3 (Vận dụng)
            $table->integer('Qnumber')->default(0); // Tổng số câu hỏi của môn học
            $table->timestamps();
        });

        // Tạo bảng trung gian liên kết kỳ thi với giáo viên
        Schema::create('exam_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_teacher');
        Schema::dropIfExists('exam_subject');
    }
};
