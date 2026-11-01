<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLogActivity extends Model
{
    use HasFactory;

    protected $table = 'student_log_activity';
    protected $primaryKey = 'id_student_log_activity';
    protected $fillable = [
        'id_student',
        'last_class_access',
        'access_type',
        'information_detail',
    ];
    protected $dateFormat = 'U';
}
