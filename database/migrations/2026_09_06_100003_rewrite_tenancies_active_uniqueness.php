<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenancies', function (Blueprint $table) {
            $table->foreignId('room_id')
                ->nullable()
                ->after('property_id')
                ->constrained('rooms')
                ->restrictOnDelete();
            $table->unsignedBigInteger('active_whole_property_id')->nullable()->after('ended_on');
            $table->unsignedBigInteger('active_room_id')->nullable()->after('active_whole_property_id');
        });

        DB::table('tenancies')
            ->whereNull('ended_on')
            ->update([
                'active_whole_property_id' => DB::raw('property_id'),
            ]);

        Schema::table('tenancies', function (Blueprint $table) {
            $table->dropUnique(['active_property_id']);
            $table->dropColumn('active_property_id');
            $table->unique('active_whole_property_id');
            $table->unique('active_room_id');
            $table->index(['room_id', 'started_on']);
        });
    }

    public function down(): void
    {
        Schema::table('tenancies', function (Blueprint $table) {
            $table->unsignedBigInteger('active_property_id')->nullable()->after('ended_on');
        });

        DB::table('tenancies')
            ->whereNull('ended_on')
            ->whereNull('room_id')
            ->update([
                'active_property_id' => DB::raw('property_id'),
            ]);

        Schema::table('tenancies', function (Blueprint $table) {
            $table->dropUnique(['active_whole_property_id']);
            $table->dropUnique(['active_room_id']);
            $table->dropIndex(['room_id', 'started_on']);
            $table->dropConstrainedForeignId('room_id');
            $table->dropColumn(['active_whole_property_id', 'active_room_id']);
            $table->unique('active_property_id');
        });
    }
};
