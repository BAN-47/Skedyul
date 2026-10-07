<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faculty', function (Blueprint $table) {
            if (!Schema::hasColumn('faculty', 'fac_special_position')) {
                $table->string('fac_special_position')->nullable();
            }
            if (!Schema::hasColumn('faculty', 'fac_special_position_max_hours')) {
                $table->decimal('fac_special_position_max_hours', 5, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('faculty', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['fac_special_position', 'fac_special_position_max_hours'],
                fn ($column) => Schema::hasColumn('faculty', $column)
            ));
            if ($columns) $table->dropColumn($columns);
        });
    }
};
