<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolBoard extends Model
{
    use HasFactory;
    protected $table = 'school_boards'; 
    protected $primaryKey = 'school_board_id'; 

    protected $fillable = [
        'school_board_id',
        'user_id',  
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
