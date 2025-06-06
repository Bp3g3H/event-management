<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->enum('rsvp_status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();

            $table->unique(['user_id', 'event_id']);

            $table->foreign('user_id', 'fk_attendees_user')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('event_id', 'fk_attendees_event')
                ->references('id')->on('events')
                ->onDelete('cascade');

            $table->index('user_id');
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('attendees');
    }
};
