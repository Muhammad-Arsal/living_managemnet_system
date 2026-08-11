<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('councils', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('council_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('council_id')->constrained('councils')->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('organization')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->unique('council_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_profiles');
        Schema::dropIfExists('councils');
    }
};
