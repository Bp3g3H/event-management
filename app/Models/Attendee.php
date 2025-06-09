<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'rsvp_status',
        'check_in',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeFilterAndSort($query, array $filters, $userId = null)
    {
        $eventIsLoaded = false;
        if (!empty($filters['include'])) {
            $query->with($filters['include']);
            $eventIsLoaded = true;
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        if (!$eventIsLoaded && $this->shouldLoadEvent($filters)) {
            $query->with(['event']);

            if ($this->shouldLoadEventOrganizer($filters)) {
                $query->with(['event.organizer']);
            }
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if (!empty($filters['event_id'])) {
            $query->where('event_id', $filters['event_id']);
        }

        if (!empty($filters['event_title'])) {
            $query->whereHas('event', function ($q) use ($filters) {
                $q->where('title', 'ILIKE', '%'.$filters['event_title'].'%');
            });
        }
        if (!empty($filters['organizer'])) {
            $query->whereHas('event.organizer', function ($q) use ($filters) {
                $q->where('name', 'ILIKE', '%'.$filters['organizer'].'%');
            });
        }
        if (!empty($filters['organizer_id'])) {
            $query->whereHas('event', function ($q) use ($filters) {
                $q->where('organizer_id', $filters['organizer_id']);
            });
        }
        if (!empty($filters['rsvp_status'])) {
            $query->where('rsvp_status', $filters['rsvp_status']);
        }

        if ($sortBy === 'event_title') {
            $query->join('events', 'attendees.event_id', '=', 'events.id')
                ->orderBy('events.title', $sortOrder)
                ->select('attendees.*');
        } elseif ($sortBy === 'organizer_name') {
            $query->join('events', 'attendees.event_id', '=', 'events.id')
                ->join('users as organizers', 'events.organizer_id', '=', 'organizers.id')
                ->orderBy('organizers.name', $sortOrder)
                ->select('attendees.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    public function checkIn(): void
    {
        $this->check_in = true;
        $this->save();
    }

    private function shouldLoadEvent(array $filters): bool
    {
        $sortBy = $filters['sort_by'] ?? null;
        return
            !empty($filters['event_title']) ||
            !empty($filters['organizer']) ||
            $sortBy === 'event_title' ||
            $sortBy === 'organizer_name';
    }

    private function shouldLoadEventOrganizer(array $filters): bool
    {
        $sortBy = $fillable['sort_by'] ?? null;
        return 
            !empty($filters['organizer']) ||
            $sortBy === 'organizer_name';
    }
}
