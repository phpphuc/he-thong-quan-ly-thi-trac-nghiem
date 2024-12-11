<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Class_Student extends Model
{
    use HasFactory;
    protected $table = 'class_student';
    protected $fillable = [
        'student_id',
        'class_id',
    ];
}
