<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id', 'id');
    }
    public function classes()
    {
        return $this->hasMany(Classroom::class, 'subject_id', 'id');
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_subject', 'subject_id', 'exam_id');
    }
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject', 'subject_id', 'teacher_id');
    }
}
