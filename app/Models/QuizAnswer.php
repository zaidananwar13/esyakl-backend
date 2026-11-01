<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;
    protected $primaryKey = "id_quiz_answer";
    protected $table = "quiz_answer";
    protected $fillable = [
        'id_quiz_student', 'id_question', 'id_option', 'answer', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
}
