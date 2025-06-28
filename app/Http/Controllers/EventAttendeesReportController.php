<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventAttendeesReportStoreRequest;
use App\Jobs\ExportAttendeeList;
use App\Models\Event;
use App\Models\EventAttendeesReport;
use Illuminate\Support\Facades\Log;

class EventAttendeesReportController extends Controller
{
    public function store(EventAttendeesReportStoreRequest $request, Event $event) 
    {
        $validated = $request->validated();
        $validated['event_id'] = $event->id;
        $report = EventAttendeesReport::create($validated);
        Log::debug('MyJob started2');
        ExportAttendeeList::dispatch($report);
        
        return response()->json([
            'message' => "A new attendee report is being generated for the event { $event->title } When the report is ready, you will receive an email with the report attached."]);
    }

    public function download() 
    {

    }
}
