<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'webauthn_credentials';

        // Some existing Supabase installations already have this table provisioned
        // outside Laravel's migration history. Keep that schema intact.
        if (Schema::hasTable($tableName)) {
            $requiredColumns = [
                'id',
                'usr_id',
                'credential_id',
                'public_key',
                'sign_count',
                'transports',
                'device_name',
                'credential_record',
                'created_at',
                'last_used_at',
            ];

            if (! Schema::hasColumns($tableName, $requiredColumns)) {
                throw new RuntimeException(
                    'The existing webauthn_credentials table is incomplete; review its columns before migrating.'
                );
            }

            return;
        }

        Schema::create($tableName, function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('usr_id')->index();
            $table->string('credential_id', 512)->unique();
            $table->text('public_key');
            $table->unsignedInteger('sign_count')->default(0);
            $table->json('transports')->nullable();
            $table->string('device_name', 80)->default('My passkey');
            $table->json('credential_record');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('last_used_at')->nullable();

            $table->foreign('usr_id')
                ->references('usr_id')
                ->on('USER')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Intentionally preserve this table on rollback. It may predate this
        // migration in an existing Supabase database and contains login credentials.
    }
};
