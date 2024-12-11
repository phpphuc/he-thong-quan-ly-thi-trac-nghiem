<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $table = 'students'; 
    protected $primaryKey = 'student_id'; 
    protected $fillable = [
        'student_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Classroom::class, 'class_student', 'student_id', 'class_id');
    }
}
