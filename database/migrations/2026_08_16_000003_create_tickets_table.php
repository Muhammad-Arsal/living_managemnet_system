<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('subject');
            $table->text('body');
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->restrictOnDelete();
            $table->foreignId('ticket_priority_id')->constrained('ticket_priorities')->restrictOnDelete();
            $table->string('status')->default('open');
            $table->nullableMorphs('creator');
            $table->nullableMorphs('assignee');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
