<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;
    protected $primaryKey = "id_promo";
    protected $table = "promo";
    protected $fillable = [
        'code', 'percentage', 'value', 'expired_at', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
}
