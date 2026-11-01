<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FEAuthorizer extends Model
{
    use HasFactory;

    protected $table = 'feauthorizer';
    protected $primaryKey = 'id_authorizer';
    protected $fillable = [
        'id_user', 'id_kelas', 'unlocked', 'created_at', 'updated_at'
    ];
    // protected $dateFormat = 'U';
}
