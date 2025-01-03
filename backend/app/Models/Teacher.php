<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'teacher_id', 'subject_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_teacher', 'teacher_id', 'exam_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Classroom::class, 'class_teacher', 'teacher_id', 'class_id');
    }
    public function questions()
    {
    return $this->hasMany(Question::class, 'teacher_id', 'id');
    }
}
