<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supabase uses PostgreSQL triggers. Local MySQL remains on the regular Laravel audit log.
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        Schema::create('database_activity_log', function (Blueprint $table): void {
            $table->uuid('dal_id')->primary();
            $table->string('dal_operation', 12)->index();
            $table->string('dal_table_name', 150)->index();
            $table->text('dal_record_key')->nullable();
            $table->text('dal_changed_columns')->nullable();
            $table->string('dal_database_user', 150)->nullable();
            $table->timestampTz('dal_created_at')->useCurrent()->index();
        });

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION public.skedyul_capture_direct_database_change()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = pg_catalog, public
AS $function$
DECLARE
    changed_row jsonb;
    primary_key_columns text[];
    primary_key_values jsonb := '{}'::jsonb;
    changed_column_names text[];
    key_column text;
BEGIN
    -- Laravel already records signed-in actions with the user's name and role.
    -- Skip those writes here so the Admin does not see duplicate entries.
    IF current_setting('skedyul.audit_source', true) = 'laravel' THEN
        IF TG_OP = 'DELETE' THEN RETURN OLD; ELSE RETURN NEW; END IF;
    END IF;

    IF TG_OP = 'DELETE' THEN
        changed_row := to_jsonb(OLD);
    ELSE
        changed_row := to_jsonb(NEW);
    END IF;

    IF TG_OP = 'UPDATE' THEN
        SELECT array_agg(new_value.key ORDER BY new_value.key)
          INTO changed_column_names
          FROM jsonb_each(to_jsonb(NEW)) AS new_value
         WHERE (to_jsonb(OLD) -> new_value.key) IS DISTINCT FROM new_value.value;
    END IF;

    SELECT array_agg(attribute.attname ORDER BY key_column.ordinality)
      INTO primary_key_columns
      FROM pg_index index_info
      CROSS JOIN LATERAL unnest(index_info.indkey) WITH ORDINALITY AS key_column(attnum, ordinality)
      JOIN pg_attribute attribute
        ON attribute.attrelid = index_info.indrelid
       AND attribute.attnum = key_column.attnum
     WHERE index_info.indrelid = TG_RELID
       AND index_info.indisprimary;

    IF primary_key_columns IS NOT NULL THEN
        FOREACH key_column IN ARRAY primary_key_columns LOOP
            primary_key_values := primary_key_values || jsonb_build_object(key_column, changed_row -> key_column);
        END LOOP;
    END IF;

    INSERT INTO public.database_activity_log (
        dal_id,
        dal_operation,
        dal_table_name,
        dal_record_key,
        dal_changed_columns,
        dal_database_user,
        dal_created_at
    ) VALUES (
        gen_random_uuid(),
        TG_OP,
        TG_TABLE_NAME,
        CASE WHEN primary_key_columns IS NULL THEN NULL ELSE primary_key_values::text END,
        CASE WHEN changed_column_names IS NULL THEN NULL ELSE array_to_string(changed_column_names, ', ') END,
        session_user,
        now()
    );

    IF TG_OP = 'DELETE' THEN RETURN OLD; ELSE RETURN NEW; END IF;
END;
$function$;
SQL);

        // Attach to business tables already in the public schema. Do not audit the log itself
        // or Laravel's internal tables, which would create noise or recursive writes.
        DB::unprepared(<<<'SQL'
DO $block$
DECLARE
    target_table record;
BEGIN
    FOR target_table IN
        SELECT namespace.nspname, relation.relname
          FROM pg_class relation
          JOIN pg_namespace namespace ON namespace.oid = relation.relnamespace
         WHERE namespace.nspname = 'public'
           AND (relation.relkind = 'p' OR (relation.relkind = 'r' AND NOT relation.relispartition))
           AND relation.relname NOT IN (
               'database_activity_log', 'audit_log', 'migrations', 'cache',
               'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions'
           )
    LOOP
        EXECUTE format('DROP TRIGGER IF EXISTS skedyul_direct_database_audit ON %I.%I', target_table.nspname, target_table.relname);
        EXECUTE format(
            'CREATE TRIGGER skedyul_direct_database_audit AFTER INSERT OR UPDATE OR DELETE ON %I.%I FOR EACH ROW EXECUTE FUNCTION public.skedyul_capture_direct_database_change()',
            target_table.nspname,
            target_table.relname
        );
    END LOOP;
END;
$block$;
SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
DO $block$
DECLARE
    target_table record;
BEGIN
    FOR target_table IN
        SELECT namespace.nspname, relation.relname
          FROM pg_class relation
          JOIN pg_namespace namespace ON namespace.oid = relation.relnamespace
         WHERE namespace.nspname = 'public'
           AND (relation.relkind = 'p' OR (relation.relkind = 'r' AND NOT relation.relispartition))
    LOOP
        EXECUTE format('DROP TRIGGER IF EXISTS skedyul_direct_database_audit ON %I.%I', target_table.nspname, target_table.relname);
    END LOOP;
END;
$block$;
DROP FUNCTION IF EXISTS public.skedyul_capture_direct_database_change();
SQL);

        Schema::dropIfExists('database_activity_log');
    }
};
