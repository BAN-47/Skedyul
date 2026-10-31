<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP INDEX IF EXISTS room_name_location_building_unique');

        if (Schema::hasColumn('room', 'room_location')) {
            Schema::table('room', function ($table) {
                $table->dropColumn('room_location');
            });
        }

        DB::statement("CREATE UNIQUE INDEX room_name_location_building_unique ON room (room_name, COALESCE(room_building, ''))");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS room_name_location_building_unique');

        if (!Schema::hasColumn('room', 'room_location')) {
            Schema::table('room', function ($table) {
                $table->string('room_location', 150)->nullable();
            });
        }

        DB::statement("CREATE UNIQUE INDEX room_name_location_building_unique ON room (room_name, COALESCE(room_location, ''), COALESCE(room_building, ''))");
    }
};
