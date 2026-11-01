<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizContainer extends Model
{
    use HasFactory;
    
    protected $table = 'quiz_section';
    protected $primaryKey = 'id_quiz_section';
    protected $dateFormat = 'U';
}
