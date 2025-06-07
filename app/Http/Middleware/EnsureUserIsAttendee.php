<?php

namespace App\Http\Middleware;

use App\Models\Attendee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAttendee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');
        $user = $request->user();

        $attendee = Attendee::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if (! $attendee) {
            return response()->json([
                'message' => 'You are not registered as an attendee for this event.',
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        $request->attributes->set('attendee', $attendee);

        return $next($request);
    }
}
