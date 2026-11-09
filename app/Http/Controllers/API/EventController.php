<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EventService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Event';

    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index(Request $request, $id = null)
    {
        $userId = null;
        if ($request->input('api_token') !== null) {
            $user = User::where('api_token', $request->input('api_token'))->first();
            $userId = $user ? $user->id_user : null;
        }

        if ($id === null) {
            $events = $this->eventService->getActiveEvents($userId);

            if (count($events) > 0) {
                return $this->successResponse(self::API_NAME, $events, 'Success!');
            }

            // Same as the old API: HTTP 200 without a data key when there are no events.
            return response([
                'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | ' . self::API_NAME . ' API',
                'code' => 200,
                'message' => 'Not Found, You have nothing achieved yet.',
            ], 200);
        }

        if (!is_numeric($id) || $id <= 0) {
            return $this->validationErrorResponse(self::API_NAME, ['Invalid event ID']);
        }

        $event = $this->eventService->getEventById((int) $id, $userId);

        if ($event === null) {
            return $this->notFoundResponse(self::API_NAME, 'Event not found');
        }

        return $this->successResponse(self::API_NAME, $event, 'Success!');
    }

    public function history(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        $history = $this->eventService->getUserEventHistory($user->id_user);

        if (count($history) > 0) {
            return $this->successResponse(self::API_NAME, $history, 'Found');
        }

        return $this->successResponse(self::API_NAME, [], 'No event history found');
    }

    public function attend(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        $eventId = $request->input('event');
        if (!is_numeric($eventId) || $eventId <= 0) {
            return $this->validationErrorResponse(self::API_NAME, ['Invalid event ID']);
        }

        $result = $this->eventService->attendEvent(
            (int) $eventId,
            $user->id_user
        );

        if (!$result['success']) {
            return $this->errorResponse(self::API_NAME, $result['message'], $result['code']);
        }

        return $this->successResponse(self::API_NAME, null, $result['message']);
    }

    public function eventToday(Request $request)
    {
        $userId = null;
        if ($request->input('api_token') !== null) {
            $user = User::where('api_token', $request->input('api_token'))->first();
            $userId = $user ? $user->id_user : null;
        }

        $events = $this->eventService->getUpcomingEvents($userId);

        if (count($events) > 0) {
            return $this->successResponse(self::API_NAME, $events, 'Success!');
        }

        // Same as the old API: HTTP 200 with code 404 in the body and an empty data list.
        return response([
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | ' . self::API_NAME . ' API',
            'code' => 404,
            'message' => 'No Event Found.',
            'data' => [],
        ], 200);
    }
}
