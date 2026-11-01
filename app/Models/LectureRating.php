<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureRating extends Model
{
    use HasFactory;
    
    protected $table = 'lecture_rating';
    protected $primaryKey = 'id_lecture_rating';
    // protected $dateFormat = 'U';
}
