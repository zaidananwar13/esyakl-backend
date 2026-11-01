<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassReview extends Model
{
    use HasFactory;

    protected $table = 'class_review';
    protected $primaryKey = 'id_class_review';

    protected $dateFormat = 'U';
}
