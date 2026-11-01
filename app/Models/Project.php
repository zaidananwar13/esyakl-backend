<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $primaryKey = "id_project";
    protected $table = "project";
    protected $fillable = [
        'id_class', 'title', 'desc', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
}
