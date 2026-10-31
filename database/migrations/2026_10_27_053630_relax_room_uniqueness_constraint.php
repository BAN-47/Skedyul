<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old unique constraint on room_name alone
        DB::statement('ALTER TABLE room DROP CONSTRAINT IF EXISTS room_room_name_key');
        
        // Add composite unique constraint: same name OK if it is in a different building
        DB::statement('
            CREATE UNIQUE INDEX room_name_location_building_unique 
            ON room (room_name, COALESCE(room_building, \'\'))
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS room_name_location_building_unique');
        DB::statement('ALTER TABLE room ADD CONSTRAINT room_room_name_key UNIQUE (room_name)');
    }
};
