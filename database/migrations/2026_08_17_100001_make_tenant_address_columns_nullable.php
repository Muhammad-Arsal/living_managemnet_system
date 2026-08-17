<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('address_line_1')->nullable()->change();
            $table->string('city', 100)->nullable()->change();
            $table->string('postcode', 12)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('address_line_1')->nullable(false)->change();
            $table->string('city', 100)->nullable(false)->change();
            $table->string('postcode', 12)->nullable(false)->change();
        });
    }
};
