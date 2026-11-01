<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overview extends Model
{
    use HasFactory;
    protected $table = "overview";
    protected $primaryKey = "id";
    protected $fillable = [
        'title', 'desc'
    ];
    protected $dateFormat = 'U';
}
