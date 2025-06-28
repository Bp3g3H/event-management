<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendeesReport extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'event_id',
        'status',
    ];
    
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
