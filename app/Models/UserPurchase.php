<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPurchase extends Model
{
    use HasFactory;
    protected $table = 'user_purchase';
    protected $primaryKey = 'id_user_purchase';
    protected $dateFormat = 'U';
    protected $fillable = [
        'id_user_purchase', 'id_user', 'id_class', 'id_promo', 'payment_method', 'value', 'expired_at', 'created_at', 'updated_at'
    ];
}
