<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPathClass extends Model
{
    use HasFactory;
    
    protected $table = 'learning_path_class';
    protected $primaryKey = 'id_learning_path_class';
    protected $dateFormat = 'U';
}
