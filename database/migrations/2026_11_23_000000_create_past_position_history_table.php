<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('past_position_history')) {
            return;
        }

        Schema::create('past_position_history', function (Blueprint $table): void {
            $table->uuid('pph_id')->primary();
            $table->string('pph_position', 40)->index();
            $table->string('pph_scope_type', 30)->index();
            $table->uuid('pph_scope_id')->index();
            $table->uuid('pph_former_usr_id')->nullable()->index();
            $table->uuid('pph_new_usr_id')->nullable()->index();
            $table->string('pph_former_name')->nullable();
            $table->string('pph_new_name')->nullable();
            $table->string('pph_action', 30);
            $table->string('pph_changed_by');
            $table->timestampTz('pph_created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('past_position_history');
    }
};
