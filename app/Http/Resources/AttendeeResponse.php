<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendeeResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'user_id' => $this->user_id,
            'rsvp_status' => $this->rsvp_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => UserResponse::make($this->whenLoaded('user')),
            'event' => EventResponse::make($this->whenLoaded('event')),
        ];
    }
}