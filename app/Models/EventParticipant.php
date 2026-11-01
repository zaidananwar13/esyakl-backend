<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory;

    protected $table = 'event_participant';
    protected $primaryKey = 'id_event_participant';
    protected $dateFormat = 'U';
}
