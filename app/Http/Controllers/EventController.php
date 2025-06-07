<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventIndexRequest;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResponse;
use App\Models\Event;

class EventController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index(EventIndexRequest $request)
    {
        $validated = $request->validated();

         $events = Event::with('organizer')
            ->filterAndSort($validated)
            ->paginate($validated['per_page'] ?? 15);
        return EventResponse::collection($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        $event = Event::create($request->validated());
        $event->load('organizer');
        return new EventResponse($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('organizer');
        return new EventResponse($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Event $event)
    {
        $event->update($request->validated());
        $event->load('organizer');
        return new EventResponse($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
