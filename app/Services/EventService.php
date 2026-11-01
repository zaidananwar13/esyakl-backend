<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Helpers\MediaHelper;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function getEventDetails(Event $event, ?int $userId = null): array
    {
        $data = $event->toArray();

        $hoster = User::where('id_user', $event->owner)->first();
        $data['hoster'] = $hoster ? $hoster->name : 'Unknown';
        $data['image'] = MediaHelper::getMediaUrl($event->image);
        $data['type'] = $event->status;

        $endDate = Carbon::parse($event->end)->addHours(
            config('app-settings.timezone.display_offset_hours', 7)
        );
        $data['status'] = $endDate->lt(Carbon::now()) ? 'End' : 'On-Going';

        $data['start'] = DateHelper::formatDateTime($event->start);
        $data['end'] = DateHelper::formatDateTime($event->end);

        if ($userId) {
            $data['user_status'] = $this->getUserEventStatus($event, $userId, $data['status']);
        }

        unset($data['id_bahasa']);
        unset($data['created_at']);
        unset($data['updated_at']);

        return $data;
    }

    public function getUserEventStatus(Event $event, int $userId, string $eventStatus): string
    {
        $participation = EventParticipant::where('id_event', $event->id_event)
            ->where('participant', $userId)
            ->first();

        if ($eventStatus === 'End') {
            return $participation ? 'Completed' : 'Unavailable';
        }

        return $participation ? 'Taken' : 'Available';
    }

    public function getActiveEvents(?int $userId = null): array
    {
        $events = Event::where('end', '>=', Carbon::now()->format('U'))->get();

        $result = [];
        foreach ($events as $event) {
            $result[] = $this->getEventDetails($event, $userId);
        }

        return $result;
    }

    public function getUpcomingEvents(?int $userId = null): array
    {
        $today = Carbon::parse(Carbon::now()->format('Y-m-d'))->format('U');
        $threeDaysLater = Carbon::parse(Carbon::now()->addDays(3)->format('Y-m-d'))->format('U');

        $events = Event::where('start', '<=', $threeDaysLater)
            ->where('end', '>=', $today)
            ->get();

        $result = [];
        foreach ($events as $event) {
            $result[] = $this->getEventDetails($event, $userId);
        }

        return $result;
    }

    public function getUserEventHistory(int $userId): array
    {
        $participations = EventParticipant::where('participant', $userId)->get();
        
        $history = [];
        $now = Carbon::now()->format('Y-m-d H:i:s');

        foreach ($participations as $participation) {
            $eventDate = Carbon::parse($participation->created_at)->addHours(
                config('app-settings.timezone.display_offset_hours', 8)
            );
            
            if ($eventDate->format('Y-m-d H:i:s') >= $now) {
                continue;
            }

            $event = Event::where('id_event', $participation->id_event)->first();
            if (!$event) {
                continue;
            }

            $history[] = [
                'id_participant' => $participation->id_participant,
                'id_event' => $participation->id_event,
                'title' => $event->title,
                'date' => $eventDate->format('d M Y'),
            ];
        }

        return $history;
    }

    public function attendEvent(int $eventId, int $userId): array
    {
        return DB::transaction(function () use ($eventId, $userId) {
            $event = Event::where('id_event', $eventId)
                ->lockForUpdate()
                ->first();

            if (!$event) {
                return [
                    'success' => false,
                    'code' => 404,
                    'message' => 'Event not found.'
                ];
            }

            $existing = EventParticipant::where('participant', $userId)
                ->where('id_event', $eventId)
                ->first();

            if ($existing) {
                return [
                    'success' => false,
                    'code' => 409,
                    'message' => 'Already attended.'
                ];
            }

            if ($event->quota <= 0) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Event is full.'
                ];
            }

            $participant = new EventParticipant();
            $participant->participant = $userId;
            $participant->id_event = $eventId;
            $participant->save();

            $event->decrement('quota');

            return [
                'success' => true,
                'code' => 200,
                'message' => 'Successfully registered for event'
            ];
        });
    }

    public function getEventById(int $eventId, ?int $userId = null): ?array
    {
        $event = Event::where('id_event', $eventId)->first();
        
        if (!$event) {
            return null;
        }

        return $this->getEventDetails($event, $userId);
    }
}
