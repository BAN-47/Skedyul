<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_backup', function (Blueprint $table) {
            $table->uuid('bkp_id')->primary();
            $table->string('bkp_file_path');
            $table->string('bkp_status'); // 'success' or 'failed'
            $table->text('bkp_error_message')->nullable();
            $table->timestamp('bkp_ran_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_backup');
    }
};