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
        return $this->hasMany(Subject::class, 'teacher_id', 'teacher_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'teacher_id', 'teacher_id');
    }
}
