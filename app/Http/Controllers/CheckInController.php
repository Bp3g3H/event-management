<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckInController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Event $event)
    {
        $attendee = $request->attributes->get('attendee');
        
        if ($event->IsOpenForCheckIn()) {
            return response()->json([
                'message' => 'Check-in is only allowed one day before and on the day of the event.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($attendee->check_in) {
            return response()->json([
                'message' => 'You have already checked in for this event.'
            ], Response::HTTP_CONFLICT);
        }

        $attendee->checkIn();

        return response()->json([
            'message' => "You have Check-in successful for event: {$event->title}",
        ]);
    }
}
