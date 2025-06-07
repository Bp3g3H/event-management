<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'Admin';
    case Organizer = 'Organizer';
    case Attendee = 'Attendee';
}
