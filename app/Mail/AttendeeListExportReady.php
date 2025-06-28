<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\EventAttendeesReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class AttendeeListExportReady extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Event $event, public EventAttendeesReport $report) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $eventTitle = $this->event->title;
        return new Envelope(
            subject: "Your attendees report for {$eventTitle} has been generated and is attached",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event_attendees_report_created',
            with: [
                'event' => $this->event,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $absolutePath = Storage::disk('public')->path($this->report->file_path);
        return [
            Attachment::fromPath($absolutePath)
                ->as($this->report->file_name)
                ->withMime('text/csv'),
        ];
    }
}
