<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->morphs('participant');
            $table->string('role');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['ticket_id', 'participant_type', 'participant_id'], 'ticket_participants_actor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_participants');
    }
};
