<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classes';
    // protected $primaryKey = 'class_id';

    protected $fillable = [
        'name',
        'subject_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id');
    }
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_teacher', 'class_id', 'teacher_id');
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'class_exam', 'class_id', 'exam_id');
    }
}
