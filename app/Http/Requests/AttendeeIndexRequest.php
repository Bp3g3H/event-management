<?php

namespace App\Http\Requests;

use App\Enums\RsvpStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AttendeeIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => 'sometimes|integer|exists:events,id',
            'event_title' => 'sometimes|string|max:255',
            'organizer' => 'sometimes|string|max:255',
            'organizer_id' => 'sometimes|integer|exists:users,id',
            'rsvp_status' => ['sometimes', new Enum(RsvpStatus::class)],
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string|in:event_title,organizer_name,rsvp_status,created_at',
            'sort_order' => 'sometimes|string|in:asc,desc',
        ];
    }
}