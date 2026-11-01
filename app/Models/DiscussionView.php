<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionView extends Model
{
    use HasFactory;

    protected $table = 'discussion_view';
    protected $primaryKey = 'discussion_view';
    protected $fillable = [
        'id_user', 'id_discussion'
    ];
    // protected $dateFormat = 'U';
}
