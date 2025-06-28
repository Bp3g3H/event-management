<?php

namespace App\Jobs;

use App\Enums\ReportStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendeeListExportReady;
use App\Models\EventAttendeesReport;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportAttendeeList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public EventAttendeesReport $report) {}

    public function handle()
    {
        $attendees = $this->report->event->attendees()->with('user')->get();
        $timestamp = time();

        $csv = "Name,Email\n";
        foreach ($attendees as $attendee) {
            $csv .= "{$attendee->user->name},{$attendee->user->email}\n";
        }

        $path = "exports/{$this->report->event->title}_{$timestamp}.csv";
        Storage::disk('public')->put($path, $csv);

        $this->report->update([
            'status' => ReportStatus::Completed,
            'file_path' => $path,
        ]);

        Mail::to($this->report->event->organizer->email)
            ->send(new AttendeeListExportReady($this->report->event, $this->report));
    }
}