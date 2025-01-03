<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'school_board_id',
        'examtype',
    ];
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'exam_subject', 'exam_id', 'subject_id');
    }
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'exam_teacher', 'exam_id', 'teacher_id');
    }
    public function results()
    {
        return $this->hasMany(Result::class, 'exam_id', 'id');
    }
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question', 'exam_id', 'question_id');
    }
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'class_exam', 'exam_id', 'class_id');
    }
    public function schoolBoard()
    {
        return $this->belongsTo(SchoolBoard::class);
    }
}
