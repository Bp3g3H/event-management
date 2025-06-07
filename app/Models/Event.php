<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'organizer_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class, 'event_id');
    }

    public function attendingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendees', 'event_id', 'user_id');
    }

    public function scopeFilterAndSort($query, array $filters)
    {
        if (!empty($filters['title'])) {
            $query->where('title', 'ILIKE', '%' . $filters['title'] . '%');
        }
        if (!empty($filters['description'])) {
            $query->where('description', 'ILIKE', '%' . $filters['description'] . '%');
        }
        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }
        if (!empty($filters['location'])) {
            $query->where('location', 'ILIKE', '%' . $filters['location'] . '%');
        }
        if (!empty($filters['organizer'])) {
            $query->whereHas('organizer', function ($q) use ($filters) {
                $q->where('name', 'ILIKE', '%' . $filters['organizer'] . '%');
            });
        }
        if (!empty($filters['organizer_id'])) {
            $query->where('organizer_id', $filters['organizer_id']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }

    public function IsOpenForCheckIn() : bool
    {
        $eventDate = new \DateTime($this->date);
        $dayBefore = (clone $eventDate)->modify('-1 day');
        $eventDate->setTime(23,59,59);
        $now = new \DateTime();

        return $now < $dayBefore || $now > $eventDate;
    }
}
