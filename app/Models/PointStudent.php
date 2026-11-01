<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointStudent extends Model
{
    use HasFactory;
    protected $table = "point_student";
    protected $primaryKey = "id_point_student";
    protected $fillable = [
        'id_user', 'id_class', 'points'
    ];
    protected $dateFormat = 'U';
}
