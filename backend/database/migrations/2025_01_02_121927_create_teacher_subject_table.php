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
        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade'); // liên kết với bảng teachers
            $table->foreignId('subject_id')->constrained()->onDelete('cascade'); // liên kết với bảng subjects
            $table->timestamps();
             // Đảm bảo mỗi giáo viên chỉ được liên kết với mỗi môn học một lần
            $table->unique(['subject_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subject');
    }
};
