<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('property_id')->constrained('properties')->restrictOnDelete();
            $table->date('started_on');
            $table->date('ended_on')->nullable();
            $table->unsignedBigInteger('active_property_id')->nullable();
            $table->unsignedBigInteger('active_tenant_id')->nullable();
            $table->timestamps();

            $table->unique('active_property_id');
            $table->unique('active_tenant_id');
            $table->index(['tenant_id', 'started_on']);
            $table->index(['property_id', 'started_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenancies');
    }
};
