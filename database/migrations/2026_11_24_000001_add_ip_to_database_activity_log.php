<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || !Schema::hasTable('database_activity_log')) {
            return;
        }

        if (!Schema::hasColumn('database_activity_log', 'dal_ip_address')) {
            Schema::table('database_activity_log', function ($table): void {
                $table->string('dal_ip_address', 45)->nullable();
            });
        }

        // The original row-audit trigger remains unchanged. This small trigger
        // adds the client address seen by PostgreSQL to each database log row.
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION public.skedyul_set_database_activity_ip()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = pg_catalog, public
AS $function$
BEGIN
    NEW.dal_ip_address := host(inet_client_addr());
    RETURN NEW;
END;
$function$;

DROP TRIGGER IF EXISTS skedyul_database_activity_ip ON public.database_activity_log;
CREATE TRIGGER skedyul_database_activity_ip
BEFORE INSERT ON public.database_activity_log
FOR EACH ROW EXECUTE FUNCTION public.skedyul_set_database_activity_ip();
SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || !Schema::hasTable('database_activity_log')) {
            return;
        }

        DB::unprepared(<<<'SQL'
DROP TRIGGER IF EXISTS skedyul_database_activity_ip ON public.database_activity_log;
DROP FUNCTION IF EXISTS public.skedyul_set_database_activity_ip();
SQL);

        if (Schema::hasColumn('database_activity_log', 'dal_ip_address')) {
            Schema::table('database_activity_log', function ($table): void {
                $table->dropColumn('dal_ip_address');
            });
        }
    }
};
