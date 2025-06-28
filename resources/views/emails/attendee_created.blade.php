<p>
    Thank you for signing up! You are now registered for "<strong>{{ $event->name }}</strong>", happening on <strong>{{ $event->date->format('F j, Y') }}</strong> at <strong>{{ $event->location }}</strong>. We look forward to seeing you there!
</p>

<p>
    Please do not forget to check in a day before the event at this<br>
    <a href="{{ url('/events/' . $event->id . '/check-in') }}">
        link
    </a>
</p>