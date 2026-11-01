<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDiscussion extends Model
{
    use HasFactory;

    protected $table = 'class_discussion';
    protected $primaryKey = 'id_class_discussion';
    protected $fillable = ['owner' , 'id_class', 'title', 'content'];
    protected $dateFormat = 'U';
}
