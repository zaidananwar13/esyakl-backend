<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Migrasi extends Model
{
    use HasFactory;
    protected $table = "migrasi";
    protected $fillable = [
        'mode'
    ];
    protected $dateFormat = 'U';
}
