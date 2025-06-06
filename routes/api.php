<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\EventController;
use App\Http\Middleware\EnsureUserIsAttendee;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    Route::post('/events/{event}/check-in', CheckInController::class)->middleware(EnsureUserIsAttendee::class);

    Route::get('/attendees', [AttendeeController::class, 'index']);
    Route::post('/attendees', [AttendeeController::class, 'store']);
    Route::get('/attendees/{attendee}', [AttendeeController::class, 'show']);
    Route::patch('/attendees/{attendee}', [AttendeeController::class, 'update'])->can('update', 'attendee');
    Route::delete('/attendees/{attendee}', [AttendeeController::class, 'destroy'])->can('delete', 'attendee');
});

require __DIR__.'/auth.php';
