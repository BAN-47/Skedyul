<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_dean', function (Blueprint $table) {
            $table->uuid('dd_id')->primary();
            $table->uuid('dd_usr_id');
            $table->uuid('dd_dept_id');

            $table->foreign('dd_usr_id')->references('usr_id')->on('USER')->onDelete('cascade');
            $table->foreign('dd_dept_id')->references('dept_id')->on('department')->onDelete('cascade');

            // One Dean per Department, and one Department per Dean.
            $table->unique('dd_usr_id');
            $table->unique('dd_dept_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_dean');
    }
};