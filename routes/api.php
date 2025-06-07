<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsAttendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/attendees', [AttendeeController::class, 'index']);
    Route::post('/attendees', [AttendeeController::class, 'store']);
    Route::get('/attendees/{attendee}', [AttendeeController::class, 'show'])->whereNumber('attendee');
    Route::patch('/attendees/{attendee}', [AttendeeController::class, 'update'])
        ->whereNumber('attendee')
        ->can('update', 'attendee');
    Route::delete('/attendees/{attendee}', [AttendeeController::class, 'destroy'])
        ->whereNumber('attendee')
        ->can('delete', 'attendee');

    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store'])->can('store', Event::class);
    Route::get('/events/{event}', [EventController::class, 'show'])->whereNumber('event');
    Route::patch('/events/{event}', [EventController::class, 'update'])
        ->whereNumber('event')
        ->can('update', 'event');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])
        ->whereNumber('event')
        ->can('destroy', 'event');

    Route::post('/events/{event}/check-in', CheckInController::class)
        ->whereNumber('event')
        ->middleware(EnsureUserIsAttendee::class);

    Route::get('/users', [UserController::class, 'index'])->can('viewAny', User::class);
    Route::post('/users', [UserController::class, 'store'])->can('create', User::class);
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->whereNumber('user')
        ->can('view', 'user');
    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->whereNumber('user')
        ->can('update', 'user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->whereNumber('user')
        ->can('delete', 'user');
});

require __DIR__.'/auth.php';
