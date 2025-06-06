<?php

namespace App\Http\Controllers;

use App\Enums\RsvpStatus;
use App\Http\Requests\AttendeeCreateRequest;
use App\Http\Requests\AttendeeIndexRequest;
use App\Http\Requests\AttendeeUpdateRequest;
use App\Http\Resources\AttendeeResponse;
use App\Models\Attendee;
use Illuminate\Support\Facades\Auth;

class AttendeeController extends Controller
{
    public function index(AttendeeIndexRequest $request)
    {
        $validated = $request->validated();

        $attendees = Attendee::with(['user', 'event', 'event.organizer'])
            ->filterAndSort($validated, Auth::id())
            ->paginate($validated['per_page'] ?? 15);

        return AttendeeResponse::collection($attendees);
    }

    public function show(Attendee $attendee)
    {
        return new AttendeeResponse($attendee->load(['user', 'event']));
    }

    public function store(AttendeeCreateRequest $request)
    {
        $validated = $request->validated();
        $attendee = Attendee::create([
            'user_id' => Auth::id(),
            'event_id' => $validated['event_id'],
            'rsvp_status' => $validated['rsvp_status'] ?? RsvpStatus::Pending->value,
        ]);

        return new AttendeeResponse($attendee);
    }

    public function update(AttendeeUpdateRequest $request, Attendee $attendee)
    {
        $attendee->update($request->validated());
        return new AttendeeResponse($attendee);
    }

    public function destroy(Attendee $attendee)
    {
        $attendee->delete();
        return response()->json(['message' => 'Attendee deleted successfully']);
    }
}
