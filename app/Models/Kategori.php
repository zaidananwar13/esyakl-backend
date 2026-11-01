<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'class_level';
    protected $primaryKey = 'id_class_level';
    protected $dateFormat = 'U';
}
