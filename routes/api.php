<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    Route::get('/attendees', [AttendeeController::class, 'index']);
    Route::post('/attendees', [AttendeeController::class, 'store']);
    Route::get('/attendees/{attendee}', [AttendeeController::class, 'show']);
    Route::patch('/attendees/{attendee}', [AttendeeController::class, 'update']);
    Route::delete('/attendees/{attendee}', [AttendeeController::class, 'destroy']);
});

require __DIR__.'/auth.php';
