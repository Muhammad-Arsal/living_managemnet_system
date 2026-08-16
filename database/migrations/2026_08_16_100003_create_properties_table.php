<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->constrained('property_types')->restrictOnDelete();
            $table->string('name', 150);
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('city', 100);
            $table->string('county', 100)->nullable();
            $table->string('postcode', 12);
            $table->string('country', 100)->default('United Kingdom');
            $table->timestamps();

            $table->index('postcode');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
