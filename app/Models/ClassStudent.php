<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    use HasFactory;
    protected $table = 'class_student';
    protected $primaryKey = 'id_class_student';
    protected $fillable = [
        'id_user',
        'id_class',
        'completed_at',
        'expired_at',
        'created_at',
        'updated_at'
    ];
    protected $dateFormat = 'U';
}
