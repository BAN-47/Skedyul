--
-- PostgreSQL database dump
--

\restrict UseyCe6xbVQn9mXIwjtWaiMlgUTxZIg1vkhBOfNgbjZELjbaHIQD7f6L4tbTCT0

-- Dumped from database version 17.6
-- Dumped by pg_dump version 18.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: auth; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA auth;


ALTER SCHEMA auth OWNER TO supabase_admin;

--
-- Name: extensions; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA extensions;


ALTER SCHEMA extensions OWNER TO postgres;

--
-- Name: graphql; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA graphql;


ALTER SCHEMA graphql OWNER TO supabase_admin;

--
-- Name: graphql_public; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA graphql_public;


ALTER SCHEMA graphql_public OWNER TO supabase_admin;

--
-- Name: pgbouncer; Type: SCHEMA; Schema: -; Owner: pgbouncer
--

CREATE SCHEMA pgbouncer;


ALTER SCHEMA pgbouncer OWNER TO pgbouncer;

--
-- Name: realtime; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA realtime;


ALTER SCHEMA realtime OWNER TO supabase_admin;

--
-- Name: storage; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA storage;


ALTER SCHEMA storage OWNER TO supabase_admin;

--
-- Name: vault; Type: SCHEMA; Schema: -; Owner: supabase_admin
--

CREATE SCHEMA vault;


ALTER SCHEMA vault OWNER TO supabase_admin;

--
-- Name: btree_gist; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS btree_gist WITH SCHEMA public;


--
-- Name: EXTENSION btree_gist; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION btree_gist IS 'support for indexing common datatypes in GiST';


--
-- Name: pg_stat_statements; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_stat_statements WITH SCHEMA extensions;


--
-- Name: EXTENSION pg_stat_statements; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pg_stat_statements IS 'track planning and execution statistics of all SQL statements executed';


--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA extensions;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- Name: supabase_vault; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS supabase_vault WITH SCHEMA vault;


--
-- Name: EXTENSION supabase_vault; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION supabase_vault IS 'Supabase Vault Extension';


--
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA extensions;


--
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- Name: aal_level; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.aal_level AS ENUM (
    'aal1',
    'aal2',
    'aal3'
);


ALTER TYPE auth.aal_level OWNER TO supabase_auth_admin;

--
-- Name: code_challenge_method; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.code_challenge_method AS ENUM (
    's256',
    'plain'
);


ALTER TYPE auth.code_challenge_method OWNER TO supabase_auth_admin;

--
-- Name: factor_status; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.factor_status AS ENUM (
    'unverified',
    'verified'
);


ALTER TYPE auth.factor_status OWNER TO supabase_auth_admin;

--
-- Name: factor_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.factor_type AS ENUM (
    'totp',
    'webauthn',
    'phone',
    'recovery_code'
);


ALTER TYPE auth.factor_type OWNER TO supabase_auth_admin;

--
-- Name: oauth_authorization_status; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_authorization_status AS ENUM (
    'pending',
    'approved',
    'denied',
    'expired'
);


ALTER TYPE auth.oauth_authorization_status OWNER TO supabase_auth_admin;

--
-- Name: oauth_client_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_client_type AS ENUM (
    'public',
    'confidential'
);


ALTER TYPE auth.oauth_client_type OWNER TO supabase_auth_admin;

--
-- Name: oauth_registration_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_registration_type AS ENUM (
    'dynamic',
    'manual'
);


ALTER TYPE auth.oauth_registration_type OWNER TO supabase_auth_admin;

--
-- Name: oauth_response_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.oauth_response_type AS ENUM (
    'code'
);


ALTER TYPE auth.oauth_response_type OWNER TO supabase_auth_admin;

--
-- Name: one_time_token_type; Type: TYPE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TYPE auth.one_time_token_type AS ENUM (
    'confirmation_token',
    'reauthentication_token',
    'recovery_token',
    'email_change_token_new',
    'email_change_token_current',
    'phone_change_token'
);


ALTER TYPE auth.one_time_token_type OWNER TO supabase_auth_admin;

--
-- Name: fac_employment_type; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.fac_employment_type AS ENUM (
    'FULL_TIME',
    'PART_TIME'
);


ALTER TYPE public.fac_employment_type OWNER TO postgres;

--
-- Name: faculty_employment_type; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.faculty_employment_type AS ENUM (
    'FULL_TIME',
    'PART_TIME'
);


ALTER TYPE public.faculty_employment_type OWNER TO postgres;

--
-- Name: sched_day; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.sched_day AS ENUM (
    'MONDAY',
    'TUESDAY',
    'WEDNESDAY',
    'THURSDAY',
    'FRIDAY',
    'SATURDAY'
);


ALTER TYPE public.sched_day OWNER TO postgres;

--
-- Name: sched_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.sched_status AS ENUM (
    'DRAFT',
    'SUBMITTED',
    'APPROVED',
    'RETURNED',
    'PUBLISHED'
);


ALTER TYPE public.sched_status OWNER TO postgres;

--
-- Name: schedule_day; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.schedule_day AS ENUM (
    'MONDAY',
    'TUESDAY',
    'WEDNESDAY',
    'THURSDAY',
    'FRIDAY',
    'SATURDAY'
);


ALTER TYPE public.schedule_day OWNER TO postgres;

--
-- Name: schedule_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.schedule_status AS ENUM (
    'DRAFT',
    'SUBMITTED',
    'APPROVED',
    'RETURNED',
    'PUBLISHED'
);


ALTER TYPE public.schedule_status OWNER TO postgres;

--
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.user_role AS ENUM (
    'SUPER_ADMIN',
    'DEAN',
    'CHAIR',
    'FACULTY'
);


ALTER TYPE public.user_role OWNER TO postgres;

--
-- Name: action; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.action AS ENUM (
    'INSERT',
    'UPDATE',
    'DELETE',
    'TRUNCATE',
    'ERROR'
);


ALTER TYPE realtime.action OWNER TO supabase_realtime_admin;

--
-- Name: equality_op; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.equality_op AS ENUM (
    'eq',
    'neq',
    'lt',
    'lte',
    'gt',
    'gte',
    'in',
    'like',
    'ilike',
    'is',
    'match',
    'imatch',
    'isdistinct'
);


ALTER TYPE realtime.equality_op OWNER TO supabase_realtime_admin;

--
-- Name: user_defined_filter; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.user_defined_filter AS (
	column_name text,
	op realtime.equality_op,
	value text,
	negate boolean
);


ALTER TYPE realtime.user_defined_filter OWNER TO supabase_realtime_admin;

--
-- Name: wal_column; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.wal_column AS (
	name text,
	type_name text,
	type_oid oid,
	value jsonb,
	is_pkey boolean,
	is_selectable boolean
);


ALTER TYPE realtime.wal_column OWNER TO supabase_realtime_admin;

--
-- Name: wal_rls; Type: TYPE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TYPE realtime.wal_rls AS (
	wal jsonb,
	is_rls_enabled boolean,
	subscription_ids uuid[],
	errors text[]
);


ALTER TYPE realtime.wal_rls OWNER TO supabase_realtime_admin;

--
-- Name: buckettype; Type: TYPE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TYPE storage.buckettype AS ENUM (
    'STANDARD',
    'ANALYTICS',
    'VECTOR'
);


ALTER TYPE storage.buckettype OWNER TO supabase_storage_admin;

--
-- Name: email(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.email() RETURNS text
    LANGUAGE sql STABLE
    AS $$
  select 
  coalesce(
    nullif(current_setting('request.jwt.claim.email', true), ''),
    (nullif(current_setting('request.jwt.claims', true), '')::jsonb ->> 'email')
  )::text
$$;


ALTER FUNCTION auth.email() OWNER TO supabase_auth_admin;

--
-- Name: FUNCTION email(); Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON FUNCTION auth.email() IS 'Deprecated. Use auth.jwt() -> ''email'' instead.';


--
-- Name: jwt(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.jwt() RETURNS jsonb
    LANGUAGE sql STABLE
    AS $$
  select 
    coalesce(
        nullif(current_setting('request.jwt.claim', true), ''),
        nullif(current_setting('request.jwt.claims', true), '')
    )::jsonb
$$;


ALTER FUNCTION auth.jwt() OWNER TO supabase_auth_admin;

--
-- Name: role(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.role() RETURNS text
    LANGUAGE sql STABLE
    AS $$
  select 
  coalesce(
    nullif(current_setting('request.jwt.claim.role', true), ''),
    (nullif(current_setting('request.jwt.claims', true), '')::jsonb ->> 'role')
  )::text
$$;


ALTER FUNCTION auth.role() OWNER TO supabase_auth_admin;

--
-- Name: FUNCTION role(); Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON FUNCTION auth.role() IS 'Deprecated. Use auth.jwt() -> ''role'' instead.';


--
-- Name: uid(); Type: FUNCTION; Schema: auth; Owner: supabase_auth_admin
--

CREATE FUNCTION auth.uid() RETURNS uuid
    LANGUAGE sql STABLE
    AS $$
  select 
  coalesce(
    nullif(current_setting('request.jwt.claim.sub', true), ''),
    (nullif(current_setting('request.jwt.claims', true), '')::jsonb ->> 'sub')
  )::uuid
$$;


ALTER FUNCTION auth.uid() OWNER TO supabase_auth_admin;

--
-- Name: FUNCTION uid(); Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON FUNCTION auth.uid() IS 'Deprecated. Use auth.jwt() -> ''sub'' instead.';


--
-- Name: grant_pg_cron_access(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.grant_pg_cron_access() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
BEGIN
  IF EXISTS (
    SELECT
    FROM pg_event_trigger_ddl_commands() AS ev
    JOIN pg_extension AS ext
    ON ev.objid = ext.oid
    WHERE ext.extname = 'pg_cron'
  )
  THEN
    grant usage on schema cron to postgres with grant option;

    alter default privileges in schema cron grant all on tables to postgres with grant option;
    alter default privileges in schema cron grant all on functions to postgres with grant option;
    alter default privileges in schema cron grant all on sequences to postgres with grant option;

    alter default privileges for user supabase_admin in schema cron grant all
        on sequences to postgres with grant option;
    alter default privileges for user supabase_admin in schema cron grant all
        on tables to postgres with grant option;
    alter default privileges for user supabase_admin in schema cron grant all
        on functions to postgres with grant option;

    grant all privileges on all tables in schema cron to postgres with grant option;
    revoke all on table cron.job from postgres;
    grant select on table cron.job to postgres with grant option;
    revoke trigger on cron.job_run_details from postgres;
  END IF;
END;
$$;


ALTER FUNCTION extensions.grant_pg_cron_access() OWNER TO supabase_admin;

--
-- Name: FUNCTION grant_pg_cron_access(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.grant_pg_cron_access() IS 'Grants access to pg_cron';


--
-- Name: grant_pg_graphql_access(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.grant_pg_graphql_access() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $_$
begin
    if not exists (
        select 1
        from pg_catalog.pg_event_trigger_ddl_commands() ev
        join pg_catalog.pg_extension e on ev.objid = e.oid
        where e.extname = 'pg_graphql'
    ) then
        return;
    end if;

    drop function if exists graphql_public.graphql;
    create or replace function graphql_public.graphql(
        "operationName" text default null,
        query text default null,
        variables jsonb default null,
        extensions jsonb default null
    )
        returns jsonb
        language sql
    as $$
        select graphql.resolve(
            query := query,
            variables := coalesce(variables, '{}'),
            "operationName" := "operationName",
            extensions := extensions
        );
    $$;

    -- Attach the wrapper to the extension so DROP EXTENSION cascades to it,
    -- which in turn triggers set_graphql_placeholder to reinstall the "not enabled" stub.
    alter extension pg_graphql add function graphql_public.graphql(text, text, jsonb, jsonb);

    grant usage on schema graphql to postgres, anon, authenticated, service_role;
    grant execute on function graphql.resolve to postgres, anon, authenticated, service_role;
    grant usage on schema graphql to postgres with grant option;
    grant usage on schema graphql_public to postgres with grant option;
end;
$_$;


ALTER FUNCTION extensions.grant_pg_graphql_access() OWNER TO supabase_admin;

--
-- Name: FUNCTION grant_pg_graphql_access(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.grant_pg_graphql_access() IS 'Grants access to pg_graphql';


--
-- Name: grant_pg_net_access(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.grant_pg_net_access() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
BEGIN
  IF EXISTS (
    SELECT 1
    FROM pg_event_trigger_ddl_commands() AS ev
    JOIN pg_extension AS ext
    ON ev.objid = ext.oid
    WHERE ext.extname = 'pg_net'
  )
  THEN
    IF NOT EXISTS (
      SELECT 1
      FROM pg_roles
      WHERE rolname = 'supabase_functions_admin'
    )
    THEN
      CREATE USER supabase_functions_admin NOINHERIT CREATEROLE LOGIN NOREPLICATION;
    END IF;

    GRANT USAGE ON SCHEMA net TO supabase_functions_admin, postgres, anon, authenticated, service_role;

    IF EXISTS (
      SELECT FROM pg_extension
      WHERE extname = 'pg_net'
      -- all versions in use on existing projects as of 2025-02-20
      -- version 0.12.0 onwards don't need these applied
      AND extversion IN ('0.2', '0.6', '0.7', '0.7.1', '0.8.0', '0.10.0', '0.11.0')
    ) THEN
      ALTER function net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) SECURITY DEFINER;
      ALTER function net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) SECURITY DEFINER;

      ALTER function net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) SET search_path = net;
      ALTER function net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) SET search_path = net;

      REVOKE ALL ON FUNCTION net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) FROM PUBLIC;
      REVOKE ALL ON FUNCTION net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) FROM PUBLIC;

      GRANT EXECUTE ON FUNCTION net.http_get(url text, params jsonb, headers jsonb, timeout_milliseconds integer) TO supabase_functions_admin, postgres, anon, authenticated, service_role;
      GRANT EXECUTE ON FUNCTION net.http_post(url text, body jsonb, params jsonb, headers jsonb, timeout_milliseconds integer) TO supabase_functions_admin, postgres, anon, authenticated, service_role;
    END IF;
  END IF;
END;
$$;


ALTER FUNCTION extensions.grant_pg_net_access() OWNER TO supabase_admin;

--
-- Name: FUNCTION grant_pg_net_access(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.grant_pg_net_access() IS 'Grants access to pg_net';


--
-- Name: pgrst_ddl_watch(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.pgrst_ddl_watch() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
DECLARE
  cmd record;
BEGIN
  FOR cmd IN SELECT * FROM pg_event_trigger_ddl_commands()
  LOOP
    IF cmd.command_tag IN (
      'CREATE SCHEMA', 'ALTER SCHEMA'
    , 'CREATE TABLE', 'CREATE TABLE AS', 'SELECT INTO', 'ALTER TABLE'
    , 'CREATE FOREIGN TABLE', 'ALTER FOREIGN TABLE'
    , 'CREATE VIEW', 'ALTER VIEW'
    , 'CREATE MATERIALIZED VIEW', 'ALTER MATERIALIZED VIEW'
    , 'CREATE FUNCTION', 'ALTER FUNCTION'
    , 'CREATE TRIGGER'
    , 'CREATE TYPE', 'ALTER TYPE'
    , 'CREATE RULE'
    , 'COMMENT'
    )
    -- don't notify in case of CREATE TEMP table or other objects created on pg_temp
    AND cmd.schema_name is distinct from 'pg_temp'
    THEN
      NOTIFY pgrst, 'reload schema';
    END IF;
  END LOOP;
END; $$;


ALTER FUNCTION extensions.pgrst_ddl_watch() OWNER TO supabase_admin;

--
-- Name: pgrst_drop_watch(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.pgrst_drop_watch() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $$
DECLARE
  obj record;
BEGIN
  FOR obj IN SELECT * FROM pg_event_trigger_dropped_objects()
  LOOP
    IF obj.object_type IN (
      'schema'
    , 'table'
    , 'foreign table'
    , 'view'
    , 'materialized view'
    , 'function'
    , 'trigger'
    , 'type'
    , 'rule'
    )
    AND obj.is_temporary IS false -- no pg_temp objects
    THEN
      NOTIFY pgrst, 'reload schema';
    END IF;
  END LOOP;
END; $$;


ALTER FUNCTION extensions.pgrst_drop_watch() OWNER TO supabase_admin;

--
-- Name: set_graphql_placeholder(); Type: FUNCTION; Schema: extensions; Owner: supabase_admin
--

CREATE FUNCTION extensions.set_graphql_placeholder() RETURNS event_trigger
    LANGUAGE plpgsql
    SET search_path TO ''
    AS $_$
    DECLARE
    graphql_is_dropped bool;
    BEGIN
    graphql_is_dropped = (
        SELECT ev.schema_name = 'graphql_public'
        FROM pg_event_trigger_dropped_objects() AS ev
        WHERE ev.schema_name = 'graphql_public'
    );

    IF graphql_is_dropped
    THEN
        create or replace function graphql_public.graphql(
            "operationName" text default null,
            query text default null,
            variables jsonb default null,
            extensions jsonb default null
        )
            returns jsonb
            language plpgsql
            set search_path to ''
        as $$
            DECLARE
                server_version float;
            BEGIN
                server_version = (SELECT (SPLIT_PART((select version()), ' ', 2))::float);

                IF server_version >= 14 THEN
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql extension is not enabled.'
                            )
                        )
                    );
                ELSE
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql is only available on projects running Postgres 14 onwards.'
                            )
                        )
                    );
                END IF;
            END;
        $$;
    END IF;

    END;
$_$;


ALTER FUNCTION extensions.set_graphql_placeholder() OWNER TO supabase_admin;

--
-- Name: FUNCTION set_graphql_placeholder(); Type: COMMENT; Schema: extensions; Owner: supabase_admin
--

COMMENT ON FUNCTION extensions.set_graphql_placeholder() IS 'Reintroduces placeholder function for graphql_public.graphql';


--
-- Name: graphql(text, text, jsonb, jsonb); Type: FUNCTION; Schema: graphql_public; Owner: supabase_admin
--

CREATE FUNCTION graphql_public.graphql("operationName" text DEFAULT NULL::text, query text DEFAULT NULL::text, variables jsonb DEFAULT NULL::jsonb, extensions jsonb DEFAULT NULL::jsonb) RETURNS jsonb
    LANGUAGE plpgsql
    AS $$
            DECLARE
                server_version float;
            BEGIN
                server_version = (SELECT (SPLIT_PART((select version()), ' ', 2))::float);

                IF server_version >= 14 THEN
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql extension is not enabled.'
                            )
                        )
                    );
                ELSE
                    RETURN jsonb_build_object(
                        'errors', jsonb_build_array(
                            jsonb_build_object(
                                'message', 'pg_graphql is only available on projects running Postgres 14 onwards.'
                            )
                        )
                    );
                END IF;
            END;
        $$;


ALTER FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) OWNER TO supabase_admin;

--
-- Name: get_auth(text); Type: FUNCTION; Schema: pgbouncer; Owner: supabase_admin
--

CREATE FUNCTION pgbouncer.get_auth(p_usename text) RETURNS TABLE(username text, password text)
    LANGUAGE plpgsql SECURITY DEFINER
    SET search_path TO ''
    AS $_$
  BEGIN
      RAISE DEBUG 'PgBouncer auth request: %', p_usename;

      RETURN QUERY
      SELECT
          rolname::text,
          CASE WHEN rolvaliduntil < now()
              THEN null
              ELSE rolpassword::text
          END
      FROM pg_authid
      WHERE rolname=$1 and rolcanlogin;
  END;
  $_$;


ALTER FUNCTION pgbouncer.get_auth(p_usename text) OWNER TO supabase_admin;

--
-- Name: rls_auto_enable(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.rls_auto_enable() RETURNS event_trigger
    LANGUAGE plpgsql SECURITY DEFINER
    SET search_path TO 'pg_catalog'
    AS $$
DECLARE
  cmd record;
BEGIN
  FOR cmd IN
    SELECT *
    FROM pg_event_trigger_ddl_commands()
    WHERE command_tag IN ('CREATE TABLE', 'CREATE TABLE AS', 'SELECT INTO')
      AND object_type IN ('table','partitioned table')
  LOOP
     IF cmd.schema_name IS NOT NULL AND cmd.schema_name IN ('public') AND cmd.schema_name NOT IN ('pg_catalog','information_schema') AND cmd.schema_name NOT LIKE 'pg_toast%' AND cmd.schema_name NOT LIKE 'pg_temp%' THEN
      BEGIN
        EXECUTE format('alter table if exists %s enable row level security', cmd.object_identity);
        RAISE LOG 'rls_auto_enable: enabled RLS on %', cmd.object_identity;
      EXCEPTION
        WHEN OTHERS THEN
          RAISE LOG 'rls_auto_enable: failed to enable RLS on %', cmd.object_identity;
      END;
     ELSE
        RAISE LOG 'rls_auto_enable: skip % (either system schema or not in enforced list: %.)', cmd.object_identity, cmd.schema_name;
     END IF;
  END LOOP;
END;
$$;


ALTER FUNCTION public.rls_auto_enable() OWNER TO postgres;

--
-- Name: apply_rls(jsonb, integer); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer DEFAULT (1024 * 1024)) RETURNS SETOF realtime.wal_rls
    LANGUAGE plpgsql
    AS $$
declare
    -- Regclass of the table e.g. public.notes
    entity_ regclass = (quote_ident(wal ->> 'schema') || '.' || quote_ident(wal ->> 'table'))::regclass;

    -- I, U, D, T: insert, update ...
    action realtime.action = (
        case wal ->> 'action'
            when 'I' then 'INSERT'
            when 'U' then 'UPDATE'
            when 'D' then 'DELETE'
            else 'ERROR'
        end
    );

    -- Is row level security enabled for the table
    is_rls_enabled bool = relrowsecurity from pg_class where oid = entity_;

    subscriptions realtime.subscription[] = array_agg(subs)
        from
            realtime.subscription subs
        where
            subs.entity = entity_
            -- Filter by action early - only get subscriptions interested in this action
            -- action_filter column can be: '*' (all), 'INSERT', 'UPDATE', or 'DELETE'
            and (subs.action_filter = '*' or subs.action_filter = action::text);

    -- Subscription vars
    working_role regrole;
    working_selected_columns text[];
    claimed_role regrole;
    claims jsonb;

    subscription_id uuid;
    subscription_has_access bool;
    visible_to_subscription_ids uuid[] = '{}';

    -- structured info for wal's columns
    columns realtime.wal_column[];
    -- previous identity values for update/delete
    old_columns realtime.wal_column[];

    -- the same values as wal2json sent them, which is what filters compare against
    raw_columns realtime.wal_column[] =
        array_agg((x->>'name', x->>'type', x->>'typeoid', x->'value', null, null)::realtime.wal_column)
        from jsonb_array_elements(wal -> 'columns') x;
    raw_old_columns realtime.wal_column[] =
        array_agg((x->>'name', x->>'type', x->>'typeoid', x->'value', null, null)::realtime.wal_column)
        from jsonb_array_elements(wal -> 'identity') x;

    error_record_exceeds_max_size boolean = octet_length(wal::text) > max_record_bytes;

    -- Primary jsonb output for record
    output jsonb;

    -- Loop record for iterating unique roles (outer loop)
    role_record record;
    -- Loop record for iterating unique selected_columns within a role (inner loop)
    cols_record record;
    -- Subscription ids visible at the role level (before fanning out by selected_columns)
    visible_role_sub_ids uuid[] = '{}';

begin
    perform set_config('role', null, true);

    columns =
        array_agg(
            (
                x->>'name',
                x->>'type',
                x->>'typeoid',
                realtime.cast(
                    (x->'value') #>> '{}',
                    coalesce(
                        (x->>'typeoid')::regtype, -- null when wal2json version <= 2.4
                        (x->>'type')::regtype
                    )
                ),
                (pks ->> 'name') is not null,
                true
            )::realtime.wal_column
        )
        from
            jsonb_array_elements(wal -> 'columns') x
            left join jsonb_array_elements(wal -> 'pk') pks
                on (x ->> 'name') = (pks ->> 'name');

    old_columns =
        array_agg(
            (
                x->>'name',
                x->>'type',
                x->>'typeoid',
                realtime.cast(
                    (x->'value') #>> '{}',
                    coalesce(
                        (x->>'typeoid')::regtype, -- null when wal2json version <= 2.4
                        (x->>'type')::regtype
                    )
                ),
                (pks ->> 'name') is not null,
                true
            )::realtime.wal_column
        )
        from
            jsonb_array_elements(wal -> 'identity') x
            left join jsonb_array_elements(wal -> 'pk') pks
                on (x ->> 'name') = (pks ->> 'name');

    for role_record in
        select claims_role
        from (select distinct claims_role from unnest(subscriptions)) t
        order by claims_role::text
    loop
        working_role := role_record.claims_role;

        -- Update `is_selectable` for columns and old_columns (once per role)
        columns =
            array_agg(
                (
                    c.name,
                    c.type_name,
                    c.type_oid,
                    c.value,
                    c.is_pkey,
                    pg_catalog.has_column_privilege(working_role, entity_, c.name, 'SELECT')
                )::realtime.wal_column
            )
            from
                unnest(columns) c;

        old_columns =
                array_agg(
                    (
                        c.name,
                        c.type_name,
                        c.type_oid,
                        c.value,
                        c.is_pkey,
                        pg_catalog.has_column_privilege(working_role, entity_, c.name, 'SELECT')
                    )::realtime.wal_column
                )
                from
                    unnest(old_columns) c;

        if action <> 'DELETE' and count(1) = 0 from unnest(columns) c where c.is_pkey then
            -- Fan out 400 error per distinct selected_columns for this role
            for cols_record in
                select selected_columns
                from (select distinct selected_columns from unnest(subscriptions) s where s.claims_role = working_role) t
                order by coalesce(array_to_string(selected_columns, ','), '')
            loop
                working_selected_columns := cols_record.selected_columns;
                return next (
                    jsonb_build_object(
                        'schema', wal ->> 'schema',
                        'table', wal ->> 'table',
                        'type', action
                    ),
                    is_rls_enabled,
                    (select array_agg(s.subscription_id) from unnest(subscriptions) as s where s.claims_role = working_role and (s.selected_columns is not distinct from working_selected_columns)),
                    array['Error 400: Bad Request, no primary key']
                )::realtime.wal_rls;
            end loop;

        -- The claims role does not have SELECT permission to the primary key of entity
        elsif action <> 'DELETE' and sum(c.is_selectable::int) <> count(1) from unnest(columns) c where c.is_pkey then
            -- Fan out 401 error per distinct selected_columns for this role
            for cols_record in
                select selected_columns
                from (select distinct selected_columns from unnest(subscriptions) s where s.claims_role = working_role) t
                order by coalesce(array_to_string(selected_columns, ','), '')
            loop
                working_selected_columns := cols_record.selected_columns;
                return next (
                    jsonb_build_object(
                        'schema', wal ->> 'schema',
                        'table', wal ->> 'table',
                        'type', action
                    ),
                    is_rls_enabled,
                    (select array_agg(s.subscription_id) from unnest(subscriptions) as s where s.claims_role = working_role and (s.selected_columns is not distinct from working_selected_columns)),
                    array['Error 401: Unauthorized']
                )::realtime.wal_rls;
            end loop;

        else
            -- Create the prepared statement (once per role)
            if is_rls_enabled and action <> 'DELETE' then
                if (select 1 from pg_prepared_statements where name = 'walrus_rls_stmt' limit 1) > 0 then
                    deallocate walrus_rls_stmt;
                end if;
                execute realtime.build_prepared_statement_sql('walrus_rls_stmt', entity_, columns);
            end if;

            -- Collect all visible subscription IDs for this role (filter check + RLS check)
            visible_role_sub_ids = '{}';

            for subscription_id, claims in (
                    select
                        subs.subscription_id,
                        subs.claims
                    from
                        unnest(subscriptions) subs
                    where
                        subs.entity = entity_
                        and subs.claims_role = working_role
                        and (
                            realtime.is_visible_through_filters(raw_columns, subs.filters)
                            or (
                              action = 'DELETE'
                              and realtime.is_visible_through_filters(raw_old_columns, subs.filters)
                            )
                        )
            ) loop

                if not is_rls_enabled or action = 'DELETE' then
                    visible_role_sub_ids = visible_role_sub_ids || subscription_id;
                else
                    -- Check if RLS allows the role to see the record
                    perform
                        -- Trim leading and trailing quotes from working_role because set_config
                        -- doesn't recognize the role as valid if they are included
                        set_config('role', trim(both '"' from working_role::text), true),
                        set_config('request.jwt.claims', claims::text, true);

                    execute 'execute walrus_rls_stmt' into subscription_has_access;

                    -- Reset the role on every FOR..LOOP batch execution.
                    -- The first batch of 10 rows is pre-fetched using the current connection role (PG internal behaviour)
                    -- then we have to reset it again otherwise it would use the role defined in the `set_config` above
                    -- to fetch the remaining rows when rows>10, which could be a user-defined role that lacks execution grants.
                    -- The flow is:
                    --   1. run batch with conn role
                    --   2. set_config working_role
                    --   3. execute walrus
                    --   4. reset role (revert)
                    --   5. repeat
                    perform set_config('role', null, true);

                    if subscription_has_access then
                        visible_role_sub_ids = visible_role_sub_ids || subscription_id;
                    end if;
                end if;
            end loop;

            perform set_config('role', null, true);

            -- Inner loop: per distinct selected_columns for this role
            for cols_record in
                select selected_columns
                from (select distinct selected_columns from unnest(subscriptions) s where s.claims_role = working_role) t
                order by coalesce(array_to_string(selected_columns, ','), '')
            loop
                working_selected_columns := cols_record.selected_columns;

                output = jsonb_build_object(
                    'schema', wal ->> 'schema',
                    'table', wal ->> 'table',
                    'type', action,
                    'commit_timestamp', to_char(
                        ((wal ->> 'timestamp')::timestamptz at time zone 'utc'),
                        'YYYY-MM-DD"T"HH24:MI:SS.MS"Z"'
                    ),
                    'columns', (
                        select
                            jsonb_agg(
                                jsonb_build_object(
                                    'name', pa.attname,
                                    'type', pt.typname
                                )
                                order by pa.attnum asc
                            )
                        from
                            pg_attribute pa
                            join pg_type pt
                                on pa.atttypid = pt.oid
                            left join (
                                select unnest(conkey) as pkey_attnum
                                from pg_constraint
                                where conrelid = entity_ and contype = 'p'
                            ) pk on pk.pkey_attnum = pa.attnum
                        where
                            attrelid = entity_
                            and attnum > 0
                            and pg_catalog.has_column_privilege(working_role, entity_, pa.attname, 'SELECT')
                            and (working_selected_columns is null or pa.attname = any(working_selected_columns) or pk.pkey_attnum is not null)
                    )
                )
                -- Add "record" key for insert and update
                || case
                    when action in ('INSERT', 'UPDATE') then
                        jsonb_build_object(
                            'record',
                            (
                                select
                                    jsonb_object_agg(
                                        -- if unchanged toast, get column name and value from old record
                                        coalesce((c).name, (oc).name),
                                        case
                                            when (c).name is null then (oc).value
                                            else (c).value
                                        end
                                    )
                                from
                                    unnest(columns) c
                                    full outer join unnest(old_columns) oc
                                        on (c).name = (oc).name
                                where
                                    coalesce((c).is_selectable, (oc).is_selectable)
                                    and (working_selected_columns is null or coalesce((c).name, (oc).name) = any(working_selected_columns) or coalesce((c).is_pkey, (oc).is_pkey))
                                    and ( not error_record_exceeds_max_size or (octet_length((c).value::text) <= 64))
                            )
                        )
                    else '{}'::jsonb
                end
                -- Add "old_record" key for update and delete
                || case
                    when action = 'UPDATE' then
                        jsonb_build_object(
                                'old_record',
                                (
                                    select jsonb_object_agg((c).name, (c).value)
                                    from unnest(old_columns) c
                                    where
                                        (c).is_selectable
                                        and (working_selected_columns is null or (c).name = any(working_selected_columns) or (c).is_pkey)
                                        and ( not error_record_exceeds_max_size or (octet_length((c).value::text) <= 64))
                                )
                            )
                    when action = 'DELETE' then
                        jsonb_build_object(
                            'old_record',
                            (
                                select jsonb_object_agg((c).name, (c).value)
                                from unnest(old_columns) c
                                where
                                    (c).is_selectable
                                    and (working_selected_columns is null or (c).name = any(working_selected_columns) or (c).is_pkey)
                                    and ( not error_record_exceeds_max_size or (octet_length((c).value::text) <= 64))
                                    and ( not is_rls_enabled or (c).is_pkey ) -- if RLS enabled, we can't secure deletes so filter to pkey
                            )
                        )
                    else '{}'::jsonb
                end;

                -- Filter visible_role_sub_ids to those matching the current selected_columns group
                visible_to_subscription_ids = coalesce(
                    (
                        select array_agg(s.subscription_id)
                        from unnest(subscriptions) s
                        where s.claims_role = working_role
                          and (s.selected_columns is not distinct from working_selected_columns)
                          and s.subscription_id = any(visible_role_sub_ids)
                    ),
                    '{}'::uuid[]
                );

                return next (
                    output,
                    is_rls_enabled,
                    visible_to_subscription_ids,
                    case
                        when error_record_exceeds_max_size then array['Error 413: Payload Too Large']
                        else '{}'
                    end
                )::realtime.wal_rls;
            end loop;

        end if;
    end loop;

    perform set_config('role', null, true);
end;
$$;


ALTER FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) OWNER TO supabase_realtime_admin;

--
-- Name: authorize(text, text, text, text, text, text[], text[]); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.authorize(role_name text, topic_name text, claims text, sub text, headers text, read_extensions text[], write_extensions text[], OUT read_allowed boolean[], OUT write_allowed boolean[]) RETURNS record
    LANGUAGE plpgsql
    AS $$
declare
  probe_ids uuid[];
  probe_inserted_at timestamp := now() at time zone 'utc';
  visible_ids uuid[];
  ext text;
  allowed boolean;
begin
  -- The probe inserts are rolled back, but they still give the transaction an xid, so it ends
  -- in a commit. Nothing durable was written, so skip waiting on the WAL flush and sync standbys.
  -- Set outside the block below, whose rollback would undo it.
  perform set_config('synchronous_commit', 'off', true);

  -- The RAISE at the end of this block rolls back everything in it.
  begin
    probe_ids := array(select gen_random_uuid() from unnest(read_extensions));

    insert into realtime.messages (id, topic, extension, inserted_at, updated_at)
    select p.id, topic_name, p.extension, probe_inserted_at, probe_inserted_at
    from unnest(probe_ids, read_extensions) as p(id, extension);

    perform set_config('role', role_name, true),
            set_config('realtime.topic', topic_name, true),
            set_config('request.jwt.claims', claims, true),
            set_config('request.jwt.claim.sub', sub, true),
            set_config('request.jwt.claim.role', role_name, true),
            set_config('request.headers', headers, true);

    -- One lookup for every probe, by the full primary key.
    visible_ids := array(
      select m.id
      from realtime.messages m
      where m.inserted_at = probe_inserted_at
        and m.id = any(probe_ids));

    read_allowed := array(
      select p.id = any(visible_ids)
      from unnest(probe_ids) with ordinality as p(id, n)
      order by p.n);

    write_allowed := '{}';

    foreach ext in array write_extensions loop
      begin
        insert into realtime.messages (topic, extension, inserted_at, updated_at)
        values (topic_name, ext, now() at time zone 'utc', now() at time zone 'utc');

        allowed := true;
      exception when insufficient_privilege then
        allowed := false;
      end;

      write_allowed := write_allowed || allowed;
    end loop;

    raise sqlstate 'RTA01';
  exception when sqlstate 'RTA01' then
    null;
  end;
end;
$$;


ALTER FUNCTION realtime.authorize(role_name text, topic_name text, claims text, sub text, headers text, read_extensions text[], write_extensions text[], OUT read_allowed boolean[], OUT write_allowed boolean[]) OWNER TO supabase_realtime_admin;

--
-- Name: broadcast_changes(text, text, text, text, text, record, record, text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text DEFAULT 'ROW'::text) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
    -- Declare a variable to hold the JSONB representation of the row
    row_data jsonb := '{}'::jsonb;
BEGIN
    IF level = 'STATEMENT' THEN
        RAISE EXCEPTION 'function can only be triggered for each row, not for each statement';
    END IF;
    -- Check the operation type and handle accordingly
    IF operation = 'INSERT' OR operation = 'UPDATE' OR operation = 'DELETE' THEN
        row_data := jsonb_build_object('old_record', OLD, 'record', NEW, 'operation', operation, 'table', table_name, 'schema', table_schema);
        PERFORM realtime.send (row_data, event_name, topic_name);
    ELSE
        RAISE EXCEPTION 'Unexpected operation type: %', operation;
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE EXCEPTION 'Failed to process the row: %', SQLERRM;
END;

$$;


ALTER FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text) OWNER TO supabase_realtime_admin;

--
-- Name: build_prepared_statement_sql(text, regclass, realtime.wal_column[]); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) RETURNS text
    LANGUAGE sql
    AS $$
      /*
      Builds a sql string that, if executed, creates a prepared statement to
      tests retrive a row from *entity* by its primary key columns.
      Example
          select realtime.build_prepared_statement_sql('public.notes', '{"id"}'::text[], '{"bigint"}'::text[])
      */
          select
      'prepare ' || prepared_statement_name || ' as
          select
              exists(
                  select
                      1
                  from
                      ' || entity || '
                  where
                      ' || string_agg(quote_ident(pkc.name) || '=' || quote_nullable(pkc.value #>> '{}') , ' and ') || '
              )'
          from
              unnest(columns) pkc
          where
              pkc.is_pkey
          group by
              entity
      $$;


ALTER FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) OWNER TO supabase_realtime_admin;

--
-- Name: cast(text, regtype); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime."cast"(val text, type_ regtype) RETURNS jsonb
    LANGUAGE plpgsql IMMUTABLE
    AS $$
declare
  res jsonb;
begin
  if type_::text = 'bytea' then
    return to_jsonb(val);
  end if;
  execute format('select to_jsonb(%L::'|| type_::text || ')', val) into res;
  return res;
end
$$;


ALTER FUNCTION realtime."cast"(val text, type_ regtype) OWNER TO supabase_realtime_admin;

--
-- Name: check_equality_op(realtime.equality_op, regtype, text, text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) RETURNS boolean
    LANGUAGE plpgsql IMMUTABLE
    AS $$
/*
Casts *val_1* and *val_2* as type *type_* and check the *op* condition for truthiness
*/
declare
    op_symbol text = (
        case
            when op = 'eq' then '='
            when op = 'neq' then '!='
            when op = 'lt' then '<'
            when op = 'lte' then '<='
            when op = 'gt' then '>'
            when op = 'gte' then '>='
            when op = 'in' then '= any'
            else 'UNKNOWN OP'
        end
    );
    res boolean;
begin
    execute format(
        'select %L::'|| type_::text || ' ' || op_symbol
        || ' ( %L::'
        || (
            case
                when op = 'in' then type_::text || '[]'
                else type_::text end
        )
        || ')', val_1, val_2) into res;
    return res;
end;
$$;


ALTER FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) OWNER TO supabase_realtime_admin;

--
-- Name: check_equality_op(realtime.equality_op, regtype, text, text, boolean); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) RETURNS boolean
    LANGUAGE plpgsql STABLE
    AS $$
declare
    op_symbol text;
    res boolean;
begin
    -- IS DISTINCT FROM / IS NOT DISTINCT FROM: infix, both sides typed literals
    if op = 'isdistinct' then
        execute format(
            'select %L::%s %s %L::%s',
            val_1,
            type_::text,
            case when negate then 'IS NOT DISTINCT FROM' else 'IS DISTINCT FROM' end,
            val_2,
            type_::text
        ) into res;
        return res;
    end if;

    -- IS requires a keyword RHS (NULL, TRUE, FALSE, UNKNOWN), not a typed literal
    if op = 'is' then
        if val_2 not in ('null', 'true', 'false', 'unknown') then
            raise exception 'invalid value for is filter: must be null, true, false, or unknown';
        end if;
        execute format(
            'select %L::%s %s %s',
            val_1,
            type_::text,
            case when negate then 'IS NOT' else 'IS' end,
            upper(val_2)
        ) into res;
        return res;
    end if;

    op_symbol = case
        when op = 'eq'    then '='
        when op = 'neq'   then '!='
        when op = 'lt'    then '<'
        when op = 'lte'   then '<='
        when op = 'gt'    then '>'
        when op = 'gte'   then '>='
        when op = 'in'    then '= any'
        when op = 'like'   then 'LIKE'
        when op = 'ilike'  then 'ILIKE'
        when op = 'match'  then '~'
        when op = 'imatch' then '~*'
        else null
    end;

    if op_symbol is null then
        raise exception 'unsupported equality operator: %', op::text;
    end if;

    execute format(
        'select %L::%s %s (%L::%s)',
        val_1,
        type_::text,
        op_symbol,
        val_2,
        case when op = 'in' then type_::text || '[]' else type_::text end
    ) into res;

    return case when negate then not res else res end;
end;
$$;


ALTER FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) OWNER TO supabase_realtime_admin;

--
-- Name: filters_hash(realtime.user_defined_filter[]); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.filters_hash(filters realtime.user_defined_filter[]) RETURNS bytea
    LANGUAGE sql IMMUTABLE STRICT PARALLEL SAFE
    AS $$
  select pg_catalog.int8send(pg_catalog.hash_array_extended(filters, 0))
      || pg_catalog.int8send(pg_catalog.hash_array_extended(filters, 1))
$$;


ALTER FUNCTION realtime.filters_hash(filters realtime.user_defined_filter[]) OWNER TO supabase_realtime_admin;

--
-- Name: is_visible_through_filters(realtime.wal_column[], realtime.user_defined_filter[]); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) RETURNS boolean
    LANGUAGE sql STABLE
    AS $$
    select
        filters is null
        or array_length(filters, 1) is null
        or coalesce(
            count(col.name) = count(1)
            and sum(
                realtime.check_equality_op(
                    op:=f.op,
                    type_:=coalesce(col.type_oid::regtype, col.type_name::regtype),
                    val_1:=col.value #>> '{}',
                    val_2:=f.value,
                    negate:=coalesce(f.negate, false)
                )::int
            ) filter (where col.name is not null) = count(col.name),
            false
        )
    from
        unnest(filters) f
        left join unnest(columns) col
            on f.column_name = col.name;
$$;


ALTER FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) OWNER TO supabase_realtime_admin;

--
-- Name: list_changes(name, name, integer, integer); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) RETURNS TABLE(wal jsonb, is_rls_enabled boolean, subscription_ids uuid[], errors text[], slot_changes_count bigint)
    LANGUAGE sql
    SET log_min_messages TO 'fatal'
    AS $$
  WITH pub AS (
    SELECT
      concat_ws(
        ',',
        CASE WHEN bool_or(pubinsert) THEN 'insert' ELSE NULL END,
        CASE WHEN bool_or(pubupdate) THEN 'update' ELSE NULL END,
        CASE WHEN bool_or(pubdelete) THEN 'delete' ELSE NULL END
      ) AS w2j_actions,
      coalesce(
        string_agg(
          realtime.quote_wal2json(format('%I.%I', schemaname, tablename)::regclass),
          ','
        ) filter (WHERE ppt.tablename IS NOT NULL),
        ''
      ) AS w2j_add_tables
    FROM pg_publication pp
    LEFT JOIN pg_publication_tables ppt ON pp.pubname = ppt.pubname
    WHERE pp.pubname = publication
    GROUP BY pp.pubname
    LIMIT 1
  ),
  -- MATERIALIZED ensures pg_logical_slot_get_changes is called exactly once
  w2j AS MATERIALIZED (
    SELECT x.*, pub.w2j_add_tables
    FROM pub,
         pg_logical_slot_get_changes(
           slot_name, null, max_changes,
           'include-pk', 'true',
           'include-transaction', 'false',
           'include-timestamp', 'true',
           'include-type-oids', 'true',
           'format-version', '2',
           'actions', pub.w2j_actions,
           'add-tables', pub.w2j_add_tables
         ) x
  ),
  slot_count AS (
    SELECT count(*)::bigint AS cnt
    FROM w2j
    WHERE w2j.w2j_add_tables <> ''
  ),
  rls_filtered AS (
    SELECT xyz.wal, xyz.is_rls_enabled, xyz.subscription_ids, xyz.errors
    FROM w2j,
         realtime.apply_rls(
           wal := w2j.data::jsonb,
           max_record_bytes := max_record_bytes
         ) xyz(wal, is_rls_enabled, subscription_ids, errors)
    WHERE w2j.w2j_add_tables <> ''
      AND xyz.subscription_ids[1] IS NOT NULL
  )
  SELECT rf.wal, rf.is_rls_enabled, rf.subscription_ids, rf.errors, sc.cnt
  FROM rls_filtered rf, slot_count sc

  UNION ALL

  SELECT null, null, null, null, sc.cnt
  FROM slot_count sc
  WHERE NOT EXISTS (SELECT 1 FROM rls_filtered)
$$;


ALTER FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) OWNER TO supabase_realtime_admin;

--
-- Name: list_changes_sync(name, name, integer, integer); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.list_changes_sync(publication name, slot_name name, max_changes integer, max_record_bytes integer) RETURNS TABLE(wal jsonb, is_rls_enabled boolean, subscription_ids uuid[], errors text[], slot_changes_count bigint)
    LANGUAGE sql
    SET log_min_messages TO 'fatal'
    AS $$
  WITH pub AS (
    SELECT
      concat_ws(
        ',',
        CASE WHEN bool_or(pubinsert) THEN 'insert' ELSE NULL END,
        CASE WHEN bool_or(pubupdate) THEN 'update' ELSE NULL END,
        CASE WHEN bool_or(pubdelete) THEN 'delete' ELSE NULL END
      ) AS w2j_actions,
      coalesce(
        string_agg(
          realtime.quote_wal2json(format('%I.%I', schemaname, tablename)::regclass),
          ','
        ) filter (WHERE ppt.tablename IS NOT NULL),
        ''
      ) AS w2j_add_tables
    FROM pg_publication pp
    LEFT JOIN pg_publication_tables ppt ON pp.pubname = ppt.pubname
    WHERE pp.pubname = publication
    GROUP BY pp.pubname
    LIMIT 1
  ),
  -- MATERIALIZED ensures the slot is read exactly once.
  consumed AS MATERIALIZED (
    SELECT x.*, pub.w2j_add_tables
    FROM pub,
         realtime.settled_changes(
           slot_name, max_changes,
           'include-pk', 'true',
           'include-transaction', 'false',
           'include-timestamp', 'true',
           'include-type-oids', 'true',
           'format-version', '2',
           'actions', pub.w2j_actions,
           'add-tables', pub.w2j_add_tables
         ) x
  ),
  slot_count AS (
    SELECT count(*)::bigint AS cnt
    FROM consumed
    WHERE consumed.w2j_add_tables <> ''
  ),
  rls_filtered AS (
    SELECT xyz.wal, xyz.is_rls_enabled, xyz.subscription_ids, xyz.errors
    FROM consumed,
         realtime.apply_rls(
           wal := consumed.data::jsonb,
           max_record_bytes := max_record_bytes
         ) xyz(wal, is_rls_enabled, subscription_ids, errors)
    WHERE consumed.w2j_add_tables <> ''
      AND xyz.subscription_ids[1] IS NOT NULL
  )
  SELECT rf.wal, rf.is_rls_enabled, rf.subscription_ids, rf.errors, sc.cnt
  FROM rls_filtered rf, slot_count sc

  UNION ALL

  SELECT null, null, null, null, sc.cnt
  FROM slot_count sc
  WHERE NOT EXISTS (SELECT 1 FROM rls_filtered)
$$;


ALTER FUNCTION realtime.list_changes_sync(publication name, slot_name name, max_changes integer, max_record_bytes integer) OWNER TO supabase_realtime_admin;

--
-- Name: quote_wal2json(regclass); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.quote_wal2json(entity regclass) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $$
  SELECT
    realtime.wal2json_escape_identifier(nsp.nspname::text)
    || '.'
    || realtime.wal2json_escape_identifier(pc.relname::text)
  FROM pg_class pc
  JOIN pg_namespace nsp ON pc.relnamespace = nsp.oid
  WHERE pc.oid = entity
$$;


ALTER FUNCTION realtime.quote_wal2json(entity regclass) OWNER TO supabase_realtime_admin;

--
-- Name: send(jsonb, text, text, boolean); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean DEFAULT true) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
  generated_id uuid;
  final_payload jsonb;
BEGIN
  BEGIN
    generated_id := gen_random_uuid();

    -- Check if payload has an 'id' key, if not, add the generated UUID
    IF payload ? 'id' THEN
      final_payload := payload;
    ELSE
      final_payload := jsonb_set(payload, '{id}', to_jsonb(generated_id));
    END IF;

    -- Set the topic configuration
    EXECUTE format('SET LOCAL realtime.topic TO %L', topic);

    INSERT INTO realtime.messages (id, payload, event, topic, private, extension)
    VALUES (generated_id, final_payload, event, topic, private, 'broadcast');
  EXCEPTION
    WHEN OTHERS THEN
      RAISE WARNING 'WarnSendingBroadcastMessage: %', SQLERRM;
  END;
END;
$$;


ALTER FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean) OWNER TO supabase_realtime_admin;

--
-- Name: send_binary(bytea, text, text, boolean); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean DEFAULT true) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
  generated_id uuid;
BEGIN
  BEGIN
    generated_id := gen_random_uuid();

    EXECUTE format('SET LOCAL realtime.topic TO %L', topic);

    INSERT INTO realtime.messages (id, binary_payload, event, topic, private, extension)
    VALUES (generated_id, payload, event, topic, private, 'broadcast');
  EXCEPTION
    WHEN OTHERS THEN
      RAISE WARNING 'WarnSendingBroadcastMessage: %', SQLERRM;
  END;
END;
$$;


ALTER FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean) OWNER TO supabase_realtime_admin;

--
-- Name: settled_changes(name, integer, text[]); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.settled_changes(slot_name name, max_changes integer, VARIADIC opts text[]) RETURNS TABLE(lsn pg_lsn, xid xid, data text)
    LANGUAGE plpgsql
    AS $$
declare
  upto pg_lsn;
  total bigint;
  xids xid[];
  starts bigint[];
  snapshot pg_snapshot;
  running_xids xid[];
  xmax_age int;
  cut bigint;
begin
  -- Each statement in a volatile function takes its own snapshot, which is what lets the
  -- check below see a writer that was still in flight when the peek ran. Under REPEATABLE
  -- READ the snapshot never advances, so a deferred change would never be released.
  if current_setting('transaction_isolation') <> 'read committed' then
    raise exception 'realtime.settled_changes requires READ COMMITTED';
  end if;

  -- The peek and the read below cover the same WAL, so the read cannot reach a commit the
  -- check never saw.
  upto := pg_current_wal_flush_lsn();

  -- One entry per transaction, in commit order: its xid and the position of its first
  -- change. The peek uses the caller's own options, so max_changes counts exactly what the
  -- read counts. A non-transactional logical message is emitted as soon as it is decoded,
  -- tagged with the xid of whatever transaction wrote it, so it does not mark where that
  -- transaction starts.
  select coalesce(sum(g.n), 0),
         array_agg(g.x order by g.first) filter (where g.first is not null),
         array_agg(g.first order by g.first) filter (where g.first is not null)
    into total, xids, starts
    from (
      select p.xid as x, count(*) as n,
             min(p.ord) filter (where not case
               when starts_with(p.data, '{"action":"M"') then (p.data::jsonb->>'transactional')::boolean is false
               else false
             end) as first
      from pg_logical_slot_peek_changes(slot_name, upto, max_changes, variadic opts)
           with ordinality as p(lsn, xid, data, ord)
      group by p.xid
    ) g;

  -- Nothing for the caller, but the slot still has to move past what the peek covered.
  if total = 0 then
    perform pg_replication_slot_advance(slot_name, upto);
    return;
  end if;

  if xids is not null then
    -- Taken after the peek is materialized, so a writer that was still in flight during
    -- decoding is guaranteed to show up here.
    snapshot := pg_current_snapshot();

    -- A commit record reaches the WAL before the writer leaves the proc array, so a change
    -- can be decoded while its row is invisible. apply_rls would resolve a policy against a
    -- row it cannot see and authorize it for nobody, while the read consumed it regardless.
    --
    -- xip lists transactions running when the snapshot was taken. It does not cover a writer
    -- whose xid sits at or beyond xmax, which never appears there, so the horizon is checked
    -- too. age() counts backwards from the current xid and so compares correctly across
    -- wraparound.
    select coalesce(array_agg(running.x::xid), array[]::xid[])
      into running_xids
      from pg_snapshot_xip(snapshot) running(x);
    xmax_age := age(pg_snapshot_xmax(snapshot)::xid);

    select min(u.s) into cut
      from unnest(xids, starts) as u(x, s)
      where u.x = any(running_xids) or age(u.x) <= xmax_age;
  end if;

  -- The read stops right after the commit that brings its count to upto_nchanges, so the
  -- count of changes in front of the first unsettled transaction stops it just before that
  -- transaction.
  if cut is null then
    return query
      select p.* from pg_logical_slot_get_changes(slot_name, upto, max_changes, variadic opts) p;
  elsif cut > 1 then
    return query
      select p.* from pg_logical_slot_get_changes(slot_name, upto, (cut - 1)::int, variadic opts) p;
  end if;
end;
$$;


ALTER FUNCTION realtime.settled_changes(slot_name name, max_changes integer, VARIADIC opts text[]) OWNER TO supabase_realtime_admin;

--
-- Name: subscription_check_filters(); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.subscription_check_filters() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
    col_names text[] = coalesce(
            array_agg(a.attname order by a.attnum),
            '{}'::text[]
        )
        from
            pg_catalog.pg_attribute a
        where
            a.attrelid = new.entity
            and a.attnum > 0
            and not a.attisdropped
            and pg_catalog.has_column_privilege(
                (new.claims ->> 'role'),
                a.attrelid,
                a.attnum,
                'SELECT'
            );
    filter realtime.user_defined_filter;
    col_type regtype;
    in_val jsonb;
    selected_col text;
begin
    for filter in select * from unnest(new.filters) loop
        if not filter.column_name = any(col_names) then
            raise exception 'invalid column for filter %', filter.column_name;
        end if;

        col_type = (
            select atttypid::regtype
            from pg_catalog.pg_attribute
            where attrelid = new.entity
                  and attname = filter.column_name
        );
        if col_type is null then
            raise exception 'failed to lookup type for column %', filter.column_name;
        end if;

        if filter.op = 'in'::realtime.equality_op then
            in_val = realtime.cast(filter.value, (col_type::text || '[]')::regtype);
            if coalesce(jsonb_array_length(in_val), 0) > 100 then
                raise exception 'too many values for `in` filter. Maximum 100';
            end if;
        elsif filter.op = 'is'::realtime.equality_op then
            -- `is` requires a keyword RHS rather than a typed literal
            if filter.value not in ('null', 'true', 'false', 'unknown') then
                raise exception 'invalid value for is filter: must be null, true, false, or unknown';
            end if;
            -- IS NULL works for any type, but IS TRUE/FALSE/UNKNOWN require a boolean
            -- operand. Reject the non-null keywords on non-boolean columns here so they
            -- don't abort apply_rls at WAL time.
            if filter.value <> 'null' and col_type <> 'boolean'::regtype then
                raise exception 'is % filter requires a boolean column, got %', filter.value, col_type::text;
            end if;
        elsif filter.op in ('like'::realtime.equality_op, 'ilike'::realtime.equality_op) then
            -- like/ilike apply the text pattern operator (~~); reject column types that
            -- have no such operator instead of failing at WAL time
            if not exists (
                select 1 from pg_catalog.pg_operator
                where oprname = '~~' and oprleft = col_type
            ) then
                raise exception 'operator % requires a text-compatible column type, got %', filter.op::text, col_type::text;
            end if;
        elsif filter.op in ('match'::realtime.equality_op, 'imatch'::realtime.equality_op) then
            -- match/imatch apply the regex operators ~ / ~*; reject column types that have
            -- no such operator (e.g. integer) instead of failing at WAL time, mirroring the
            -- like/ilike guard above.
            if not exists (
                select 1 from pg_catalog.pg_operator
                where oprname = case when filter.op = 'imatch'::realtime.equality_op then '~*' else '~' end
                  and oprleft = col_type
                  and oprright = col_type
                  and oprresult = 'boolean'::regtype
            ) then
                raise exception 'operator % requires a text-compatible column type, got %', filter.op::text, col_type::text;
            end if;
            -- validate the regex eagerly so a bad pattern is rejected here, not inside
            -- apply_rls where it would abort the WAL stream for the entity
            begin
                perform '' ~ filter.value;
            exception when others then
                raise exception 'invalid regular expression for % filter: %', filter.op::text, sqlerrm;
            end;
        else
            -- eq/neq/lt/lte/gt/gte: value must be coercable to the type
            perform realtime.cast(filter.value, col_type);
        end if;
    end loop;

    if new.selected_columns is not null then
        for selected_col in select * from unnest(new.selected_columns) loop
            if not selected_col = any(col_names) then
                raise exception 'invalid column for select %', selected_col;
            end if;
        end loop;
    end if;

    -- Apply consistent order to filters so the unique constraint can't be tricked by a
    -- different filter order. negate is part of the sort key.
    new.filters = coalesce(
        array_agg(f order by f.column_name, f.op, f.value, f.negate),
        '{}'
    ) from unnest(new.filters) f;

    -- Normalize selected_columns order so ARRAY['a','b'] and ARRAY['b','a'] are treated
    -- as the same subscription group in apply_rls. Preserve an empty array as '{}'
    -- ("primary keys only") so it stays distinct from NULL ("all columns"); array_agg
    -- over an empty set would otherwise collapse '{}' back to NULL.
    if new.selected_columns is not null then
        new.selected_columns = coalesce(
            (
                select array_agg(c order by c)
                from unnest(new.selected_columns) c
            ),
            '{}'::text[]
        );
    end if;

    return new;
end;
$$;


ALTER FUNCTION realtime.subscription_check_filters() OWNER TO supabase_realtime_admin;

--
-- Name: to_regrole(text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.to_regrole(role_name text) RETURNS regrole
    LANGUAGE sql IMMUTABLE
    AS $$ select role_name::regrole $$;


ALTER FUNCTION realtime.to_regrole(role_name text) OWNER TO supabase_realtime_admin;

--
-- Name: topic(); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.topic() RETURNS text
    LANGUAGE sql STABLE
    AS $$
select nullif(current_setting('realtime.topic', true), '')::text;
$$;


ALTER FUNCTION realtime.topic() OWNER TO supabase_realtime_admin;

--
-- Name: wal2json_escape_identifier(text); Type: FUNCTION; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE FUNCTION realtime.wal2json_escape_identifier(name text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $$
  -- Prefix `\`, `,`, `.`, and any whitespace with `\`
  SELECT regexp_replace(name, '([\\,.[:space:]])', '\\\1', 'g')
$$;


ALTER FUNCTION realtime.wal2json_escape_identifier(name text) OWNER TO supabase_realtime_admin;

--
-- Name: allow_any_operation(text[]); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.allow_any_operation(expected_operations text[]) RETURNS boolean
    LANGUAGE sql STABLE
    AS $$
  WITH current_operation AS (
    SELECT storage.operation() AS raw_operation
  ),
  normalized AS (
    SELECT CASE
      WHEN raw_operation LIKE 'storage.%' THEN substr(raw_operation, 9)
      ELSE raw_operation
    END AS current_operation
    FROM current_operation
  )
  SELECT EXISTS (
    SELECT 1
    FROM normalized n
    CROSS JOIN LATERAL unnest(expected_operations) AS expected_operation
    WHERE expected_operation IS NOT NULL
      AND expected_operation <> ''
      AND n.current_operation = CASE
        WHEN expected_operation LIKE 'storage.%' THEN substr(expected_operation, 9)
        ELSE expected_operation
      END
  );
$$;


ALTER FUNCTION storage.allow_any_operation(expected_operations text[]) OWNER TO supabase_storage_admin;

--
-- Name: allow_only_operation(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.allow_only_operation(expected_operation text) RETURNS boolean
    LANGUAGE sql STABLE
    AS $$
  WITH current_operation AS (
    SELECT storage.operation() AS raw_operation
  ),
  normalized AS (
    SELECT
      CASE
        WHEN raw_operation LIKE 'storage.%' THEN substr(raw_operation, 9)
        ELSE raw_operation
      END AS current_operation,
      CASE
        WHEN expected_operation LIKE 'storage.%' THEN substr(expected_operation, 9)
        ELSE expected_operation
      END AS requested_operation
    FROM current_operation
  )
  SELECT CASE
    WHEN requested_operation IS NULL OR requested_operation = '' THEN FALSE
    ELSE COALESCE(current_operation = requested_operation, FALSE)
  END
  FROM normalized;
$$;


ALTER FUNCTION storage.allow_only_operation(expected_operation text) OWNER TO supabase_storage_admin;

--
-- Name: can_insert_object(text, text, uuid, jsonb); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.can_insert_object(bucketid text, name text, owner uuid, metadata jsonb) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
  INSERT INTO "storage"."objects" ("bucket_id", "name", "owner", "metadata") VALUES (bucketid, name, owner, metadata);
  -- hack to rollback the successful insert
  RAISE sqlstate 'PT200' using
  message = 'ROLLBACK',
  detail = 'rollback successful insert';
END
$$;


ALTER FUNCTION storage.can_insert_object(bucketid text, name text, owner uuid, metadata jsonb) OWNER TO supabase_storage_admin;

--
-- Name: delete_lifecycle_objects(uuid[]); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.delete_lifecycle_objects(object_ids uuid[]) RETURNS TABLE(id uuid, is_delete_marker boolean, metadata jsonb)
    LANGUAGE plpgsql
    AS $$
DECLARE
    object_id uuid;
    deleted_row storage.objects%ROWTYPE;
    deleted_ids uuid[];
    deleted_markers boolean[];
    deleted_metadata jsonb[];
    bulk_deleted boolean := false;
BEGIN
    -- Surface pre-existing deferred violations instead of treating them as blocked rows.
    SET CONSTRAINTS ALL IMMEDIATE;
    PERFORM set_config('storage.allow_delete_query', 'true', true);

    -- One subxact keeps the subxid cache from overflowing; rows return only once it commits.
    BEGIN
        WITH deleted AS (
            DELETE FROM storage.objects AS object_row
              WHERE object_row.id = ANY(object_ids) AND object_row.archived_at IS NOT NULL
              RETURNING object_row.id, object_row.is_delete_marker, object_row.metadata
        )
        SELECT array_agg(deleted.id), array_agg(deleted.is_delete_marker), array_agg(deleted.metadata)
          INTO deleted_ids, deleted_markers, deleted_metadata
          FROM deleted;
        bulk_deleted := true;
    EXCEPTION
        WHEN OTHERS THEN
            -- User triggers may use insufficient_privilege to block a row.
            IF SQLSTATE <> '42501' AND left(SQLSTATE, 2) IN ('08', '40', '42', '53', '55', '57', '58', 'XX') THEN
                RAISE;
            END IF;
    END;
    IF bulk_deleted THEN
        RETURN QUERY SELECT * FROM unnest(deleted_ids, deleted_markers, deleted_metadata);
        RETURN;
    END IF;

    FOREACH object_id IN ARRAY object_ids LOOP
        BEGIN
            DELETE FROM storage.objects AS object_row
              WHERE object_row.id = object_id AND object_row.archived_at IS NOT NULL
              RETURNING object_row.* INTO deleted_row;
            IF FOUND THEN
                id := deleted_row.id;
                is_delete_marker := deleted_row.is_delete_marker;
                metadata := deleted_row.metadata;
                RETURN NEXT;
            END IF;
        EXCEPTION
            WHEN OTHERS THEN
                IF SQLSTATE <> '42501' AND left(SQLSTATE, 2) IN ('08', '40', '42', '53', '55', '57', '58', 'XX') THEN
                    RAISE;
                END IF;
        END;
    END LOOP;
END;
$$;


ALTER FUNCTION storage.delete_lifecycle_objects(object_ids uuid[]) OWNER TO supabase_storage_admin;

--
-- Name: enforce_bucket_lifecycle_service_role(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.enforce_bucket_lifecycle_service_role() RETURNS trigger
    LANGUAGE plpgsql
    SET search_path TO 'pg_catalog'
    AS $$
BEGIN
  IF current_user::text IS DISTINCT FROM TG_ARGV[0] THEN
    -- Keep this check AFTER caller RLS accepts the proposed row. The API
    -- recognizes this exact signal after rolling back its permission probe.
    RAISE EXCEPTION 'bucket control columns may only be changed by the configured storage service role'
      USING ERRCODE = 'PST01',
            SCHEMA = TG_TABLE_SCHEMA,
            TABLE = TG_TABLE_NAME,
            CONSTRAINT = TG_NAME;
  END IF;

  RETURN NULL;
END;
$$;


ALTER FUNCTION storage.enforce_bucket_lifecycle_service_role() OWNER TO supabase_storage_admin;

--
-- Name: enforce_bucket_name_length(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.enforce_bucket_name_length() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
    if length(new.name) > 100 then
        raise exception 'bucket name "%" is too long (% characters). Max is 100.', new.name, length(new.name);
    end if;
    return new;
end;
$$;


ALTER FUNCTION storage.enforce_bucket_name_length() OWNER TO supabase_storage_admin;

--
-- Name: enforce_versioning_status_transition(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.enforce_versioning_status_transition() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF NEW.versioning_status = OLD.versioning_status THEN
        RETURN NEW;
    END IF;

    IF NEW.versioning_status = 'DISABLED' OR
       (OLD.versioning_status = 'DISABLED' AND NEW.versioning_status = 'SUSPENDED') THEN
        RAISE EXCEPTION 'Cannot transition bucket versioning status from % to %',
            OLD.versioning_status, NEW.versioning_status;
    END IF;

    RETURN NEW;
END;
$$;


ALTER FUNCTION storage.enforce_versioning_status_transition() OWNER TO supabase_storage_admin;

--
-- Name: extension(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.extension(name text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
    _parts text[];
    _filename text;
BEGIN
    -- Split on "/" to get path segments
    SELECT string_to_array(name, '/') INTO _parts;
    -- Get the last path segment (the actual filename)
    SELECT _parts[array_length(_parts, 1)] INTO _filename;
    -- Extract extension: reverse, split on '.', then reverse again
    RETURN reverse(split_part(reverse(_filename), '.', 1));
END
$$;


ALTER FUNCTION storage.extension(name text) OWNER TO supabase_storage_admin;

--
-- Name: filename(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.filename(name text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
    _parts text[];
BEGIN
    SELECT string_to_array(name, '/') INTO _parts;
    RETURN _parts[array_length(_parts, 1)];
END
$$;


ALTER FUNCTION storage.filename(name text) OWNER TO supabase_storage_admin;

--
-- Name: foldername(text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.foldername(name text) RETURNS text[]
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
    _parts text[];
BEGIN
    -- Split on "/" to get path segments
    SELECT string_to_array(name, '/') INTO _parts;
    -- Return everything except the last segment
    RETURN _parts[1 : array_length(_parts,1) - 1];
END
$$;


ALTER FUNCTION storage.foldername(name text) OWNER TO supabase_storage_admin;

--
-- Name: get_common_prefix(text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.get_common_prefix(p_key text, p_prefix text, p_delimiter text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $$
SELECT CASE
    WHEN p_delimiter <> ''
         AND position(p_delimiter IN substring(p_key FROM length(p_prefix) + 1)) > 0
    THEN left(
        p_key,
        length(p_prefix)
            + position(p_delimiter IN substring(p_key FROM length(p_prefix) + 1))
            + length(p_delimiter) - 1
    )
    ELSE NULL
END;
$$;


ALTER FUNCTION storage.get_common_prefix(p_key text, p_prefix text, p_delimiter text) OWNER TO supabase_storage_admin;

--
-- Name: get_size_by_bucket(text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.get_size_by_bucket(noncurrent_versions text DEFAULT 'include'::text, delete_markers text DEFAULT 'include'::text) RETURNS TABLE(size bigint, bucket_id text)
    LANGUAGE plpgsql STABLE
    AS $$
BEGIN
    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'include');
    delete_markers := COALESCE(delete_markers, 'include');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'include';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'include';
    END IF;

    return query
        select sum((metadata->>'size')::bigint)::bigint as size, obj.bucket_id
        from "storage".objects as obj
        where (noncurrent_versions != 'exclude' OR obj.archived_at IS NULL)
          and (noncurrent_versions != 'only' OR obj.archived_at IS NOT NULL)
          and (delete_markers != 'exclude' OR NOT obj.is_delete_marker)
          and (delete_markers != 'only' OR obj.is_delete_marker)
        group by obj.bucket_id;
END
$$;


ALTER FUNCTION storage.get_size_by_bucket(noncurrent_versions text, delete_markers text) OWNER TO supabase_storage_admin;

--
-- Name: list_multipart_uploads_with_delimiter(text, text, text, integer, text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.list_multipart_uploads_with_delimiter(bucket_id text, prefix_param text, delimiter_param text, max_keys integer DEFAULT 100, next_key_token text DEFAULT ''::text, next_upload_token text DEFAULT ''::text, raw_prefix_param text DEFAULT NULL::text) RETURNS TABLE(key text, id text, created_at timestamp with time zone)
    LANGUAGE sql STABLE
    AS $_$
WITH candidates AS (
    SELECT
        upload.key AS object_key,
        CASE
            WHEN position($3 IN substring(upload.key FROM length(coalesce($7, $2)) + 1)) > 0
            THEN left(
                upload.key,
                length(coalesce($7, $2))
                    + position($3 IN substring(upload.key FROM length(coalesce($7, $2)) + 1))
                    + length($3) - 1
            )
            ELSE upload.key
        END AS result_key,
        upload.id,
        upload.created_at,
        position($3 IN substring(upload.key FROM length(coalesce($7, $2)) + 1)) > 0 AS is_common_prefix
    FROM storage.s3_multipart_uploads AS upload
    WHERE upload.bucket_id = $1
      AND upload.key COLLATE "C" LIKE $2 || '%'
), filtered AS (
    SELECT candidate.*
    FROM candidates AS candidate
    WHERE $5 = ''
       OR candidate.result_key COLLATE "C" > $5
       OR (
           candidate.result_key COLLATE "C" = $5
           AND NOT candidate.is_common_prefix
           AND $6 <> ''
           -- A completed or aborted marker repeats the remaining same-key uploads.
           AND COALESCE(
               (candidate.created_at, candidate.id COLLATE "C") > (
                   SELECT marker.created_at, marker.id COLLATE "C"
                   FROM storage.s3_multipart_uploads AS marker
                   WHERE marker.bucket_id = $1
                     AND marker.key COLLATE "C" = $5
                     AND marker.id = $6
               ),
               TRUE
           )
       )
), ranked AS (
    SELECT
        filtered.*,
        row_number() OVER (
            PARTITION BY filtered.result_key COLLATE "C"
            ORDER BY filtered.created_at, filtered.id COLLATE "C"
        ) AS prefix_rank
    FROM filtered
)
SELECT ranked.result_key, ranked.id, ranked.created_at
FROM ranked
WHERE NOT ranked.is_common_prefix OR ranked.prefix_rank = 1
ORDER BY ranked.result_key COLLATE "C", ranked.created_at, ranked.id COLLATE "C"
LIMIT $4;
$_$;


ALTER FUNCTION storage.list_multipart_uploads_with_delimiter(bucket_id text, prefix_param text, delimiter_param text, max_keys integer, next_key_token text, next_upload_token text, raw_prefix_param text) OWNER TO supabase_storage_admin;

--
-- Name: list_objects_with_delimiter(text, text, text, integer, text, text, text, text, text, timestamp with time zone, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.list_objects_with_delimiter(_bucket_id text, prefix_param text, delimiter_param text, max_keys integer DEFAULT 100, start_after text DEFAULT ''::text, next_token text DEFAULT ''::text, sort_order text DEFAULT 'asc'::text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text, next_token_archived_at timestamp with time zone DEFAULT NULL::timestamp with time zone, next_token_version text DEFAULT ''::text) RETURNS TABLE(name text, id uuid, metadata jsonb, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $_$
DECLARE
    v_peek_name TEXT;
    v_current RECORD;
    v_common_prefix TEXT;

    -- Configuration
    v_is_asc BOOLEAN;
    v_prefix TEXT;
    v_start TEXT;
    v_start_relative TEXT;
    v_upper_bound TEXT;
    v_file_batch_size INT;
    v_version_filter TEXT;

    -- true when noncurrent_versions can return >1 row per name; keeps them
    -- ordered most-recent-first and lets pagination resume mid-key
    v_multi_row BOOLEAN;
    v_name_order TEXT;
    v_exact_range_predicate TEXT;
    v_strict_range_predicate TEXT;
    v_inclusive_range_predicate TEXT;

    -- Seek state for the current name. archived_at is normalized to JavaScript's
    -- millisecond precision and version breaks ties within the same millisecond.
    -- Current rows use 'infinity'; NULL means no tiebreak has been established.
    v_next_seek TEXT;
    v_next_seek_at TIMESTAMPTZ;
    v_next_seek_version TEXT;
    v_next_seek_strict BOOLEAN := false;
    v_cursor_is_folder BOOLEAN;
    v_count INT := 0;
    v_previous_seek TEXT;
    v_previous_seek_at TIMESTAMPTZ;
    v_previous_seek_version TEXT;
    v_previous_count INT;

    -- Dynamic SQL for batch query only
    v_batch_query TEXT;
    v_batch_query_strict TEXT;
    v_delete_marker_peek_query TEXT;
    v_delete_marker_peek_query_strict TEXT;

BEGIN
    -- ========================================================================
    -- INITIALIZATION
    -- ========================================================================
    v_is_asc := lower(coalesce(sort_order, 'asc')) = 'asc';
    v_prefix := coalesce(prefix_param, '');
    v_start := CASE WHEN coalesce(next_token, '') <> '' THEN next_token ELSE coalesce(start_after, '') END;
    v_file_batch_size := LEAST(GREATEST(max_keys * 2, 100), 1000);
    v_next_seek_at := NULL;
    v_next_seek_version := '';

    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'exclude');
    delete_markers := COALESCE(delete_markers, 'exclude');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'exclude';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'exclude';
    END IF;

    v_multi_row := noncurrent_versions IN ('only', 'include');
    v_name_order := CASE WHEN v_is_asc THEN 'ASC' ELSE 'DESC' END;

    v_version_filter := '';
    IF noncurrent_versions = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NULL';
    ELSIF noncurrent_versions = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NOT NULL';
    END IF;
    IF delete_markers = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND NOT o.is_delete_marker';
    ELSIF delete_markers = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.is_delete_marker';
    END IF;

    -- Calculate upper bound for prefix filtering (bytewise, using COLLATE "C")
    IF v_prefix = '' THEN
        v_upper_bound := NULL;
    ELSE
        v_upper_bound := left(v_prefix, -1) || chr(ascii(right(v_prefix, 1)) + 1);
    END IF;

    -- Keep caller-provided cursors inside the requested prefix range.
    IF v_start <> '' AND v_upper_bound IS NOT NULL THEN
        IF v_is_asc THEN
            IF v_start COLLATE "C" < v_prefix COLLATE "C" THEN
                v_start := '';
            ELSIF v_start COLLATE "C" >= v_upper_bound COLLATE "C" THEN
                RETURN;
            END IF;
        ELSE
            IF v_start COLLATE "C" < v_prefix COLLATE "C" THEN
                RETURN;
            ELSIF v_start COLLATE "C" >= v_upper_bound COLLATE "C" THEN
                v_start := '';
            END IF;
        END IF;
    END IF;

    v_start_relative := substring(v_start FROM length(v_prefix) + 1);

    -- Direction affects only the indexed name range and its ordering. Cursor
    -- state transitions and within-key version ordering stay shared.
    IF v_is_asc THEN
        v_exact_range_predicate := 'TRUE';
        v_strict_range_predicate := 'o.name COLLATE "C" > $2';
        v_inclusive_range_predicate := 'o.name COLLATE "C" >= $2';
        IF v_upper_bound IS NOT NULL THEN
            v_exact_range_predicate := 'o.name COLLATE "C" < $3';
            v_strict_range_predicate := v_strict_range_predicate || ' AND o.name COLLATE "C" < $3';
            v_inclusive_range_predicate := v_inclusive_range_predicate || ' AND o.name COLLATE "C" < $3';
        END IF;
    ELSE
        v_exact_range_predicate := 'TRUE';
        v_strict_range_predicate := 'o.name COLLATE "C" < $2';
        v_inclusive_range_predicate := 'o.name COLLATE "C" < $2';
        IF v_prefix <> '' THEN
            v_exact_range_predicate := 'o.name COLLATE "C" >= $3';
            v_strict_range_predicate := v_strict_range_predicate || ' AND o.name COLLATE "C" >= $3';
            v_inclusive_range_predicate := v_inclusive_range_predicate || ' AND o.name COLLATE "C" >= $3';
        END IF;
    END IF;

    -- Build batch query (dynamic SQL - called infrequently, amortized over many rows)
    -- The multi-row order matches the externally serialized cursor exactly:
    -- archived_at at millisecond precision, then version as the final tiebreak.
    --
    -- When v_multi_row, the seek is a keyset tuple comparison ("name > $2 OR
    -- (name = $2 AND tiebreak)") - Postgres won't split that OR into indexable
    -- form (confirmed even with fully literal values), so as one WHERE clause
    -- it forces a full bucket scan filtered row-by-row. Splitting it into two
    -- independently-indexable branches (exact name match with the tiebreak
    -- filter, vs. strictly-past names) combined with UNION ALL lets each
    -- branch keep name as a real index condition; the outer ORDER BY/LIMIT
    -- re-merges them into the same page the single query used to produce.
    IF v_multi_row THEN
        v_batch_query := format(
            $sql$
            SELECT *
            FROM (
                (
                    SELECT o.name, o.id, o.updated_at, o.created_at,
                           o.last_accessed_at, o.metadata, o.version,
                           o.archived_at, o.is_delete_marker, o.is_versioned
                    FROM storage.objects o
                    WHERE o.bucket_id = $1
                      AND o.name COLLATE "C" = $2
                      AND %s
                      AND NOT $7::boolean
                      AND (
                          $5::timestamptz IS NULL
                          OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < $5
                          OR (
                              COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = $5
                              AND COALESCE(o.version, '') > $6
                          )
                      )
                      %s
                    ORDER BY
                        COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC,
                        COALESCE(o.version, '') ASC
                    LIMIT $4
                )
                UNION ALL
                (
                    SELECT o.name, o.id, o.updated_at, o.created_at,
                           o.last_accessed_at, o.metadata, o.version,
                           o.archived_at, o.is_delete_marker, o.is_versioned
                    FROM storage.objects o
                    WHERE o.bucket_id = $1
                      AND %s
                      %s
                    ORDER BY
                        o.name COLLATE "C" %s,
                        COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC,
                        COALESCE(o.version, '') ASC
                    LIMIT $4
                )
            ) sub
            ORDER BY
                sub.name COLLATE "C" %s,
                COALESCE(date_trunc('milliseconds', sub.archived_at), 'infinity'::timestamptz) DESC,
                COALESCE(sub.version, '') ASC
            LIMIT $4
            $sql$,
            v_exact_range_predicate,
            v_version_filter,
            v_strict_range_predicate,
            v_version_filter,
            v_name_order,
            v_name_order
        );
    ELSE
        v_batch_query := format(
            $sql$
            SELECT o.name, o.id, o.updated_at, o.created_at,
                   o.last_accessed_at, o.metadata, o.version,
                   o.archived_at, o.is_delete_marker, o.is_versioned
            FROM storage.objects o
            WHERE o.bucket_id = $1
              AND %s
              %s
            ORDER BY o.name COLLATE "C" %s, o.archived_at DESC
            LIMIT $4
            $sql$,
            v_inclusive_range_predicate,
            v_version_filter,
            v_name_order
        );

        -- Strict counterpart of the query above: used once the single-row
        -- ASC batch advance (below) has left v_next_seek pointing at the
        -- last row already emitted, so an inclusive predicate would
        -- re-match it forever. Only single-row mode ever sets strict mode,
        -- so this variant is never needed when v_multi_row.
        v_batch_query_strict := format(
            $sql$
            SELECT o.name, o.id, o.updated_at, o.created_at,
                   o.last_accessed_at, o.metadata, o.version,
                   o.archived_at, o.is_delete_marker, o.is_versioned
            FROM storage.objects o
            WHERE o.bucket_id = $1
              AND %s
              %s
            ORDER BY o.name COLLATE "C" %s, o.archived_at DESC
            LIMIT $4
            $sql$,
            v_strict_range_predicate,
            v_version_filter,
            v_name_order
        );
    END IF;

    -- The static peek predicates cannot use the partial delete-marker index
    -- once PL/pgSQL switches to a generic plan because whether
    -- is_delete_marker is required remains parameter-dependent. Reuse the
    -- already-specialized batch query with a one-row limit for this sparse
    -- filter so the plan sees a literal `o.is_delete_marker` predicate.
    IF delete_markers = 'only' THEN
        v_delete_marker_peek_query :=
            'SELECT marker_page.name FROM (' || v_batch_query || ') marker_page LIMIT 1';
        IF NOT v_multi_row THEN
            v_delete_marker_peek_query_strict :=
                'SELECT marker_page.name FROM (' || v_batch_query_strict || ') marker_page LIMIT 1';
        END IF;
    END IF;

    -- ========================================================================
    -- SEEK INITIALIZATION: Determine starting position
    -- ========================================================================
    IF v_start = '' THEN
        IF v_is_asc THEN
            v_next_seek := v_prefix;
        ELSE
            -- DESC without cursor performs one specialized initial seek so
            -- partial current-version and delete-marker indexes remain available.
            EXECUTE format(
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1%s%s ORDER BY o.name COLLATE "C" DESC LIMIT 1',
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND o.name COLLATE "C" >= $2 AND o.name COLLATE "C" < $3'
                    ELSE ''
                END,
                v_version_filter
            )
            INTO v_next_seek
            USING _bucket_id, v_prefix, v_upper_bound;

            IF v_next_seek IS NOT NULL THEN
                v_next_seek := v_next_seek || delimiter_param;
            ELSE
                RETURN;
            END IF;
        END IF;
    ELSE
        -- Folder continuation tokens retain their trailing delimiter. A
        -- delimiter-less startAfter is always a literal key boundary.
        v_cursor_is_folder := delimiter_param <> ''
            AND v_start_relative <> ''
            AND right(v_start_relative, length(delimiter_param)) = delimiter_param;

        IF v_cursor_is_folder THEN
            v_next_seek := CASE
                WHEN right(v_start, length(delimiter_param)) = delimiter_param
                    THEN v_start
                ELSE v_start || delimiter_param
            END;
            IF v_is_asc THEN
                v_next_seek := left(v_next_seek, -1)
                    || chr(ascii(right(v_next_seek, 1)) + 1);
            END IF;
            v_next_seek_strict := NOT v_is_asc;
        ELSE
            -- leaf object: when v_multi_row, stay on v_start with the
            -- caller-supplied tiebreak so a page boundary mid-key resumes
            -- that key's remaining rows instead of skipping them. Truncate
            -- to milliseconds like every other v_next_seek_at assignment -
            -- harmless today since object.ts's cursor always round-trips
            -- through JS Date first, but this shouldn't rely on that.
            IF v_multi_row THEN
                v_next_seek := v_start;
                v_next_seek_at := date_trunc('milliseconds', next_token_archived_at);
                v_next_seek_version := coalesce(next_token_version, '');
                v_next_seek_strict := coalesce(next_token, '') = '';
            ELSIF v_is_asc THEN
                v_next_seek := v_start;
                v_next_seek_strict := true;
            ELSE
                v_next_seek := v_start;
            END IF;
        END IF;
    END IF;

    -- ========================================================================
    -- MAIN LOOP: Hybrid peek-then-batch algorithm
    -- Uses STATIC SQL for peek (hot path) and DYNAMIC SQL for batch
    -- ========================================================================
    LOOP
        EXIT WHEN v_count >= max_keys;

        v_previous_seek := v_next_seek;
        v_previous_seek_at := v_next_seek_at;
        v_previous_seek_version := v_next_seek_version;
        v_previous_count := v_count;

        -- STEP 1: PEEK using STATIC SQL (plan cached, very fast)
        -- v_multi_row is branched here (rather than folded into the WHERE
        -- clause as a bound parameter) so each concrete query keeps an
        -- unconditional seek predicate - once PL/pgSQL switches to its
        -- cached generic plan (after 5 calls), a parameter-gated
        -- "(NOT v_multi_row AND name >= $x) OR (v_multi_row AND ...)"
        -- predicate stops the planner from using name as an index
        -- condition at all, degrading every subsequent peek to a full
        -- index scan filtered row-by-row instead of a bounded range scan.
        -- v_multi_row's seek predicate is a keyset tuple comparison
        -- ("name > x OR (name = x AND tiebreak)") - Postgres does not
        -- split this OR into indexable form even with fully literal
        -- values, so it falls back to a full scan filtered row-by-row.
        -- Splitting it into two independently-indexable branches (exact
        -- name match with the tiebreak filter, vs. strictly-past name)
        -- combined with UNION ALL lets each branch keep name as a real
        -- index condition; the outer ORDER BY/LIMIT picks whichever of
        -- the (at most 2) rows sorts first.
        IF delete_markers = 'only' THEN
            EXECUTE CASE WHEN v_next_seek_strict AND NOT v_multi_row
                THEN v_delete_marker_peek_query_strict
                ELSE v_delete_marker_peek_query
            END
                INTO v_peek_name
                USING _bucket_id, v_next_seek,
                    CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix) ELSE v_prefix END,
                    1, v_next_seek_at, v_next_seek_version, v_next_seek_strict;
        ELSIF v_multi_row THEN
            IF v_is_asc THEN
                IF v_upper_bound IS NOT NULL THEN
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND o.name COLLATE "C" < v_upper_bound
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" > v_next_seek AND o.name COLLATE "C" < v_upper_bound
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" ASC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" ASC LIMIT 1;
                ELSE
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" > v_next_seek
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" ASC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" ASC LIMIT 1;
                END IF;
            ELSE
                IF v_upper_bound IS NOT NULL THEN
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND o.name COLLATE "C" >= v_prefix
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" < v_next_seek AND o.name COLLATE "C" >= v_prefix
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" DESC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" DESC LIMIT 1;
                ELSE
                    SELECT sub.name INTO v_peek_name FROM (
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" = v_next_seek
                           AND NOT v_next_seek_strict
                           AND (v_next_seek_at IS NULL
                                OR COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) < v_next_seek_at
                                OR (COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) = v_next_seek_at
                                    AND COALESCE(o.version, '') > v_next_seek_version))
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY COALESCE(date_trunc('milliseconds', o.archived_at), 'infinity'::timestamptz) DESC, COALESCE(o.version, '') ASC LIMIT 1)
                        UNION ALL
                        (SELECT o.name FROM storage.objects o
                         WHERE o.bucket_id = _bucket_id AND o.name COLLATE "C" < v_next_seek
                           AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                           AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                           AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                           AND (delete_markers != 'only' OR o.is_delete_marker)
                         ORDER BY o.name COLLATE "C" DESC LIMIT 1)
                    ) sub ORDER BY sub.name COLLATE "C" DESC LIMIT 1;
                END IF;
            END IF;
        ELSE
            -- Single-row mode is always noncurrent_versions='exclude'. Keep
            -- this predicate literal so generic plans use the current index.
            IF v_is_asc THEN
                IF v_next_seek_strict AND v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" > v_next_seek
                      AND o.name COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                ELSIF v_next_seek_strict THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" > v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                ELSIF v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" >= v_next_seek
                      AND o.name COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                ELSE
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" >= v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" ASC LIMIT 1;
                END IF;
            ELSE
                IF v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" < v_next_seek
                      AND o.name COLLATE "C" >= v_prefix
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" DESC LIMIT 1;
                ELSE
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = _bucket_id
                      AND o.name COLLATE "C" < v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                      AND (delete_markers != 'only' OR o.is_delete_marker)
                    ORDER BY o.name COLLATE "C" DESC LIMIT 1;
                END IF;
            END IF;
        END IF;

        EXIT WHEN v_peek_name IS NULL;

        -- STEP 2: Check if this is a FOLDER or FILE
        v_common_prefix := storage.get_common_prefix(v_peek_name, v_prefix, delimiter_param);

        IF v_common_prefix IS NOT NULL THEN
            -- FOLDER: Emit and skip to next folder (no heap access needed)
            name := v_common_prefix;
            id := NULL;
            updated_at := NULL;
            created_at := NULL;
            last_accessed_at := NULL;
            metadata := NULL;
            version := NULL;
            archived_at := NULL;
            is_delete_marker := NULL;
            is_versioned := NULL;
            RETURN NEXT;
            v_count := v_count + 1;

            -- Advance seek past the folder range
            IF v_is_asc THEN
                v_next_seek := left(v_common_prefix, -1)
                    || chr(ascii(right(v_common_prefix, 1)) + 1);
            ELSE
                v_next_seek := v_common_prefix;
            END IF;
            v_next_seek_at := NULL;
            v_next_seek_version := '';
            v_next_seek_strict := NOT v_is_asc;
        ELSE
            -- FILE: Batch fetch using DYNAMIC SQL (overhead amortized over many rows)
            -- For ASC: upper_bound is the exclusive upper limit (< condition)
            -- For DESC: prefix is the inclusive lower limit (>= condition)
            FOR v_current IN EXECUTE CASE WHEN v_next_seek_strict AND NOT v_multi_row THEN v_batch_query_strict ELSE v_batch_query END
                USING _bucket_id, v_next_seek,
                CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix) ELSE v_prefix END, v_file_batch_size, v_next_seek_at, v_next_seek_version,
                v_next_seek_strict
            LOOP
                v_common_prefix := storage.get_common_prefix(v_current.name, v_prefix, delimiter_param);

                IF v_common_prefix IS NOT NULL THEN
                    -- Hit a folder: exit batch, let peek handle it. Reset
                    -- strict mode too it may have been set by an earlier
                    -- row in this same batch (see the single-row ASC advance
                    -- below), and v_next_seek here is the folder-triggering
                    -- row's own name, which the next peek must find inclusively.
                    v_next_seek := CASE
                        WHEN v_is_asc THEN v_current.name
                        ELSE v_current.name || delimiter_param
                    END;
                    v_next_seek_at := NULL;
                    v_next_seek_version := '';
                    v_next_seek_strict := false;
                    EXIT;
                END IF;

                -- Emit file
                name := v_current.name;
                id := v_current.id;
                updated_at := v_current.updated_at;
                created_at := v_current.created_at;
                last_accessed_at := v_current.last_accessed_at;
                metadata := v_current.metadata;
                version := v_current.version;
                archived_at := v_current.archived_at;
                is_delete_marker := v_current.is_delete_marker;
                is_versioned := v_current.is_versioned;
                RETURN NEXT;
                v_count := v_count + 1;

                -- when v_multi_row, stay on this name and record its
                -- archived_at as the new tiebreak so remaining rows for the
                -- same key are picked up before moving to the next name
                IF v_multi_row THEN
                    v_next_seek := v_current.name;
                    v_next_seek_at := COALESCE(date_trunc('milliseconds', v_current.archived_at), 'infinity'::timestamptz);
                    v_next_seek_version := COALESCE(v_current.version, '');
                    v_next_seek_strict := false;
                ELSIF v_is_asc THEN
                    -- Appending the delimiter as a fake lexical successor
                    -- would skip a real key like `name || '!'` (or any
                    -- character sorting below the delimiter), which sorts
                    -- between `name` and `name || delimiter`. Track the real
                    -- name and mark the next comparison strict instead.
                    v_next_seek := v_current.name;
                    v_next_seek_strict := true;
                ELSE
                    v_next_seek := v_current.name;
                END IF;

                EXIT WHEN v_count >= max_keys;
            END LOOP;
        END IF;

        IF v_count = v_previous_count
           AND v_next_seek IS NOT DISTINCT FROM v_previous_seek
           AND v_next_seek_at IS NOT DISTINCT FROM v_previous_seek_at
           AND v_next_seek_version IS NOT DISTINCT FROM v_previous_seek_version THEN
            RAISE EXCEPTION 'storage.list_objects_with_delimiter made no progress at seek (%, %, %)',
                v_next_seek, v_next_seek_at, v_next_seek_version;
        END IF;
    END LOOP;
END;
$_$;


ALTER FUNCTION storage.list_objects_with_delimiter(_bucket_id text, prefix_param text, delimiter_param text, max_keys integer, start_after text, next_token text, sort_order text, noncurrent_versions text, delete_markers text, next_token_archived_at timestamp with time zone, next_token_version text) OWNER TO supabase_storage_admin;

--
-- Name: operation(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.operation() RETURNS text
    LANGUAGE plpgsql STABLE
    AS $$
BEGIN
    RETURN current_setting('storage.operation', true);
END;
$$;


ALTER FUNCTION storage.operation() OWNER TO supabase_storage_admin;

--
-- Name: protect_active_lifecycle_states(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.protect_active_lifecycle_states() RETURNS trigger
    LANGUAGE plpgsql SECURITY DEFINER
    SET search_path TO 'pg_catalog'
    AS $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM storage.bucket_lifecycle_states
        WHERE bucket_id = OLD.id
          AND NOT (
              continuation IS NULL
              OR (continuation @> '{"continuationVersion": 1}' AND continuation #> '{batch,inFlight}' IS NULL)
          )
    ) THEN
        RAISE EXCEPTION 'update or delete on table "buckets" violates foreign key constraint "bucket_lifecycle_states_bucket_id_fkey" on table "bucket_lifecycle_states"'
            USING ERRCODE = 'foreign_key_violation',
                  DETAIL = format('Key (id)=(%s) is still referenced from table "bucket_lifecycle_states".', OLD.id),
                  SCHEMA = 'storage',
                  TABLE = 'bucket_lifecycle_states',
                  CONSTRAINT = 'bucket_lifecycle_states_bucket_id_fkey';
    END IF;
    RETURN OLD;
END;
$$;


ALTER FUNCTION storage.protect_active_lifecycle_states() OWNER TO supabase_storage_admin;

--
-- Name: protect_bucket_control_columns(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.protect_bucket_control_columns() RETURNS trigger
    LANGUAGE plpgsql
    SET search_path TO 'pg_catalog'
    AS $$
DECLARE
  configuration_changed boolean;
BEGIN
  IF TG_OP = 'INSERT' THEN
    IF NEW.lifecycle_configuration IS NOT NULL
       OR NEW.lifecycle_configuration_generation IS NOT NULL THEN
      IF NOT pg_has_role(current_user, TG_ARGV[0], 'MEMBER') THEN
        RAISE EXCEPTION 'only members of the configured storage service role may insert lifecycle policy state'
          USING ERRCODE = '42501',
                HINT = format(
                  'Insert with both lifecycle columns NULL and configure lifecycle through the Storage API afterward, or insert as a member of %I.',
                  TG_ARGV[0]
                );
      END IF;
    END IF;

    RETURN NEW;
  END IF;

  configuration_changed =
    OLD.lifecycle_configuration IS DISTINCT FROM NEW.lifecycle_configuration
    OR OLD.lifecycle_configuration_generation IS DISTINCT FROM NEW.lifecycle_configuration_generation;

  IF NOT configuration_changed THEN
    RETURN NEW;
  END IF;

  IF NEW.type IS DISTINCT FROM 'STANDARD' THEN
    RAISE EXCEPTION 'bucket versioning and lifecycle controls require a Standard bucket'
      USING ERRCODE = '0A000';
  END IF;

  IF NEW.lifecycle_configuration IS NULL
     AND NEW.lifecycle_configuration_generation IS NULL THEN
    RETURN NEW;
  END IF;

  IF NEW.lifecycle_configuration IS NULL
     OR NEW.lifecycle_configuration_generation IS NULL
     OR OLD.lifecycle_configuration IS NOT DISTINCT FROM NEW.lifecycle_configuration
     OR OLD.lifecycle_configuration_generation IS NOT DISTINCT FROM NEW.lifecycle_configuration_generation THEN
    RAISE EXCEPTION 'a changed lifecycle policy requires a new non-null generation'
      USING ERRCODE = '22023';
  END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION storage.protect_bucket_control_columns() OWNER TO supabase_storage_admin;

--
-- Name: protect_delete(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.protect_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Check if storage.allow_delete_query is set to 'true'
    IF COALESCE(current_setting('storage.allow_delete_query', true), 'false') != 'true' THEN
        RAISE EXCEPTION 'Direct deletion from storage tables is not allowed. Use the Storage API instead.'
            USING HINT = 'This prevents accidental data loss from orphaned objects.',
                  ERRCODE = '42501';
    END IF;
    RETURN NULL;
END;
$$;


ALTER FUNCTION storage.protect_delete() OWNER TO supabase_storage_admin;

--
-- Name: reconcile_lifecycle_state(text, boolean); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.reconcile_lifecycle_state(target_bucket_id text, policy_changed boolean) RETURNS void
    LANGUAGE plpgsql SECURITY DEFINER
    SET search_path TO 'pg_catalog'
    AS $$
DECLARE
    bucket storage.buckets%ROWTYPE;
    active boolean;
    idle boolean;
    state_generation uuid;
BEGIN
    SELECT * INTO bucket FROM storage.buckets WHERE id = target_bucket_id FOR NO KEY UPDATE;
    IF NOT FOUND THEN
        RETURN;
    END IF;

    SELECT continuation IS NULL OR (
               continuation @> '{"continuationVersion": 1}'
               AND continuation #> '{batch,inFlight}' IS NULL
           ), generation
    INTO idle, state_generation
    FROM storage.bucket_lifecycle_states
    WHERE bucket_id = target_bucket_id
    FOR UPDATE;

    IF bucket.lifecycle_configuration IS NULL THEN
        DELETE FROM storage.bucket_lifecycle_states
        WHERE bucket_id = target_bucket_id
          AND idle IS TRUE;

        UPDATE storage.bucket_lifecycle_states
        SET next_run_at = clock_timestamp(), updated_at = clock_timestamp()
        WHERE bucket_id = target_bucket_id
          AND idle IS NOT TRUE;
        RETURN;
    END IF;

    active := bucket.versioning_status <> 'DISABLED'
        AND bucket.lifecycle_configuration @> '{"rules":[{"status":"Enabled"}]}'::jsonb;

    -- The state row records the generation it was reconciled with so callers need not detect
    -- policy changes. Rows without a recorded generation keep their work.
    policy_changed := policy_changed OR (
        state_generation IS NOT NULL AND state_generation <> bucket.lifecycle_configuration_generation
    );

    INSERT INTO storage.bucket_lifecycle_states AS state (bucket_id, generation, next_run_at)
    VALUES (bucket.id, bucket.lifecycle_configuration_generation, CASE WHEN active THEN clock_timestamp() END)
    ON CONFLICT (bucket_id) DO UPDATE
    SET next_run_at = CASE
            WHEN policy_changed THEN
                CASE WHEN active OR idle IS NOT TRUE THEN clock_timestamp() END
            ELSE EXCLUDED.next_run_at
        END,
        generation = EXCLUDED.generation,
        claim_id = CASE WHEN policy_changed AND idle IS TRUE
                        THEN NULL ELSE state.claim_id END,
        claim_until = CASE WHEN policy_changed AND idle IS TRUE
                           THEN NULL ELSE state.claim_until END,
        continuation = CASE WHEN policy_changed AND idle IS TRUE
                            THEN NULL ELSE state.continuation END,
        failure_count = CASE WHEN policy_changed AND idle IS TRUE
                             THEN 0 ELSE state.failure_count END,
        last_error = CASE WHEN policy_changed AND idle IS TRUE
                          THEN NULL ELSE state.last_error END,
        updated_at = clock_timestamp()
    WHERE policy_changed OR (state.next_run_at IS NULL AND EXCLUDED.next_run_at IS NOT NULL);
END;
$$;


ALTER FUNCTION storage.reconcile_lifecycle_state(target_bucket_id text, policy_changed boolean) OWNER TO supabase_storage_admin;

--
-- Name: search(text, text, integer, integer, integer, text, text, text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.search(prefix text, bucketname text, limits integer DEFAULT 100, levels integer DEFAULT 1, offsets integer DEFAULT 0, search text DEFAULT ''::text, sortcolumn text DEFAULT 'name'::text, sortorder text DEFAULT 'asc'::text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text) RETURNS TABLE(name text, id uuid, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, metadata jsonb, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $_$
DECLARE
    v_peek_name TEXT;
    v_current RECORD;
    v_common_prefix TEXT;
    v_delimiter CONSTANT TEXT := '/';

    -- Configuration
    v_limit INT;
    v_prefix TEXT;
    v_prefix_lower TEXT;
    v_prefix_len INT;
    v_prefix_start INT;
    v_combined_levels INT;
    v_is_asc BOOLEAN;
    v_order_by TEXT;
    v_sort_order TEXT;
    v_upper_bound TEXT;
    v_file_batch_size INT;
    v_version_filter TEXT;
    v_multi_row BOOLEAN;

    -- Dynamic SQL for batch query only
    v_batch_query TEXT;
    v_delete_marker_peek_query TEXT;
    v_delete_marker_peek_query_strict TEXT;

    -- Seek state
    v_next_seek TEXT;
    v_next_seek_at TIMESTAMPTZ;
    v_next_seek_version TEXT;
    v_next_seek_strict BOOLEAN := false;
    v_count INT := 0;
    v_skipped INT := 0;
    v_previous_seek TEXT;
    v_previous_seek_at TIMESTAMPTZ;
    v_previous_seek_version TEXT;
    v_previous_count INT;
    v_previous_skipped INT;
BEGIN
    -- ========================================================================
    -- INITIALIZATION
    -- ========================================================================
    v_limit := LEAST(coalesce(limits, 100), 1500);
    v_prefix := coalesce(prefix, '') || coalesce(search, '');
    v_prefix_lower := lower(v_prefix);
    v_prefix_len := length(coalesce(prefix, ''));
    v_prefix_start := coalesce(array_length(string_to_array(coalesce(prefix, ''), v_delimiter), 1), 1);
    v_combined_levels := coalesce(array_length(string_to_array(v_prefix, v_delimiter), 1), 1);
    v_is_asc := lower(coalesce(sortorder, 'asc')) = 'asc';
    v_file_batch_size := LEAST(GREATEST(v_limit * 2, 100), 1000);
    v_next_seek_at := NULL;
    v_next_seek_version := '';

    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'exclude');
    delete_markers := COALESCE(delete_markers, 'exclude');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'exclude';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'exclude';
    END IF;

    v_multi_row := noncurrent_versions IN ('only', 'include');

    v_version_filter := '';
    IF noncurrent_versions = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NULL';
    ELSIF noncurrent_versions = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.archived_at IS NOT NULL';
    END IF;
    IF delete_markers = 'exclude' THEN
        v_version_filter := v_version_filter || ' AND NOT o.is_delete_marker';
    ELSIF delete_markers = 'only' THEN
        v_version_filter := v_version_filter || ' AND o.is_delete_marker';
    END IF;

    -- Validate sort column
    CASE lower(coalesce(sortcolumn, 'name'))
        WHEN 'name' THEN v_order_by := 'name';
        WHEN 'updated_at' THEN v_order_by := 'updated_at';
        WHEN 'created_at' THEN v_order_by := 'created_at';
        WHEN 'last_accessed_at' THEN v_order_by := 'last_accessed_at';
        ELSE v_order_by := 'name';
    END CASE;

    v_sort_order := CASE WHEN v_is_asc THEN 'asc' ELSE 'desc' END;

    -- ========================================================================
    -- NON-NAME SORTING: Use path_tokens approach
    -- ========================================================================
    IF v_order_by != 'name' THEN
        RETURN QUERY EXECUTE format(
            $sql$
            WITH folders AS (
                SELECT array_to_string(path_tokens[$1:$2], '/') AS folder
                FROM storage.objects
                WHERE objects.name ILIKE $3 || '%%'
                  AND bucket_id = $4
                  AND array_length(objects.path_tokens, 1) <> $2
                  AND ($7 != 'exclude' OR objects.archived_at IS NULL)
                  AND ($7 != 'only' OR objects.archived_at IS NOT NULL)
                  AND ($8 != 'exclude' OR NOT objects.is_delete_marker)
                  AND ($8 != 'only' OR objects.is_delete_marker)
                GROUP BY folder
                ORDER BY folder %s
            )
            (SELECT folder AS "name",
                   NULL::uuid AS id,
                   NULL::timestamptz AS updated_at,
                   NULL::timestamptz AS created_at,
                   NULL::timestamptz AS last_accessed_at,
                   NULL::jsonb AS metadata,
                   NULL::text AS version,
                   NULL::timestamptz AS archived_at,
                   NULL::boolean AS is_delete_marker,
                   NULL::boolean AS is_versioned FROM folders)
            UNION ALL
            (SELECT array_to_string(path_tokens[$1:$2], '/') AS "name",
                   id, updated_at, created_at, last_accessed_at, metadata,
                   version, archived_at, is_delete_marker, is_versioned
             FROM storage.objects
             WHERE objects.name ILIKE $3 || '%%'
               AND bucket_id = $4
               AND array_length(objects.path_tokens, 1) = $2
               AND ($7 != 'exclude' OR objects.archived_at IS NULL)
               AND ($7 != 'only' OR objects.archived_at IS NOT NULL)
               AND ($8 != 'exclude' OR NOT objects.is_delete_marker)
               AND ($8 != 'only' OR objects.is_delete_marker)
             -- name, then version, as tiebreaks so two versions of the same
             -- key tying on the sort column still sort deterministically
             ORDER BY %I %s, name COLLATE "C" %s, COALESCE(version, '') %s)
            LIMIT $5 OFFSET $6
            $sql$, v_sort_order, v_order_by, v_sort_order, v_sort_order, v_sort_order
        ) USING v_prefix_start, v_combined_levels, v_prefix, bucketname, v_limit, offsets, noncurrent_versions, delete_markers;
        RETURN;
    END IF;

    -- ========================================================================
    -- NAME SORTING: Hybrid skip-scan with batch optimization
    -- ========================================================================

    -- Calculate upper bound for prefix filtering
    IF v_prefix_lower = '' THEN
        v_upper_bound := NULL;
    ELSIF right(v_prefix_lower, 1) = v_delimiter THEN
        v_upper_bound := left(v_prefix_lower, -1) || chr(ascii(v_delimiter) + 1);
    ELSE
        v_upper_bound := left(v_prefix_lower, -1) || chr(ascii(right(v_prefix_lower, 1)) + 1);
    END IF;

    -- Build a resume-safe batch query. The exact-name branch returns remaining
    -- versions after the current (archived_at, version) boundary; the strict
    -- name branch returns subsequent keys. UNION ALL keeps both predicates
    -- independently indexable.
    IF v_is_asc THEN
        IF v_upper_bound IS NOT NULL THEN
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" > $2 AND lower(o.name) COLLATE "C" < $3' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" ASC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        ELSE
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" > $2' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" ASC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        END IF;
    ELSE
        IF v_upper_bound IS NOT NULL THEN
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" < $2 AND lower(o.name) COLLATE "C" >= $3' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" DESC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" DESC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        ELSE
            v_batch_query := 'SELECT * FROM (' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" = $2 AND ($5::timestamptz IS NULL OR COALESCE(o.archived_at, ''infinity''::timestamptz) < $5 OR (COALESCE(o.archived_at, ''infinity''::timestamptz) = $5 AND COALESCE(o.version, '''') > $6))' ||
                v_version_filter || ' ORDER BY COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4) UNION ALL ' ||
                '(SELECT o.name, o.id, o.updated_at, o.created_at, o.last_accessed_at, o.metadata, o.version, o.archived_at, o.is_delete_marker, o.is_versioned FROM storage.objects o ' ||
                'WHERE o.bucket_id = $1 AND lower(o.name) COLLATE "C" < $2' || v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" DESC, COALESCE(o.archived_at, ''infinity''::timestamptz) DESC, COALESCE(o.version, '''') ASC LIMIT $4)' ||
                ') sub ORDER BY lower(sub.name) COLLATE "C" DESC, COALESCE(sub.archived_at, ''infinity''::timestamptz) DESC, COALESCE(sub.version, '''') ASC LIMIT $4';
        END IF;
    END IF;

    -- Keep the delete-marker predicate literal so the cached generic
    -- plan can use idx_objects_delete_markers during the main-loop peek.
    IF delete_markers = 'only' THEN
        IF v_multi_row THEN
            v_delete_marker_peek_query :=
                'SELECT marker_page.name FROM (' || v_batch_query || ') marker_page LIMIT 1';
        ELSIF v_is_asc THEN
            -- Two separate literal query strings, not one gated by a bound
            -- boolean: folding "$n AND op1 OR NOT $n AND op2" into a single
            -- query defeats the generic plan's ability to push either
            -- comparison into the index. Branching in PL/pgSQL control flow
            -- instead keeps each query's index condition intact.
            v_delete_marker_peek_query :=
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1 ' ||
                'AND lower(o.name) COLLATE "C" >= $2' ||
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND lower(o.name) COLLATE "C" < $3'
                    ELSE ''
                END ||
                v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1';
            -- Strict variant: used once the single-row ASC batch advance
            -- (below) has left v_next_seek pointing at the last row already
            -- emitted, so a plain >= would re-match it forever.
            v_delete_marker_peek_query_strict :=
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1 ' ||
                'AND lower(o.name) COLLATE "C" > $2' ||
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND lower(o.name) COLLATE "C" < $3'
                    ELSE ''
                END ||
                v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1';
        ELSE
            v_delete_marker_peek_query :=
                'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1 ' ||
                'AND lower(o.name) COLLATE "C" < $2' ||
                CASE WHEN v_upper_bound IS NOT NULL
                    THEN ' AND lower(o.name) COLLATE "C" >= $3'
                    ELSE ''
                END ||
                v_version_filter ||
                ' ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1';
        END IF;
    END IF;

    -- Initialize seek position
    IF v_is_asc THEN
        v_next_seek := v_prefix_lower;
    ELSE
        -- DESC performs one specialized initial seek so partial current-version
        -- and delete-marker indexes remain available.
        EXECUTE format(
            'SELECT o.name FROM storage.objects o WHERE o.bucket_id = $1%s%s ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1',
            CASE WHEN v_upper_bound IS NOT NULL
                THEN ' AND lower(o.name) COLLATE "C" >= $2 AND lower(o.name) COLLATE "C" < $3'
                ELSE ''
            END,
            v_version_filter
        )
        INTO v_peek_name
        USING bucketname, v_prefix_lower, v_upper_bound;

        IF v_peek_name IS NOT NULL THEN
            v_next_seek := lower(v_peek_name) || v_delimiter;
        ELSE
            RETURN;
        END IF;
    END IF;

    -- ========================================================================
    -- MAIN LOOP: Hybrid peek-then-batch algorithm
    -- Uses STATIC SQL for peek (hot path) and DYNAMIC SQL for batch and
    -- the delete-marker-only path
    -- ========================================================================
    LOOP
        EXIT WHEN v_count >= v_limit;

        v_previous_seek := v_next_seek;
        v_previous_seek_at := v_next_seek_at;
        v_previous_seek_version := v_next_seek_version;
        v_previous_count := v_count;
        v_previous_skipped := v_skipped;

        -- STEP 1: PEEK
        v_peek_name := NULL;
        IF delete_markers = 'only' THEN
            EXECUTE CASE WHEN v_next_seek_strict
                THEN v_delete_marker_peek_query_strict
                ELSE v_delete_marker_peek_query
            END
                INTO v_peek_name
                USING bucketname, v_next_seek,
                    CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix_lower) ELSE v_prefix_lower END,
                    1, v_next_seek_at, v_next_seek_version;
        ELSIF v_multi_row AND v_next_seek_at IS NOT NULL THEN
            SELECT o.name INTO v_peek_name
            FROM storage.objects o
            WHERE o.bucket_id = bucketname
              AND lower(o.name) COLLATE "C" = v_next_seek
              AND (COALESCE(o.archived_at, 'infinity'::timestamptz) < v_next_seek_at
                   OR (COALESCE(o.archived_at, 'infinity'::timestamptz) = v_next_seek_at
                       AND COALESCE(o.version, '') > v_next_seek_version))
              AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
              AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
              AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
              AND (delete_markers != 'only' OR o.is_delete_marker)
            ORDER BY COALESCE(o.archived_at, 'infinity'::timestamptz) DESC,
                     COALESCE(o.version, '') ASC
            LIMIT 1;

            -- The current key is exhausted. Clear its version boundary and
            -- make the following ASC name peek strict. Appending '/' is not a
            -- valid lexical successor because keys ending in characters such
            -- as '!' sort between the exhausted name and name || '/'.
            IF v_peek_name IS NULL THEN
                IF v_is_asc THEN
                    v_next_seek_strict := true;
                END IF;
                v_next_seek_at := NULL;
                v_next_seek_version := '';
            END IF;
        END IF;

        -- Single-row mode is always noncurrent_versions='exclude'. Keep the
        -- current-row predicate literal so generic plans use the current index.
        IF delete_markers != 'only' AND v_peek_name IS NULL AND NOT v_multi_row THEN
            IF v_is_asc THEN
                IF v_next_seek_strict AND v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                ELSIF v_next_seek_strict THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                ELSIF v_upper_bound IS NOT NULL THEN
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                ELSE
                    SELECT o.name INTO v_peek_name FROM storage.objects o
                    WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek
                      AND o.archived_at IS NULL
                      AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                    ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
                END IF;
            ELSIF v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek AND lower(o.name) COLLATE "C" >= v_prefix_lower
                  AND o.archived_at IS NULL
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            ELSE
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek
                  AND o.archived_at IS NULL
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            END IF;
        ELSIF delete_markers != 'only' AND v_peek_name IS NULL AND v_is_asc THEN
            IF v_next_seek_strict AND v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            ELSIF v_next_seek_strict THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" > v_next_seek
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            ELSIF v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek AND lower(o.name) COLLATE "C" < v_upper_bound
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            ELSE
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" >= v_next_seek
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" ASC LIMIT 1;
            END IF;
        ELSIF delete_markers != 'only' AND v_peek_name IS NULL THEN
            IF v_upper_bound IS NOT NULL THEN
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek AND lower(o.name) COLLATE "C" >= v_prefix_lower
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            ELSE
                SELECT o.name INTO v_peek_name FROM storage.objects o
                WHERE o.bucket_id = bucketname AND lower(o.name) COLLATE "C" < v_next_seek
                  AND (noncurrent_versions != 'exclude' OR o.archived_at IS NULL)
                  AND (noncurrent_versions != 'only' OR o.archived_at IS NOT NULL)
                  AND (delete_markers != 'exclude' OR NOT o.is_delete_marker)
                  AND (delete_markers != 'only' OR o.is_delete_marker)
                ORDER BY lower(o.name) COLLATE "C" DESC LIMIT 1;
            END IF;
        END IF;

        EXIT WHEN v_peek_name IS NULL;

        -- If the peek landed on a different key than we were tracking, any
        -- version boundary belongs to the OLD key and must not leak into the
        -- new one - e.g. the deleteMarkers='only' peek doesn't know or care
        -- whether it's continuing the same key or jumping to a new one, so
        -- it never clears these itself.
        IF lower(v_peek_name) IS DISTINCT FROM v_next_seek THEN
            v_next_seek_at := NULL;
            v_next_seek_version := '';
        END IF;

        -- The peek is authoritative for the next key to process. This is
        -- especially important after exhausting a multi-version key: the
        -- version boundary has been cleared, so executing the batch against
        -- a stale v_next_seek would replay every version of that old key.
        v_next_seek := lower(v_peek_name);
        v_next_seek_strict := false;

        -- STEP 2: Check if this is a FOLDER or FILE
        v_common_prefix := storage.get_common_prefix(lower(v_peek_name), v_prefix_lower, v_delimiter);

        IF v_common_prefix IS NOT NULL THEN
            -- FOLDER: Handle offset, emit if needed, skip to next folder
            IF v_skipped < offsets THEN
                v_skipped := v_skipped + 1;
            ELSE
                name := substring(rtrim(storage.get_common_prefix(v_peek_name, v_prefix, v_delimiter), v_delimiter) from v_prefix_len + 1);
                id := NULL;
                updated_at := NULL;
                created_at := NULL;
                last_accessed_at := NULL;
                metadata := NULL;
                version := NULL;
                archived_at := NULL;
                is_delete_marker := NULL;
                is_versioned := NULL;
                RETURN NEXT;
                v_count := v_count + 1;
            END IF;

            -- Advance seek past the folder range
            IF v_is_asc THEN
                v_next_seek := lower(left(v_common_prefix, -1)) || chr(ascii(v_delimiter) + 1);
            ELSE
                v_next_seek := lower(v_common_prefix);
            END IF;
            v_next_seek_at := NULL;
            v_next_seek_version := '';
        ELSE
            -- FILE: Batch fetch using DYNAMIC SQL (overhead amortized over many rows)
            -- For ASC: upper_bound is the exclusive upper limit (< condition)
            -- For DESC: prefix_lower is the inclusive lower limit (>= condition)
            FOR v_current IN EXECUTE v_batch_query
                USING bucketname, v_next_seek,
                    CASE WHEN v_is_asc THEN COALESCE(v_upper_bound, v_prefix_lower) ELSE v_prefix_lower END, v_file_batch_size,
                    v_next_seek_at, v_next_seek_version
            LOOP
                v_common_prefix := storage.get_common_prefix(lower(v_current.name), v_prefix_lower, v_delimiter);

                IF v_common_prefix IS NOT NULL THEN
                    -- Hit a folder: exit batch, let peek handle it. Reset
                    -- strict mode too - it may have been set by an earlier
                    -- row in this same batch (see the single-row ASC advance
                    -- below), and v_next_seek here is the folder-triggering
                    -- row's own name, which the next peek must find inclusively.
                    v_next_seek := CASE
                        WHEN v_is_asc THEN lower(v_current.name)
                        ELSE lower(v_current.name) || v_delimiter
                    END;
                    v_next_seek_at := NULL;
                    v_next_seek_version := '';
                    v_next_seek_strict := false;
                    EXIT;
                END IF;

                -- Handle offset skipping
                IF v_skipped < offsets THEN
                    v_skipped := v_skipped + 1;
                ELSE
                    -- Emit file
                    name := substring(v_current.name from v_prefix_len + 1);
                    id := v_current.id;
                    updated_at := v_current.updated_at;
                    created_at := v_current.created_at;
                    last_accessed_at := v_current.last_accessed_at;
                    metadata := v_current.metadata;
                    version := v_current.version;
                    archived_at := v_current.archived_at;
                    is_delete_marker := v_current.is_delete_marker;
                    is_versioned := v_current.is_versioned;
                    RETURN NEXT;
                    v_count := v_count + 1;
                END IF;

                -- Multi-row mode must remain on this key until all of its
                -- versions have crossed the internal batch boundary.
                IF v_multi_row THEN
                    v_next_seek := lower(v_current.name);
                    v_next_seek_at := COALESCE(v_current.archived_at, 'infinity'::timestamptz);
                    v_next_seek_version := COALESCE(v_current.version, '');
                ELSIF v_is_asc THEN
                    -- Appending the delimiter as a fake lexical successor would
                    -- skip a real key like `name || '!'` (or any character
                    -- sorting below the delimiter), which sorts between `name`
                    -- and `name || delimiter`. Track the real name and mark the
                    -- next comparison strict instead - same fix as the
                    -- exhausted-key case above.
                    v_next_seek := lower(v_current.name);
                    v_next_seek_strict := true;
                ELSE
                    v_next_seek := lower(v_current.name);
                END IF;

                EXIT WHEN v_count >= v_limit;
            END LOOP;
        END IF;

        IF v_count = v_previous_count
           AND v_skipped = v_previous_skipped
           AND v_next_seek IS NOT DISTINCT FROM v_previous_seek
           AND v_next_seek_at IS NOT DISTINCT FROM v_previous_seek_at
           AND v_next_seek_version IS NOT DISTINCT FROM v_previous_seek_version THEN
            RAISE EXCEPTION 'storage.search made no progress at seek (%, %, %)',
                v_next_seek, v_next_seek_at, v_next_seek_version;
        END IF;
    END LOOP;
END;
$_$;


ALTER FUNCTION storage.search(prefix text, bucketname text, limits integer, levels integer, offsets integer, search text, sortcolumn text, sortorder text, noncurrent_versions text, delete_markers text) OWNER TO supabase_storage_admin;

--
-- Name: search_by_timestamp(text, text, integer, integer, text, text, text, text, text, text, text); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.search_by_timestamp(p_prefix text, p_bucket_id text, p_limit integer, p_level integer, p_start_after text, p_sort_order text, p_sort_column text, p_sort_column_after text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text, p_start_after_version text DEFAULT ''::text) RETURNS TABLE(key text, name text, id uuid, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, metadata jsonb, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $_$
DECLARE
    v_cursor_op text;
    v_query text;
    v_prefix text;
    v_prefix_pattern text;
    v_sort_order text;
    v_sort_column text;
    v_version_tiebreak text;
BEGIN
    v_prefix := coalesce(p_prefix, '');
    -- Keep the raw prefix for common-prefix calculations and escape only LIKE metacharacters.
    v_prefix_pattern := replace(v_prefix, chr(92), chr(92) || chr(92));
    v_prefix_pattern := replace(v_prefix_pattern, '%', chr(92) || '%');
    v_prefix_pattern := replace(v_prefix_pattern, '_', chr(92) || '_');

    -- COALESCE first: NULL NOT IN (...) evaluates to NULL (not TRUE), so a
    -- bare NOT IN check silently leaves an explicit NULL argument unreset.
    noncurrent_versions := COALESCE(noncurrent_versions, 'exclude');
    delete_markers := COALESCE(delete_markers, 'exclude');
    IF noncurrent_versions NOT IN ('exclude', 'only', 'include') THEN
        noncurrent_versions := 'exclude';
    END IF;
    IF delete_markers NOT IN ('exclude', 'only', 'include') THEN
        delete_markers := 'exclude';
    END IF;

    -- $9 is only populated in multi-row mode; it's always '' otherwise, so
    -- only use each row's real version as a tiebreak in multi-row mode.
    v_version_tiebreak := CASE WHEN noncurrent_versions IN ('only', 'include') THEN 'COALESCE(version, '''')' ELSE '''''' END;

    -- Defense-in-depth: this function is independently reachable and must
    -- not trust p_sort_order/p_sort_column to already be validated by a
    -- caller. Normalize to the same strict allow-list storage.search_v2
    -- uses before interpolating anything into dynamic SQL below.
    v_sort_order := lower(coalesce(p_sort_order, 'asc'));
    IF v_sort_order NOT IN ('asc', 'desc') THEN
        v_sort_order := 'asc';
    END IF;

    v_sort_column := lower(coalesce(p_sort_column, 'updated_at'));
    IF v_sort_column NOT IN ('updated_at', 'created_at') THEN
        v_sort_column := 'updated_at';
    END IF;

    IF v_sort_order = 'asc' THEN
        v_cursor_op := '>';
    ELSE
        v_cursor_op := '<';
    END IF;

    v_query := format($sql$
        WITH raw_objects AS (
            SELECT
                o.name AS obj_name,
                o.id AS obj_id,
                o.updated_at AS obj_updated_at,
                o.created_at AS obj_created_at,
                o.last_accessed_at AS obj_last_accessed_at,
                o.metadata AS obj_metadata,
                o.version AS obj_version,
                o.archived_at AS obj_archived_at,
                o.is_delete_marker AS obj_is_delete_marker,
                o.is_versioned AS obj_is_versioned,
                storage.get_common_prefix(o.name, $1, '/') AS common_prefix
            FROM storage.objects o
            WHERE o.bucket_id = $2
              AND o.name COLLATE "C" LIKE $10 || '%%'
              AND ($7 != 'exclude' OR o.archived_at IS NULL)
              AND ($7 != 'only' OR o.archived_at IS NOT NULL)
              AND ($8 != 'exclude' OR NOT o.is_delete_marker)
              AND ($8 != 'only' OR o.is_delete_marker)
        ),
        -- Aggregate common prefixes (folders)
        -- Both created_at and updated_at use MIN(obj_created_at) to match the old prefixes table behavior
        aggregated_prefixes AS (
            SELECT
                common_prefix AS name,
                NULL::uuid AS id,
                MIN(obj_created_at) AS updated_at,
                MIN(obj_created_at) AS created_at,
                NULL::timestamptz AS last_accessed_at,
                NULL::jsonb AS metadata,
                NULL::text AS version,
                NULL::timestamptz AS archived_at,
                NULL::boolean AS is_delete_marker,
                NULL::boolean AS is_versioned,
                TRUE AS is_prefix
            FROM raw_objects
            WHERE common_prefix IS NOT NULL
            GROUP BY common_prefix
        ),
        leaf_objects AS (
            SELECT
                obj_name AS name,
                obj_id AS id,
                obj_updated_at AS updated_at,
                obj_created_at AS created_at,
                obj_last_accessed_at AS last_accessed_at,
                obj_metadata AS metadata,
                obj_version AS version,
                obj_archived_at AS archived_at,
                obj_is_delete_marker AS is_delete_marker,
                obj_is_versioned AS is_versioned,
                FALSE AS is_prefix
            FROM raw_objects
            WHERE common_prefix IS NULL
        ),
        combined AS (
            SELECT * FROM aggregated_prefixes
            UNION ALL
            SELECT * FROM leaf_objects
        ),
        filtered AS (
            SELECT *
            FROM combined
            WHERE (
                $5 = ''
                OR ROW(
                    COALESCE(date_trunc('milliseconds', %I), 'epoch'::timestamptz),
                    name COLLATE "C",
                    %s
                ) %s ROW(
                    -- truncated the same way as the stored value above
                    date_trunc('milliseconds', COALESCE(NULLIF($6, '')::timestamptz, 'epoch'::timestamptz)),
                    $5,
                    $9
                )
            )
        )
        SELECT
            split_part(name, '/', $3) AS key,
            name,
            id,
            updated_at,
            created_at,
            last_accessed_at,
            metadata,
            version,
            archived_at,
            is_delete_marker,
            is_versioned
        FROM filtered
        ORDER BY
            COALESCE(date_trunc('milliseconds', %I), 'epoch'::timestamptz) %s,
            name COLLATE "C" %s,
            COALESCE(version, '') %s
        LIMIT $4
    $sql$,
        v_sort_column,
        v_version_tiebreak,
        v_cursor_op,
        v_sort_column,
        v_sort_order,
        v_sort_order,
        v_sort_order
    );

    -- version is the third tiebreak component for two versions of the same
    -- key tying on both timestamp and name (see filtered CTE / ORDER BY above)
    RETURN QUERY EXECUTE v_query
    USING v_prefix, p_bucket_id, p_level, p_limit, p_start_after, p_sort_column_after, noncurrent_versions, delete_markers, coalesce(p_start_after_version, ''), v_prefix_pattern;
END;
$_$;


ALTER FUNCTION storage.search_by_timestamp(p_prefix text, p_bucket_id text, p_limit integer, p_level integer, p_start_after text, p_sort_order text, p_sort_column text, p_sort_column_after text, noncurrent_versions text, delete_markers text, p_start_after_version text) OWNER TO supabase_storage_admin;

--
-- Name: search_v2(text, text, integer, integer, text, text, text, text, text, text, timestamp with time zone, text, boolean); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.search_v2(prefix text, bucket_name text, limits integer DEFAULT 100, levels integer DEFAULT 1, start_after text DEFAULT ''::text, sort_order text DEFAULT 'asc'::text, sort_column text DEFAULT 'name'::text, sort_column_after text DEFAULT ''::text, noncurrent_versions text DEFAULT 'exclude'::text, delete_markers text DEFAULT 'exclude'::text, start_after_archived_at timestamp with time zone DEFAULT NULL::timestamp with time zone, start_after_version text DEFAULT ''::text, start_after_is_continuation boolean DEFAULT false) RETURNS TABLE(key text, name text, id uuid, updated_at timestamp with time zone, created_at timestamp with time zone, last_accessed_at timestamp with time zone, metadata jsonb, version text, archived_at timestamp with time zone, is_delete_marker boolean, is_versioned boolean)
    LANGUAGE plpgsql STABLE
    AS $$
DECLARE
    v_sort_col text;
    v_sort_ord text;
    v_limit int;
BEGIN
    -- Cap limit to maximum of 1500 records
    v_limit := LEAST(coalesce(limits, 100), 1500);

    -- Validate and normalize sort_order
    v_sort_ord := lower(coalesce(sort_order, 'asc'));
    IF v_sort_ord NOT IN ('asc', 'desc') THEN
        v_sort_ord := 'asc';
    END IF;

    -- Validate and normalize sort_column
    v_sort_col := lower(coalesce(sort_column, 'name'));
    IF v_sort_col NOT IN ('name', 'updated_at', 'created_at') THEN
        v_sort_col := 'name';
    END IF;

    -- Route to appropriate implementation
    IF v_sort_col = 'name' THEN
        -- Use list_objects_with_delimiter for name sorting (most efficient: O(k * log n))
        RETURN QUERY
        SELECT
            split_part(l.name, '/', levels) AS key,
            l.name AS name,
            l.id,
            l.updated_at,
            l.created_at,
            l.last_accessed_at,
            l.metadata,
            l.version,
            l.archived_at,
            l.is_delete_marker,
            l.is_versioned
        FROM storage.list_objects_with_delimiter(
            bucket_name,
            coalesce(prefix, ''),
            '/',
            v_limit,
            CASE WHEN start_after_is_continuation THEN '' ELSE start_after END,
            CASE WHEN start_after_is_continuation THEN start_after ELSE '' END,
            v_sort_ord,
            noncurrent_versions,
            delete_markers,
            start_after_archived_at,
            start_after_version
        ) l;
    ELSE
        -- Use aggregation approach for timestamp sorting
        -- Not efficient for large datasets but supports correct pagination
        RETURN QUERY SELECT * FROM storage.search_by_timestamp(
            prefix, bucket_name, v_limit, levels, start_after,
            v_sort_ord, v_sort_col, sort_column_after,
            noncurrent_versions, delete_markers, start_after_version
        );
    END IF;
END;
$$;


ALTER FUNCTION storage.search_v2(prefix text, bucket_name text, limits integer, levels integer, start_after text, sort_order text, sort_column text, sort_column_after text, noncurrent_versions text, delete_markers text, start_after_archived_at timestamp with time zone, start_after_version text, start_after_is_continuation boolean) OWNER TO supabase_storage_admin;

--
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW; 
END;
$$;


ALTER FUNCTION storage.update_updated_at_column() OWNER TO supabase_storage_admin;

--
-- Name: wake_lifecycle_state(); Type: FUNCTION; Schema: storage; Owner: supabase_storage_admin
--

CREATE FUNCTION storage.wake_lifecycle_state() RETURNS trigger
    LANGUAGE plpgsql SECURITY DEFINER
    SET search_path TO 'pg_catalog'
    AS $$
BEGIN
    IF TG_OP = 'INSERT' AND NEW.lifecycle_configuration IS NULL THEN
        RETURN NEW;
    END IF;

    PERFORM storage.reconcile_lifecycle_state(NEW.id, false);
    RETURN NEW;
END;
$$;


ALTER FUNCTION storage.wake_lifecycle_state() OWNER TO supabase_storage_admin;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: audit_log_entries; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.audit_log_entries (
    instance_id uuid,
    id uuid NOT NULL,
    payload json,
    created_at timestamp with time zone,
    ip_address character varying(64) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE auth.audit_log_entries OWNER TO supabase_auth_admin;

--
-- Name: TABLE audit_log_entries; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.audit_log_entries IS 'Auth: Audit trail for user actions.';


--
-- Name: custom_oauth_providers; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.custom_oauth_providers (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    provider_type text NOT NULL,
    identifier text NOT NULL,
    name text NOT NULL,
    client_id text NOT NULL,
    client_secret text NOT NULL,
    acceptable_client_ids text[] DEFAULT '{}'::text[] NOT NULL,
    scopes text[] DEFAULT '{}'::text[] NOT NULL,
    pkce_enabled boolean DEFAULT true NOT NULL,
    attribute_mapping jsonb DEFAULT '{}'::jsonb NOT NULL,
    authorization_params jsonb DEFAULT '{}'::jsonb NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    email_optional boolean DEFAULT false NOT NULL,
    issuer text,
    discovery_url text,
    skip_nonce_check boolean DEFAULT false NOT NULL,
    cached_discovery jsonb,
    discovery_cached_at timestamp with time zone,
    authorization_url text,
    token_url text,
    userinfo_url text,
    jwks_uri text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    custom_claims_allowlist text[] DEFAULT '{}'::text[] NOT NULL,
    CONSTRAINT custom_oauth_providers_authorization_url_https CHECK (((authorization_url IS NULL) OR (authorization_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_authorization_url_length CHECK (((authorization_url IS NULL) OR (char_length(authorization_url) <= 2048))),
    CONSTRAINT custom_oauth_providers_client_id_length CHECK (((char_length(client_id) >= 1) AND (char_length(client_id) <= 512))),
    CONSTRAINT custom_oauth_providers_discovery_url_length CHECK (((discovery_url IS NULL) OR (char_length(discovery_url) <= 2048))),
    CONSTRAINT custom_oauth_providers_identifier_format CHECK ((identifier ~ '^[a-z0-9][a-z0-9:-]{0,48}[a-z0-9]$'::text)),
    CONSTRAINT custom_oauth_providers_issuer_length CHECK (((issuer IS NULL) OR ((char_length(issuer) >= 1) AND (char_length(issuer) <= 2048)))),
    CONSTRAINT custom_oauth_providers_jwks_uri_https CHECK (((jwks_uri IS NULL) OR (jwks_uri ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_jwks_uri_length CHECK (((jwks_uri IS NULL) OR (char_length(jwks_uri) <= 2048))),
    CONSTRAINT custom_oauth_providers_name_length CHECK (((char_length(name) >= 1) AND (char_length(name) <= 100))),
    CONSTRAINT custom_oauth_providers_oauth2_requires_endpoints CHECK (((provider_type <> 'oauth2'::text) OR ((authorization_url IS NOT NULL) AND (token_url IS NOT NULL) AND (userinfo_url IS NOT NULL)))),
    CONSTRAINT custom_oauth_providers_oidc_discovery_url_https CHECK (((provider_type <> 'oidc'::text) OR (discovery_url IS NULL) OR (discovery_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_oidc_issuer_https CHECK (((provider_type <> 'oidc'::text) OR (issuer IS NULL) OR (issuer ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_oidc_requires_issuer CHECK (((provider_type <> 'oidc'::text) OR (issuer IS NOT NULL))),
    CONSTRAINT custom_oauth_providers_provider_type_check CHECK ((provider_type = ANY (ARRAY['oauth2'::text, 'oidc'::text]))),
    CONSTRAINT custom_oauth_providers_token_url_https CHECK (((token_url IS NULL) OR (token_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_token_url_length CHECK (((token_url IS NULL) OR (char_length(token_url) <= 2048))),
    CONSTRAINT custom_oauth_providers_userinfo_url_https CHECK (((userinfo_url IS NULL) OR (userinfo_url ~~ 'https://%'::text))),
    CONSTRAINT custom_oauth_providers_userinfo_url_length CHECK (((userinfo_url IS NULL) OR (char_length(userinfo_url) <= 2048)))
);


ALTER TABLE auth.custom_oauth_providers OWNER TO supabase_auth_admin;

--
-- Name: flow_state; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.flow_state (
    id uuid NOT NULL,
    user_id uuid,
    auth_code text,
    code_challenge_method auth.code_challenge_method,
    code_challenge text,
    provider_type text NOT NULL,
    provider_access_token text,
    provider_refresh_token text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    authentication_method text NOT NULL,
    auth_code_issued_at timestamp with time zone,
    invite_token text,
    referrer text,
    oauth_client_state_id uuid,
    linking_target_id uuid,
    email_optional boolean DEFAULT false NOT NULL
);


ALTER TABLE auth.flow_state OWNER TO supabase_auth_admin;

--
-- Name: TABLE flow_state; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.flow_state IS 'Stores metadata for all OAuth/SSO login flows';


--
-- Name: identities; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.identities (
    provider_id text NOT NULL,
    user_id uuid NOT NULL,
    identity_data jsonb NOT NULL,
    provider text NOT NULL,
    last_sign_in_at timestamp with time zone,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    email text GENERATED ALWAYS AS (lower((identity_data ->> 'email'::text))) STORED,
    id uuid DEFAULT gen_random_uuid() NOT NULL
);


ALTER TABLE auth.identities OWNER TO supabase_auth_admin;

--
-- Name: TABLE identities; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.identities IS 'Auth: Stores identities associated to a user.';


--
-- Name: COLUMN identities.email; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.identities.email IS 'Auth: Email is a generated column that references the optional email property in the identity_data';


--
-- Name: instances; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.instances (
    id uuid NOT NULL,
    uuid uuid,
    raw_base_config text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE auth.instances OWNER TO supabase_auth_admin;

--
-- Name: TABLE instances; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.instances IS 'Auth: Manages users across multiple sites.';


--
-- Name: mfa_amr_claims; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_amr_claims (
    session_id uuid NOT NULL,
    created_at timestamp with time zone NOT NULL,
    updated_at timestamp with time zone NOT NULL,
    authentication_method text NOT NULL,
    id uuid NOT NULL
);


ALTER TABLE auth.mfa_amr_claims OWNER TO supabase_auth_admin;

--
-- Name: TABLE mfa_amr_claims; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.mfa_amr_claims IS 'auth: stores authenticator method reference claims for multi factor authentication';


--
-- Name: mfa_challenges; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_challenges (
    id uuid NOT NULL,
    factor_id uuid NOT NULL,
    created_at timestamp with time zone NOT NULL,
    verified_at timestamp with time zone,
    ip_address inet NOT NULL,
    otp_code text,
    web_authn_session_data jsonb
);


ALTER TABLE auth.mfa_challenges OWNER TO supabase_auth_admin;

--
-- Name: TABLE mfa_challenges; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.mfa_challenges IS 'auth: stores metadata about challenge requests made';


--
-- Name: mfa_factors; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_factors (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    friendly_name text,
    factor_type auth.factor_type NOT NULL,
    status auth.factor_status NOT NULL,
    created_at timestamp with time zone NOT NULL,
    updated_at timestamp with time zone NOT NULL,
    secret text,
    phone text,
    last_challenged_at timestamp with time zone,
    web_authn_credential jsonb,
    web_authn_aaguid uuid,
    last_webauthn_challenge_data jsonb
);


ALTER TABLE auth.mfa_factors OWNER TO supabase_auth_admin;

--
-- Name: TABLE mfa_factors; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.mfa_factors IS 'auth: stores metadata about factors';


--
-- Name: COLUMN mfa_factors.last_webauthn_challenge_data; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.mfa_factors.last_webauthn_challenge_data IS 'Stores the latest WebAuthn challenge data including attestation/assertion for customer verification';


--
-- Name: mfa_recovery_code_sets; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_recovery_code_sets (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    mfa_factor_id uuid NOT NULL,
    failed_verification_count integer DEFAULT 0 NOT NULL,
    verification_locked_until timestamp with time zone,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT mfa_recovery_code_sets_failed_verification_count_check CHECK ((failed_verification_count >= 0))
);


ALTER TABLE auth.mfa_recovery_code_sets OWNER TO supabase_auth_admin;

--
-- Name: mfa_recovery_codes; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.mfa_recovery_codes (
    id uuid NOT NULL,
    mfa_recovery_code_set_id uuid NOT NULL,
    code_hash text NOT NULL,
    consumed_at timestamp with time zone,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE auth.mfa_recovery_codes OWNER TO supabase_auth_admin;

--
-- Name: oauth_authorizations; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_authorizations (
    id uuid NOT NULL,
    authorization_id text NOT NULL,
    client_id uuid NOT NULL,
    user_id uuid,
    redirect_uri text NOT NULL,
    scope text NOT NULL,
    state text,
    resource text,
    code_challenge text,
    code_challenge_method auth.code_challenge_method,
    response_type auth.oauth_response_type DEFAULT 'code'::auth.oauth_response_type NOT NULL,
    status auth.oauth_authorization_status DEFAULT 'pending'::auth.oauth_authorization_status NOT NULL,
    authorization_code text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone DEFAULT (now() + '00:03:00'::interval) NOT NULL,
    approved_at timestamp with time zone,
    nonce text,
    CONSTRAINT oauth_authorizations_authorization_code_length CHECK ((char_length(authorization_code) <= 255)),
    CONSTRAINT oauth_authorizations_code_challenge_length CHECK ((char_length(code_challenge) <= 128)),
    CONSTRAINT oauth_authorizations_expires_at_future CHECK ((expires_at > created_at)),
    CONSTRAINT oauth_authorizations_nonce_length CHECK ((char_length(nonce) <= 255)),
    CONSTRAINT oauth_authorizations_redirect_uri_length CHECK ((char_length(redirect_uri) <= 2048)),
    CONSTRAINT oauth_authorizations_resource_length CHECK ((char_length(resource) <= 2048)),
    CONSTRAINT oauth_authorizations_scope_length CHECK ((char_length(scope) <= 4096)),
    CONSTRAINT oauth_authorizations_state_length CHECK ((char_length(state) <= 4096))
);


ALTER TABLE auth.oauth_authorizations OWNER TO supabase_auth_admin;

--
-- Name: oauth_client_states; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_client_states (
    id uuid NOT NULL,
    provider_type text NOT NULL,
    code_verifier text,
    created_at timestamp with time zone NOT NULL
);


ALTER TABLE auth.oauth_client_states OWNER TO supabase_auth_admin;

--
-- Name: TABLE oauth_client_states; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.oauth_client_states IS 'Stores OAuth states for third-party provider authentication flows where Supabase acts as the OAuth client.';


--
-- Name: oauth_clients; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_clients (
    id uuid NOT NULL,
    client_secret_hash text,
    registration_type auth.oauth_registration_type NOT NULL,
    redirect_uris text NOT NULL,
    grant_types text NOT NULL,
    client_name text,
    client_uri text,
    logo_uri text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    deleted_at timestamp with time zone,
    client_type auth.oauth_client_type DEFAULT 'confidential'::auth.oauth_client_type NOT NULL,
    token_endpoint_auth_method text NOT NULL,
    CONSTRAINT oauth_clients_client_name_length CHECK ((char_length(client_name) <= 1024)),
    CONSTRAINT oauth_clients_client_uri_length CHECK ((char_length(client_uri) <= 2048)),
    CONSTRAINT oauth_clients_logo_uri_length CHECK ((char_length(logo_uri) <= 2048)),
    CONSTRAINT oauth_clients_token_endpoint_auth_method_check CHECK ((token_endpoint_auth_method = ANY (ARRAY['client_secret_basic'::text, 'client_secret_post'::text, 'none'::text])))
);


ALTER TABLE auth.oauth_clients OWNER TO supabase_auth_admin;

--
-- Name: oauth_consents; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.oauth_consents (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    client_id uuid NOT NULL,
    scopes text NOT NULL,
    granted_at timestamp with time zone DEFAULT now() NOT NULL,
    revoked_at timestamp with time zone,
    CONSTRAINT oauth_consents_revoked_after_granted CHECK (((revoked_at IS NULL) OR (revoked_at >= granted_at))),
    CONSTRAINT oauth_consents_scopes_length CHECK ((char_length(scopes) <= 2048)),
    CONSTRAINT oauth_consents_scopes_not_empty CHECK ((char_length(TRIM(BOTH FROM scopes)) > 0))
);


ALTER TABLE auth.oauth_consents OWNER TO supabase_auth_admin;

--
-- Name: one_time_tokens; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.one_time_tokens (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    token_type auth.one_time_token_type NOT NULL,
    token_hash text NOT NULL,
    relates_to text NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone,
    CONSTRAINT one_time_tokens_token_hash_check CHECK ((char_length(token_hash) > 0))
);


ALTER TABLE auth.one_time_tokens OWNER TO supabase_auth_admin;

--
-- Name: refresh_tokens; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.refresh_tokens (
    instance_id uuid,
    id bigint NOT NULL,
    token character varying(255),
    user_id character varying(255),
    revoked boolean,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    parent character varying(255),
    session_id uuid
);


ALTER TABLE auth.refresh_tokens OWNER TO supabase_auth_admin;

--
-- Name: TABLE refresh_tokens; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.refresh_tokens IS 'Auth: Store of tokens used to refresh JWT tokens once they expire.';


--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE; Schema: auth; Owner: supabase_auth_admin
--

CREATE SEQUENCE auth.refresh_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE auth.refresh_tokens_id_seq OWNER TO supabase_auth_admin;

--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: supabase_auth_admin
--

ALTER SEQUENCE auth.refresh_tokens_id_seq OWNED BY auth.refresh_tokens.id;


--
-- Name: saml_providers; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.saml_providers (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    entity_id text NOT NULL,
    metadata_xml text NOT NULL,
    metadata_url text,
    attribute_mapping jsonb,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    name_id_format text,
    CONSTRAINT "entity_id not empty" CHECK ((char_length(entity_id) > 0)),
    CONSTRAINT "metadata_url not empty" CHECK (((metadata_url = NULL::text) OR (char_length(metadata_url) > 0))),
    CONSTRAINT "metadata_xml not empty" CHECK ((char_length(metadata_xml) > 0))
);


ALTER TABLE auth.saml_providers OWNER TO supabase_auth_admin;

--
-- Name: TABLE saml_providers; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.saml_providers IS 'Auth: Manages SAML Identity Provider connections.';


--
-- Name: saml_relay_states; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.saml_relay_states (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    request_id text NOT NULL,
    for_email text,
    redirect_to text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    flow_state_id uuid,
    CONSTRAINT "request_id not empty" CHECK ((char_length(request_id) > 0))
);


ALTER TABLE auth.saml_relay_states OWNER TO supabase_auth_admin;

--
-- Name: TABLE saml_relay_states; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.saml_relay_states IS 'Auth: Contains SAML Relay State information for each Service Provider initiated login.';


--
-- Name: schema_migrations; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.schema_migrations (
    version character varying(255) NOT NULL
);


ALTER TABLE auth.schema_migrations OWNER TO supabase_auth_admin;

--
-- Name: TABLE schema_migrations; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.schema_migrations IS 'Auth: Manages updates to the auth system.';


--
-- Name: scim_tokens; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.scim_tokens (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    token_hash text NOT NULL,
    prefix text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone,
    revoked_at timestamp with time zone,
    last_used_at timestamp with time zone,
    CONSTRAINT scim_tokens_expires_at_future CHECK (((expires_at IS NULL) OR (expires_at > created_at))),
    CONSTRAINT scim_tokens_revoked_after_created CHECK (((revoked_at IS NULL) OR (revoked_at >= created_at))),
    CONSTRAINT scim_tokens_token_hash_check CHECK ((token_hash ~ '^[0-9a-f]{64}$'::text))
);


ALTER TABLE auth.scim_tokens OWNER TO supabase_auth_admin;

--
-- Name: scim_users; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.scim_users (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    user_id uuid,
    resource jsonb NOT NULL,
    user_name text GENERATED ALWAYS AS (lower((resource ->> 'userName'::text))) STORED NOT NULL,
    external_id text GENERATED ALWAYS AS ((resource ->> 'externalId'::text)) STORED,
    active boolean GENERATED ALWAYS AS (COALESCE(((resource ->> 'active'::text))::boolean, true)) STORED NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    deleted_at timestamp with time zone
);


ALTER TABLE auth.scim_users OWNER TO supabase_auth_admin;

--
-- Name: sessions; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.sessions (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    factor_id uuid,
    aal auth.aal_level,
    not_after timestamp with time zone,
    refreshed_at timestamp without time zone,
    user_agent text,
    ip inet,
    tag text,
    oauth_client_id uuid,
    refresh_token_hmac_key text,
    refresh_token_counter bigint,
    scopes text,
    CONSTRAINT sessions_scopes_length CHECK ((char_length(scopes) <= 4096))
);


ALTER TABLE auth.sessions OWNER TO supabase_auth_admin;

--
-- Name: TABLE sessions; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.sessions IS 'Auth: Stores session data associated to a user.';


--
-- Name: COLUMN sessions.not_after; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sessions.not_after IS 'Auth: Not after is a nullable column that contains a timestamp after which the session should be regarded as expired.';


--
-- Name: COLUMN sessions.refresh_token_hmac_key; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sessions.refresh_token_hmac_key IS 'Holds a HMAC-SHA256 key used to sign refresh tokens for this session.';


--
-- Name: COLUMN sessions.refresh_token_counter; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sessions.refresh_token_counter IS 'Holds the ID (counter) of the last issued refresh token.';


--
-- Name: sso_domains; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.sso_domains (
    id uuid NOT NULL,
    sso_provider_id uuid NOT NULL,
    domain text NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    CONSTRAINT "domain not empty" CHECK ((char_length(domain) > 0))
);


ALTER TABLE auth.sso_domains OWNER TO supabase_auth_admin;

--
-- Name: TABLE sso_domains; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.sso_domains IS 'Auth: Manages SSO email address domain mapping to an SSO Identity Provider.';


--
-- Name: sso_providers; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.sso_providers (
    id uuid NOT NULL,
    resource_id text,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    disabled boolean,
    CONSTRAINT "resource_id not empty" CHECK (((resource_id = NULL::text) OR (char_length(resource_id) > 0)))
);


ALTER TABLE auth.sso_providers OWNER TO supabase_auth_admin;

--
-- Name: TABLE sso_providers; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.sso_providers IS 'Auth: Manages SSO identity provider information; see saml_providers for SAML.';


--
-- Name: COLUMN sso_providers.resource_id; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.sso_providers.resource_id IS 'Auth: Uniquely identifies a SSO provider according to a user-chosen resource ID (case insensitive), useful in infrastructure as code.';


--
-- Name: users; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.users (
    instance_id uuid,
    id uuid NOT NULL,
    aud character varying(255),
    role character varying(255),
    email character varying(255),
    encrypted_password character varying(255),
    email_confirmed_at timestamp with time zone,
    invited_at timestamp with time zone,
    confirmation_token character varying(255),
    confirmation_sent_at timestamp with time zone,
    recovery_token character varying(255),
    recovery_sent_at timestamp with time zone,
    email_change_token_new character varying(255),
    email_change character varying(255),
    email_change_sent_at timestamp with time zone,
    last_sign_in_at timestamp with time zone,
    raw_app_meta_data jsonb,
    raw_user_meta_data jsonb,
    is_super_admin boolean,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    phone text DEFAULT NULL::character varying,
    phone_confirmed_at timestamp with time zone,
    phone_change text DEFAULT ''::character varying,
    phone_change_token character varying(255) DEFAULT ''::character varying,
    phone_change_sent_at timestamp with time zone,
    confirmed_at timestamp with time zone GENERATED ALWAYS AS (LEAST(email_confirmed_at, phone_confirmed_at)) STORED,
    email_change_token_current character varying(255) DEFAULT ''::character varying,
    email_change_confirm_status smallint DEFAULT 0,
    banned_until timestamp with time zone,
    reauthentication_token character varying(255) DEFAULT ''::character varying,
    reauthentication_sent_at timestamp with time zone,
    is_sso_user boolean DEFAULT false NOT NULL,
    deleted_at timestamp with time zone,
    is_anonymous boolean DEFAULT false NOT NULL,
    CONSTRAINT users_email_change_confirm_status_check CHECK (((email_change_confirm_status >= 0) AND (email_change_confirm_status <= 2)))
);


ALTER TABLE auth.users OWNER TO supabase_auth_admin;

--
-- Name: TABLE users; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON TABLE auth.users IS 'Auth: Stores user login data within a secure schema.';


--
-- Name: COLUMN users.is_sso_user; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON COLUMN auth.users.is_sso_user IS 'Auth: Set this column to true when the account comes from SSO. These accounts can have duplicate emails.';


--
-- Name: webauthn_challenges; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.webauthn_challenges (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid,
    challenge_type text NOT NULL,
    session_data jsonb NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    expires_at timestamp with time zone NOT NULL,
    CONSTRAINT webauthn_challenges_challenge_type_check CHECK ((challenge_type = ANY (ARRAY['signup'::text, 'registration'::text, 'authentication'::text])))
);


ALTER TABLE auth.webauthn_challenges OWNER TO supabase_auth_admin;

--
-- Name: webauthn_credentials; Type: TABLE; Schema: auth; Owner: supabase_auth_admin
--

CREATE TABLE auth.webauthn_credentials (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid NOT NULL,
    credential_id bytea NOT NULL,
    public_key bytea NOT NULL,
    attestation_type text DEFAULT ''::text NOT NULL,
    aaguid uuid,
    sign_count bigint DEFAULT 0 NOT NULL,
    transports jsonb DEFAULT '[]'::jsonb NOT NULL,
    backup_eligible boolean DEFAULT false NOT NULL,
    backed_up boolean DEFAULT false NOT NULL,
    friendly_name text DEFAULT ''::text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    last_used_at timestamp with time zone
);


ALTER TABLE auth.webauthn_credentials OWNER TO supabase_auth_admin;

--
-- Name: USER; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."USER" (
    usr_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    usr_name character varying(150) NOT NULL,
    usr_email character varying(255) NOT NULL,
    usr_password_hash text NOT NULL,
    usr_role character varying(30) NOT NULL,
    usr_is_active boolean DEFAULT true NOT NULL,
    usr_created_at timestamp with time zone DEFAULT now() NOT NULL,
    usr_updated_at timestamp with time zone DEFAULT now() NOT NULL,
    usr_first_name character varying(255),
    usr_last_name character varying(255),
    usr_middle_name character varying(255),
    usr_suffix character varying(255),
    usr_failed_login_attempts integer DEFAULT 0 NOT NULL,
    usr_locked_until timestamp(0) without time zone,
    CONSTRAINT "USER_usr_role_check" CHECK (((usr_role)::text = ANY (ARRAY[('system_admin'::character varying)::text, ('dean'::character varying)::text, ('department_chair'::character varying)::text, ('faculty'::character varying)::text])))
);


ALTER TABLE public."USER" OWNER TO postgres;

--
-- Name: academic_year; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.academic_year (
    ay_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    ay_academic_year character varying(20) NOT NULL,
    ay_year_label character varying(50),
    ay_is_active boolean DEFAULT false NOT NULL,
    ay_created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.academic_year OWNER TO postgres;

--
-- Name: audit_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit_log (
    al_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    al_usr_id uuid,
    al_action character varying(100) NOT NULL,
    al_target_table character varying(100) NOT NULL,
    al_target_id uuid,
    al_description text,
    al_ip_address character varying(45),
    al_created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.audit_log OWNER TO postgres;

--
-- Name: biometric_credential; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.biometric_credential (
    bio_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    bio_usr_id uuid NOT NULL,
    bio_fingerprint_hash text NOT NULL,
    bio_registered_at timestamp with time zone DEFAULT now() NOT NULL,
    bio_is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.biometric_credential OWNER TO postgres;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: college; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.college (
    college_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    college_name character varying(100) NOT NULL,
    college_code character varying(20) NOT NULL,
    college_created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.college OWNER TO postgres;

--
-- Name: course; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.course (
    course_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    course_college_id uuid NOT NULL,
    course_dept_id uuid NOT NULL,
    course_code character varying(20) NOT NULL,
    course_name character varying(150) NOT NULL,
    course_lecture_hours numeric(4,1) DEFAULT 0 NOT NULL,
    course_lab_hours numeric(4,1) DEFAULT 0 NOT NULL,
    course_is_active boolean DEFAULT true NOT NULL,
    course_created_at timestamp with time zone DEFAULT now() NOT NULL,
    course_updated_at timestamp with time zone DEFAULT now() NOT NULL,
    course_year_level smallint,
    course_semester smallint,
    course_units numeric(5,2),
    course_total_hours numeric(6,2) GENERATED ALWAYS AS ((COALESCE(course_lecture_hours, (0)::numeric) + COALESCE(course_lab_hours, (0)::numeric))) STORED,
    course_category text
);


ALTER TABLE public.course OWNER TO postgres;

--
-- Name: COLUMN course.course_year_level; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.course.course_year_level IS '1–4 year level in the program curriculum';


--
-- Name: COLUMN course.course_semester; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.course.course_semester IS '1 = 1st Semester, 2 = 2nd Semester';


--
-- Name: dean; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dean (
    dean_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    dean_usr_id uuid NOT NULL,
    dean_first_name character varying(100) NOT NULL,
    dean_middle_name character varying(100),
    dean_last_name character varying(100) NOT NULL,
    dean_suffix character varying(10),
    dean_phone_number character varying(20) NOT NULL,
    dean_gmail character varying(255),
    dean_office_address character varying(255),
    dean_profile_image text,
    dean_assigned_at timestamp with time zone DEFAULT now() NOT NULL,
    dean_college_id uuid,
    dean_dept_id uuid,
    dean_employee_id character varying(50),
    dean_gender character varying(50),
    dean_civil_status character varying(50),
    dean_dob date,
    dean_nationality character varying(100),
    dean_profile_image_public_id character varying(255),
    dean_bio text,
    dean_address text
);


ALTER TABLE public.dean OWNER TO postgres;

--
-- Name: COLUMN dean.dean_address; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.dean.dean_address IS 'Home / office address (same idea as fac_address / dc_address)';


--
-- Name: department; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.department (
    dept_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    dept_college_id uuid NOT NULL,
    dept_name character varying(100) NOT NULL,
    dept_code character varying(20) NOT NULL,
    dept_created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.department OWNER TO postgres;

--
-- Name: department_chair; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.department_chair (
    dc_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    dc_usr_id uuid NOT NULL,
    dc_college_id uuid NOT NULL,
    dc_first_name character varying(100) NOT NULL,
    dc_middle_name character varying(100),
    dc_last_name character varying(100) NOT NULL,
    dc_suffix character varying(10),
    dc_phone_number character varying(20) NOT NULL,
    dc_gmail character varying(255),
    dc_address character varying(255),
    dc_profile_image text,
    dc_assigned_at timestamp with time zone DEFAULT now() NOT NULL,
    dc_dept_id uuid,
    dc_employee_id character varying(50),
    dc_gender character varying(50),
    dc_civil_status character varying(50),
    dc_dob date,
    dc_nationality character varying(100),
    dc_bio text
);


ALTER TABLE public.department_chair OWNER TO postgres;

--
-- Name: fac_specialization; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fac_specialization (
    fspec_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    fspec_fac_id uuid NOT NULL,
    fspec_specialization character varying(150) NOT NULL
);


ALTER TABLE public.fac_specialization OWNER TO postgres;

--
-- Name: faculty; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.faculty (
    fac_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    fac_usr_id uuid NOT NULL,
    fac_college_id uuid NOT NULL,
    fac_first_name character varying(100) NOT NULL,
    fac_middle_name character varying(100),
    fac_last_name character varying(100) NOT NULL,
    fac_suffix character varying(10),
    fac_phone_number character varying(20) NOT NULL,
    fac_gmail character varying(255),
    fac_address character varying(255),
    fac_employment_type character varying(20) NOT NULL,
    fac_rank character varying(100),
    fac_profile_image text,
    fac_created_at timestamp with time zone DEFAULT now() NOT NULL,
    fac_updated_at timestamp with time zone DEFAULT now() NOT NULL,
    fac_dept_id uuid,
    fac_employee_id character varying(50),
    fac_gender character varying(50),
    fac_civil_status character varying(50),
    fac_dob date,
    fac_nationality character varying(100),
    fac_bio text,
    fac_special_position text,
    fac_special_position_max_hours numeric(5,2),
    fac_bachelors_degree character varying(255),
    fac_masters_degree character varying(255),
    fac_doctorate_degree character varying(255),
    CONSTRAINT faculty_fac_employment_type_check CHECK (((fac_employment_type)::text = ANY (ARRAY[('full_time'::character varying)::text, ('part_time'::character varying)::text])))
);


ALTER TABLE public.faculty OWNER TO postgres;

--
-- Name: faculty_account_reviews; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.faculty_account_reviews (
    fvr_id uuid NOT NULL,
    fvr_usr_id uuid NOT NULL,
    fvr_id_photo_path character varying(500) NOT NULL,
    fvr_status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    fvr_reviewed_by uuid,
    fvr_reviewed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    fvr_applicant_name character varying(255),
    fvr_applicant_email character varying(255),
    fvr_decision_note text
);


ALTER TABLE public.faculty_account_reviews OWNER TO postgres;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: historical_schedule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.historical_schedule (
    hist_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    hist_fac_id uuid NOT NULL,
    hist_subj_id uuid NOT NULL,
    hist_sec_id uuid NOT NULL,
    hist_day character varying(15) NOT NULL,
    hist_start_time time without time zone NOT NULL,
    hist_end_time time without time zone NOT NULL,
    hist_sem_id uuid NOT NULL,
    hist_ay_id uuid NOT NULL,
    hist_archived_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.historical_schedule OWNER TO postgres;

--
-- Name: institution; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.institution (
    inst_id uuid DEFAULT gen_random_uuid() NOT NULL,
    inst_name character varying(255) NOT NULL,
    inst_branch_campus character varying(255) NOT NULL,
    inst_college character varying(255) NOT NULL,
    inst_abbreviation character varying(50) NOT NULL,
    inst_contact_email character varying(255) NOT NULL,
    inst_phone character varying(50) NOT NULL,
    inst_created_at timestamp without time zone DEFAULT now(),
    inst_updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.institution OWNER TO postgres;

--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notification; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.notification (
    notif_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    notif_usr_id uuid NOT NULL,
    notif_title character varying(200) NOT NULL,
    notif_message text NOT NULL,
    notif_type character varying(50) DEFAULT 'info'::character varying NOT NULL,
    notif_is_read boolean DEFAULT false NOT NULL,
    notif_created_at timestamp with time zone DEFAULT now() NOT NULL,
    notif_updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.notification OWNER TO postgres;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id uuid NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: room; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.room (
    room_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    room_name character varying(100) NOT NULL,
    room_building character varying(100) NOT NULL,
    room_type character varying(50) NOT NULL,
    room_capacity integer NOT NULL,
    room_is_available boolean DEFAULT true NOT NULL,
    room_created_at timestamp with time zone DEFAULT now() NOT NULL,
    room_updated_at timestamp with time zone DEFAULT now() NOT NULL,
    room_college_id uuid,
    CONSTRAINT room_room_capacity_check CHECK ((room_capacity > 0))
);


ALTER TABLE public.room OWNER TO postgres;

--
-- Name: schedule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.schedule (
    sch_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    sch_load_id uuid NOT NULL,
    sch_fac_id uuid NOT NULL,
    sch_course_id uuid NOT NULL,
    sch_sec_id uuid,
    sch_room_id uuid,
    sch_sem_id uuid NOT NULL,
    sch_day character varying(15) NOT NULL,
    sch_start_time time without time zone NOT NULL,
    sch_end_time time without time zone NOT NULL,
    sch_time_range tsrange GENERATED ALWAYS AS (tsrange(('2000-01-01 00:00:00'::timestamp without time zone + (sch_start_time)::interval), ('2000-01-01 00:00:00'::timestamp without time zone + (sch_end_time)::interval))) STORED,
    sch_status character varying(20) DEFAULT 'draft'::character varying NOT NULL,
    sch_is_active boolean DEFAULT true NOT NULL,
    sch_created_by uuid NOT NULL,
    sch_created_at timestamp with time zone DEFAULT now() NOT NULL,
    sch_updated_at timestamp with time zone DEFAULT now() NOT NULL,
    sch_mis_code character varying(50),
    sch_shift character varying(10) DEFAULT 'day'::character varying NOT NULL,
    CONSTRAINT schedule_check CHECK ((sch_end_time > sch_start_time)),
    CONSTRAINT schedule_sch_day_check CHECK (((sch_day)::text = ANY (ARRAY[('Monday'::character varying)::text, ('Tuesday'::character varying)::text, ('Wednesday'::character varying)::text, ('Thursday'::character varying)::text, ('Friday'::character varying)::text, ('Saturday'::character varying)::text, ('Sunday'::character varying)::text]))),
    CONSTRAINT schedule_sch_status_check CHECK (((sch_status)::text = ANY (ARRAY[('draft'::character varying)::text, ('published'::character varying)::text, ('archived'::character varying)::text])))
);


ALTER TABLE public.schedule OWNER TO postgres;

--
-- Name: COLUMN schedule.sch_mis_code; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.schedule.sch_mis_code IS 'MIS reference code — manual entry on MIS page';


--
-- Name: schedule_archives; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.schedule_archives (
    sar_id uuid NOT NULL,
    sar_sch_id uuid,
    sar_sem_id uuid,
    sar_ay_id uuid,
    sar_fac_id uuid,
    sar_course_id uuid,
    sar_sec_id uuid,
    sar_room_id uuid,
    sar_day character varying(255),
    sar_start_time time(0) without time zone,
    sar_end_time time(0) without time zone,
    sar_shift character varying(255),
    sar_status character varying(255),
    sar_is_active boolean DEFAULT false NOT NULL,
    sar_description text,
    sar_created_by uuid,
    sar_updated_by uuid,
    sar_archived_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.schedule_archives OWNER TO postgres;

--
-- Name: schedule_submission; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.schedule_submission (
    schsub_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    schsub_dept_id uuid NOT NULL,
    schsub_sem_id uuid NOT NULL,
    schsub_submitted_by uuid NOT NULL,
    schsub_submitted_at timestamp with time zone DEFAULT now() NOT NULL,
    schsub_reviewed_by uuid,
    schsub_reviewed_at timestamp with time zone,
    schsub_status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    schsub_remarks text,
    schsub_schedule_snapshot json,
    schsub_fac_id uuid,
    CONSTRAINT schedule_submission_schsub_status_check CHECK (((schsub_status)::text = ANY (ARRAY[('pending'::character varying)::text, ('approved'::character varying)::text, ('returned'::character varying)::text])))
);


ALTER TABLE public.schedule_submission OWNER TO postgres;

--
-- Name: section; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.section (
    sec_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    sec_dept_id uuid NOT NULL,
    sec_ay_id uuid NOT NULL,
    sec_sem_id uuid NOT NULL,
    sec_name character varying(50) NOT NULL,
    sec_year_level integer NOT NULL,
    sec_no_of_student integer DEFAULT 0 NOT NULL,
    sec_max_capacity integer DEFAULT 40 NOT NULL,
    sec_status character varying(20) DEFAULT 'active'::character varying NOT NULL,
    sec_created_at timestamp with time zone DEFAULT now() NOT NULL,
    sec_updated_at timestamp with time zone DEFAULT now() NOT NULL,
    sec_is_evening boolean DEFAULT false NOT NULL,
    CONSTRAINT section_sec_max_capacity_check CHECK ((sec_max_capacity > 0)),
    CONSTRAINT section_sec_year_level_check CHECK (((sec_year_level >= 1) AND (sec_year_level <= 5)))
);


ALTER TABLE public.section OWNER TO postgres;

--
-- Name: semester; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.semester (
    sem_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    sem_ay_id uuid NOT NULL,
    sem_name character varying(50) NOT NULL,
    sem_start_date date NOT NULL,
    sem_end_date date NOT NULL,
    sem_is_active boolean DEFAULT false NOT NULL,
    sem_created_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT semester_check CHECK ((sem_end_date > sem_start_date))
);


ALTER TABLE public.semester OWNER TO postgres;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: study_load; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.study_load (
    sl_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    sl_fac_id uuid NOT NULL,
    sl_course_id uuid NOT NULL,
    sl_sec_id uuid,
    sl_sem_id uuid NOT NULL,
    sl_assigned_by uuid NOT NULL,
    sl_assigned_at timestamp with time zone DEFAULT now() NOT NULL,
    sl_status character varying(20) DEFAULT 'draft'::character varying NOT NULL,
    sl_shift character varying(10) DEFAULT 'day'::character varying NOT NULL,
    CONSTRAINT study_load_sl_status_check CHECK (((sl_status)::text = ANY (ARRAY[('draft'::character varying)::text, ('submitted'::character varying)::text, ('approved'::character varying)::text, ('returned'::character varying)::text, ('published'::character varying)::text])))
);


ALTER TABLE public.study_load OWNER TO postgres;

--
-- Name: system_backup; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_backup (
    bkp_id uuid NOT NULL,
    bkp_file_path character varying(255) NOT NULL,
    bkp_status character varying(255) NOT NULL,
    bkp_error_message text,
    bkp_ran_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.system_backup OWNER TO postgres;

--
-- Name: system_setting; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_setting (
    sset_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    sset_key character varying(100) NOT NULL,
    sset_value text NOT NULL,
    sset_updated_by uuid,
    sset_updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.system_setting OWNER TO postgres;

--
-- Name: token_blacklist; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.token_blacklist (
    tbl_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    tbl_usr_id uuid NOT NULL,
    tbl_token text NOT NULL,
    tbl_revoked_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.token_blacklist OWNER TO postgres;

--
-- Name: webauthn_credentials; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.webauthn_credentials (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    usr_id uuid NOT NULL,
    credential_id text NOT NULL,
    public_key text NOT NULL,
    sign_count bigint DEFAULT 0 NOT NULL,
    transports jsonb DEFAULT '[]'::jsonb NOT NULL,
    device_name text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    last_used_at timestamp with time zone,
    credential_record jsonb,
    CONSTRAINT webauthn_credentials_sign_count_check CHECK ((sign_count >= 0))
);


ALTER TABLE public.webauthn_credentials OWNER TO postgres;

--
-- Name: workload; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.workload (
    wl_id uuid DEFAULT extensions.uuid_generate_v4() NOT NULL,
    wl_fac_id uuid NOT NULL,
    wl_sem_id uuid NOT NULL,
    wl_ay_id uuid NOT NULL,
    wl_type character varying(30) DEFAULT 'regular'::character varying NOT NULL,
    wl_total_hours numeric(5,1) DEFAULT 0 NOT NULL,
    wl_created_at timestamp with time zone DEFAULT now() NOT NULL,
    wl_updated_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT workload_wl_total_hours_check CHECK (((wl_total_hours >= (0)::numeric) AND (wl_total_hours <= (60)::numeric)))
);


ALTER TABLE public.workload OWNER TO postgres;

--
-- Name: messages; Type: TABLE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TABLE realtime.messages (
    topic text NOT NULL,
    extension text NOT NULL,
    payload jsonb,
    event text,
    private boolean DEFAULT false,
    updated_at timestamp without time zone DEFAULT timezone('utc'::text, clock_timestamp()) NOT NULL,
    inserted_at timestamp without time zone DEFAULT timezone('utc'::text, clock_timestamp()) NOT NULL,
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    binary_payload bytea,
    skip_broadcast boolean DEFAULT false NOT NULL,
    expires_at timestamp without time zone
)
PARTITION BY RANGE (inserted_at);


ALTER TABLE realtime.messages OWNER TO supabase_realtime_admin;

--
-- Name: schema_migrations; Type: TABLE; Schema: realtime; Owner: supabase_admin
--

CREATE TABLE realtime.schema_migrations (
    version bigint NOT NULL,
    inserted_at timestamp(0) without time zone DEFAULT now()
);


ALTER TABLE realtime.schema_migrations OWNER TO supabase_admin;

--
-- Name: subscription; Type: TABLE; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TABLE realtime.subscription (
    id bigint NOT NULL,
    subscription_id uuid NOT NULL,
    entity regclass NOT NULL,
    filters realtime.user_defined_filter[] DEFAULT '{}'::realtime.user_defined_filter[] NOT NULL,
    claims jsonb NOT NULL,
    claims_role regrole GENERATED ALWAYS AS (realtime.to_regrole((claims ->> 'role'::text))) STORED NOT NULL,
    created_at timestamp without time zone DEFAULT timezone('utc'::text, now()) NOT NULL,
    action_filter text DEFAULT '*'::text,
    selected_columns text[],
    CONSTRAINT subscription_action_filter_check CHECK ((action_filter = ANY (ARRAY['*'::text, 'INSERT'::text, 'UPDATE'::text, 'DELETE'::text])))
);


ALTER TABLE realtime.subscription OWNER TO supabase_realtime_admin;

--
-- Name: subscription_id_seq; Type: SEQUENCE; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE realtime.subscription ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME realtime.subscription_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: bucket_lifecycle_states; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.bucket_lifecycle_states (
    bucket_id text NOT NULL,
    generation uuid,
    next_run_at timestamp with time zone,
    claim_id uuid,
    claim_until timestamp with time zone,
    continuation jsonb,
    last_started_at timestamp with time zone,
    last_completed_at timestamp with time zone,
    last_success_at timestamp with time zone,
    last_result jsonb,
    failure_count integer DEFAULT 0 NOT NULL,
    last_error jsonb,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT bucket_lifecycle_continuation_object_check CHECK (((continuation IS NULL) OR (jsonb_typeof(continuation) = 'object'::text))),
    CONSTRAINT bucket_lifecycle_failure_count_check CHECK ((failure_count >= 0)),
    CONSTRAINT bucket_lifecycle_last_result_object_check CHECK (((last_result IS NULL) OR (jsonb_typeof(last_result) = 'object'::text))),
    CONSTRAINT bucket_lifecycle_recovery_due_check CHECK (((next_run_at IS NOT NULL) OR (NOT jsonb_path_exists(COALESCE(continuation, '{}'::jsonb), '$."batch"."inFlight"'::jsonpath))))
);


ALTER TABLE storage.bucket_lifecycle_states OWNER TO supabase_storage_admin;

--
-- Name: buckets; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.buckets (
    id text NOT NULL,
    name text NOT NULL,
    owner uuid,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    public boolean DEFAULT false,
    avif_autodetection boolean DEFAULT false,
    file_size_limit bigint,
    allowed_mime_types text[],
    owner_id text,
    type storage.buckettype DEFAULT 'STANDARD'::storage.buckettype NOT NULL,
    versioning_status text DEFAULT 'DISABLED'::text NOT NULL,
    lifecycle_configuration jsonb,
    lifecycle_configuration_generation uuid,
    CONSTRAINT buckets_lifecycle_configuration_pair_check CHECK (((lifecycle_configuration IS NULL) = (lifecycle_configuration_generation IS NULL))),
    CONSTRAINT buckets_lifecycle_configuration_shape_check CHECK (((lifecycle_configuration IS NULL) OR ((jsonb_typeof(lifecycle_configuration) = 'object'::text) AND (lifecycle_configuration ? 'rules'::text) AND
CASE
    WHEN (jsonb_typeof((lifecycle_configuration -> 'rules'::text)) = 'array'::text) THEN ((jsonb_array_length((lifecycle_configuration -> 'rules'::text)) >= 1) AND (jsonb_array_length((lifecycle_configuration -> 'rules'::text)) <= 1000))
    ELSE false
END))),
    CONSTRAINT buckets_lifecycle_configuration_standard_only_check CHECK (((type = 'STANDARD'::storage.buckettype) OR ((lifecycle_configuration IS NULL) AND (lifecycle_configuration_generation IS NULL)))),
    CONSTRAINT buckets_versioning_standard_only_check CHECK (((type = 'STANDARD'::storage.buckettype) OR (versioning_status = 'DISABLED'::text))),
    CONSTRAINT buckets_versioning_status_check CHECK ((versioning_status = ANY (ARRAY['DISABLED'::text, 'ENABLED'::text, 'SUSPENDED'::text])))
);


ALTER TABLE storage.buckets OWNER TO supabase_storage_admin;

--
-- Name: COLUMN buckets.owner; Type: COMMENT; Schema: storage; Owner: supabase_storage_admin
--

COMMENT ON COLUMN storage.buckets.owner IS 'Field is deprecated, use owner_id instead';


--
-- Name: buckets_analytics; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.buckets_analytics (
    name text NOT NULL,
    type storage.buckettype DEFAULT 'ANALYTICS'::storage.buckettype NOT NULL,
    format text DEFAULT 'ICEBERG'::text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    deleted_at timestamp with time zone
);


ALTER TABLE storage.buckets_analytics OWNER TO supabase_storage_admin;

--
-- Name: buckets_vectors; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.buckets_vectors (
    id text NOT NULL,
    type storage.buckettype DEFAULT 'VECTOR'::storage.buckettype NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE storage.buckets_vectors OWNER TO supabase_storage_admin;

--
-- Name: lifecycle_abandoned_artifacts; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.lifecycle_abandoned_artifacts (
    bucket_id text NOT NULL,
    key text NOT NULL,
    name text NOT NULL,
    version text,
    attempted_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE storage.lifecycle_abandoned_artifacts OWNER TO supabase_storage_admin;

--
-- Name: migrations; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.migrations (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    hash character varying(40) NOT NULL,
    executed_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE storage.migrations OWNER TO supabase_storage_admin;

--
-- Name: objects; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.objects (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    bucket_id text,
    name text,
    owner uuid,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    last_accessed_at timestamp with time zone DEFAULT now(),
    metadata jsonb,
    path_tokens text[] GENERATED ALWAYS AS (string_to_array(name, '/'::text)) STORED,
    version text,
    owner_id text,
    user_metadata jsonb,
    archived_at timestamp with time zone,
    is_delete_marker boolean DEFAULT false NOT NULL,
    is_versioned boolean DEFAULT false NOT NULL
);


ALTER TABLE storage.objects OWNER TO supabase_storage_admin;

--
-- Name: COLUMN objects.owner; Type: COMMENT; Schema: storage; Owner: supabase_storage_admin
--

COMMENT ON COLUMN storage.objects.owner IS 'Field is deprecated, use owner_id instead';


--
-- Name: s3_multipart_uploads; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.s3_multipart_uploads (
    id text NOT NULL,
    in_progress_size bigint DEFAULT 0 NOT NULL,
    upload_signature text NOT NULL,
    bucket_id text NOT NULL,
    key text NOT NULL COLLATE pg_catalog."C",
    version text NOT NULL,
    owner_id text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    user_metadata jsonb,
    metadata jsonb
);


ALTER TABLE storage.s3_multipart_uploads OWNER TO supabase_storage_admin;

--
-- Name: s3_multipart_uploads_parts; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.s3_multipart_uploads_parts (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    upload_id text NOT NULL,
    size bigint DEFAULT 0 NOT NULL,
    part_number integer NOT NULL,
    bucket_id text NOT NULL,
    key text NOT NULL COLLATE pg_catalog."C",
    etag text NOT NULL,
    owner_id text,
    version text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE storage.s3_multipart_uploads_parts OWNER TO supabase_storage_admin;

--
-- Name: vector_indexes; Type: TABLE; Schema: storage; Owner: supabase_storage_admin
--

CREATE TABLE storage.vector_indexes (
    id text DEFAULT gen_random_uuid() NOT NULL,
    name text NOT NULL COLLATE pg_catalog."C",
    bucket_id text NOT NULL,
    data_type text NOT NULL,
    dimension integer NOT NULL,
    distance_metric text NOT NULL,
    metadata_configuration jsonb,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE storage.vector_indexes OWNER TO supabase_storage_admin;

--
-- Name: refresh_tokens id; Type: DEFAULT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens ALTER COLUMN id SET DEFAULT nextval('auth.refresh_tokens_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Data for Name: audit_log_entries; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.audit_log_entries (instance_id, id, payload, created_at, ip_address) FROM stdin;
\.


--
-- Data for Name: custom_oauth_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.custom_oauth_providers (id, provider_type, identifier, name, client_id, client_secret, acceptable_client_ids, scopes, pkce_enabled, attribute_mapping, authorization_params, enabled, email_optional, issuer, discovery_url, skip_nonce_check, cached_discovery, discovery_cached_at, authorization_url, token_url, userinfo_url, jwks_uri, created_at, updated_at, custom_claims_allowlist) FROM stdin;
\.


--
-- Data for Name: flow_state; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.flow_state (id, user_id, auth_code, code_challenge_method, code_challenge, provider_type, provider_access_token, provider_refresh_token, created_at, updated_at, authentication_method, auth_code_issued_at, invite_token, referrer, oauth_client_state_id, linking_target_id, email_optional) FROM stdin;
\.


--
-- Data for Name: identities; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.identities (provider_id, user_id, identity_data, provider, last_sign_in_at, created_at, updated_at, id) FROM stdin;
\.


--
-- Data for Name: instances; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.instances (id, uuid, raw_base_config, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mfa_amr_claims; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_amr_claims (session_id, created_at, updated_at, authentication_method, id) FROM stdin;
\.


--
-- Data for Name: mfa_challenges; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_challenges (id, factor_id, created_at, verified_at, ip_address, otp_code, web_authn_session_data) FROM stdin;
\.


--
-- Data for Name: mfa_factors; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_factors (id, user_id, friendly_name, factor_type, status, created_at, updated_at, secret, phone, last_challenged_at, web_authn_credential, web_authn_aaguid, last_webauthn_challenge_data) FROM stdin;
\.


--
-- Data for Name: mfa_recovery_code_sets; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_recovery_code_sets (id, user_id, mfa_factor_id, failed_verification_count, verification_locked_until, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mfa_recovery_codes; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.mfa_recovery_codes (id, mfa_recovery_code_set_id, code_hash, consumed_at, created_at) FROM stdin;
\.


--
-- Data for Name: oauth_authorizations; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_authorizations (id, authorization_id, client_id, user_id, redirect_uri, scope, state, resource, code_challenge, code_challenge_method, response_type, status, authorization_code, created_at, expires_at, approved_at, nonce) FROM stdin;
\.


--
-- Data for Name: oauth_client_states; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_client_states (id, provider_type, code_verifier, created_at) FROM stdin;
\.


--
-- Data for Name: oauth_clients; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_clients (id, client_secret_hash, registration_type, redirect_uris, grant_types, client_name, client_uri, logo_uri, created_at, updated_at, deleted_at, client_type, token_endpoint_auth_method) FROM stdin;
\.


--
-- Data for Name: oauth_consents; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.oauth_consents (id, user_id, client_id, scopes, granted_at, revoked_at) FROM stdin;
\.


--
-- Data for Name: one_time_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.one_time_tokens (id, user_id, token_type, token_hash, relates_to, created_at, updated_at, expires_at) FROM stdin;
\.


--
-- Data for Name: refresh_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.refresh_tokens (instance_id, id, token, user_id, revoked, created_at, updated_at, parent, session_id) FROM stdin;
\.


--
-- Data for Name: saml_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.saml_providers (id, sso_provider_id, entity_id, metadata_xml, metadata_url, attribute_mapping, created_at, updated_at, name_id_format) FROM stdin;
\.


--
-- Data for Name: saml_relay_states; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.saml_relay_states (id, sso_provider_id, request_id, for_email, redirect_to, created_at, updated_at, flow_state_id) FROM stdin;
\.


--
-- Data for Name: schema_migrations; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.schema_migrations (version) FROM stdin;
20171026211738
20171026211808
20171026211834
20180103212743
20180108183307
20180119214651
20180125194653
00
20210710035447
20210722035447
20210730183235
20210909172000
20210927181326
20211122151130
20211124214934
20211202183645
20220114185221
20220114185340
20220224000811
20220323170000
20220429102000
20220531120530
20220614074223
20220811173540
20221003041349
20221003041400
20221011041400
20221020193600
20221021073300
20221021082433
20221027105023
20221114143122
20221114143410
20221125140132
20221208132122
20221215195500
20221215195800
20221215195900
20230116124310
20230116124412
20230131181311
20230322519590
20230402418590
20230411005111
20230508135423
20230523124323
20230818113222
20230914180801
20231027141322
20231114161723
20231117164230
20240115144230
20240214120130
20240306115329
20240314092811
20240427152123
20240612123726
20240729123726
20240802193726
20240806073726
20241009103726
20250717082212
20250731150234
20250804100000
20250901200500
20250903112500
20250904133000
20250925093508
20251007112900
20251104100000
20251111201300
20251201000000
20260115000000
20260121000000
20260219120000
20260302000000
20260625000000
20260821000000
20260821010000
20260824000000
20260824000001
20260831180000
\.


--
-- Data for Name: scim_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.scim_tokens (id, sso_provider_id, token_hash, prefix, created_at, expires_at, revoked_at, last_used_at) FROM stdin;
\.


--
-- Data for Name: scim_users; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.scim_users (id, sso_provider_id, user_id, resource, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.sessions (id, user_id, created_at, updated_at, factor_id, aal, not_after, refreshed_at, user_agent, ip, tag, oauth_client_id, refresh_token_hmac_key, refresh_token_counter, scopes) FROM stdin;
\.


--
-- Data for Name: sso_domains; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.sso_domains (id, sso_provider_id, domain, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: sso_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.sso_providers (id, resource_id, created_at, updated_at, disabled) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.users (instance_id, id, aud, role, email, encrypted_password, email_confirmed_at, invited_at, confirmation_token, confirmation_sent_at, recovery_token, recovery_sent_at, email_change_token_new, email_change, email_change_sent_at, last_sign_in_at, raw_app_meta_data, raw_user_meta_data, is_super_admin, created_at, updated_at, phone, phone_confirmed_at, phone_change, phone_change_token, phone_change_sent_at, email_change_token_current, email_change_confirm_status, banned_until, reauthentication_token, reauthentication_sent_at, is_sso_user, deleted_at, is_anonymous) FROM stdin;
\.


--
-- Data for Name: webauthn_challenges; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.webauthn_challenges (id, user_id, challenge_type, session_data, created_at, expires_at) FROM stdin;
\.


--
-- Data for Name: webauthn_credentials; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

COPY auth.webauthn_credentials (id, user_id, credential_id, public_key, attestation_type, aaguid, sign_count, transports, backup_eligible, backed_up, friendly_name, created_at, updated_at, last_used_at) FROM stdin;
\.


--
-- Data for Name: USER; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public."USER" (usr_id, usr_name, usr_email, usr_password_hash, usr_role, usr_is_active, usr_created_at, usr_updated_at, usr_first_name, usr_last_name, usr_middle_name, usr_suffix, usr_failed_login_attempts, usr_locked_until) FROM stdin;
d126364e-680c-4fe5-879b-52a849cf8e19	Emelie C. Villaceran	emelievillaceran@gmail.com	$2y$12$y8L6FjE2IKKELvZWVfPjuOPR0hRb1OI.2u61x77b3NmIDh25Kl5Ni	faculty	t	2026-11-03 08:33:06.441612+00	2026-11-03 08:33:06.441612+00	Emelie	Villaceran	C.	\N	0	\N
cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Raamah C. Rosales	hamaar.selasor@gmail.com	$2y$12$pJuHVUfofeizWzqAF4sokefl6TSV42KWmoWcQkzL0BO2gxXQud2WG	department_chair	t	2026-11-03 08:30:28.347565+00	2026-11-03 08:30:28.347565+00	Raamah	Rosales	C.	\N	0	\N
2005b54a-b45b-437a-bb5a-e167c8137a02	Black N/A Pink	blackpink@gmail.com	$2y$12$QPPqZXUtLqDY8lCgzLg02uS3MtA1eg3d3K4i2kqcKZsxVPMnm.TO.	faculty	t	2026-11-08 11:30:21.480931+00	2026-11-08 11:30:21.480931+00	Black	Pink	N/A	\N	0	\N
e891982b-d1b2-45c8-a64f-e2c5615e433a	John Rey E. Furtos	johnfurtos@gmail.com	$2y$12$HfFK6cFQIaFx6tngjFqWO.HtspkIQsflxbggAT85CTMF6RKtY4gxK	faculty	t	2026-11-03 08:05:10.661713+00	2026-11-03 08:05:10.661713+00	John Rey	Furtos	E.	\N	0	\N
335c70b6-c19a-4214-8b7b-fb771a2c9e02	Noreen B. Fuentes	noreen.fuentes@ctu.edu.ph	$2y$12$cEw4K73H9eIH1BQj/K6rC.gWgsPbxsyf7mACw3PmiqVqFobxohvSC	department_chair	t	2026-07-18 06:03:37.813106+00	2026-07-18 06:03:37.813106+00	Noreen	Fuentes	B.	\N	0	\N
270ad7b7-7c4d-4823-b314-816b91773cb9	System Administrator	admin@skedyul.ctu.edu.ph	$2y$12$5UjYtBYFQ/VoBJn20OHFi.99w85lPfLCPSVilGSt3kFTj04mZU4Si	system_admin	t	2026-07-15 15:02:46.174993+00	2026-07-15 15:02:46.174993+00	Inde	Navarette	Golan	\N	0	\N
0180a717-2d4b-4d0d-a418-153b7b24911e	Pet Andrew P. Nacua	pet.nacua@ctu.edu.ph	$2y$12$RYI3Bz9DBTQyErHy.P0zt.33ZcC5ye/XyWV.iry9aSZSPe1RIrmCi	department_chair	t	2026-07-18 06:03:37.813106+00	2026-07-18 06:03:37.813106+00	Pet Andrew	Nacua\r\n	P.\r\n	\N	0	\N
d2468e93-6a49-4532-8eae-b5ab3f0dd090	Gerry Ritz R. Densing	gerryritzdensing@ctu.edu.ph	$2y$12$D0JOu.DDCVdfQZmv0GaAFu3PGiF0c4B8egvCVqMBfslPglfkXGUXe	faculty	t	2026-10-06 07:01:06.548816+00	2026-10-06 07:01:06.548816+00	Gerry Ritz	Densing	R.	\N	0	\N
80e78fb0-6908-422c-ae9e-cc31a7a29f04	Albert D. Maghanoy	albertmaghanoy123@gmail.com	$2y$12$7jHp6/fy1TvaVtxGY3vIIOEQmmkzMuuUQAHL7KrBIoMbcG2DOkblG	faculty	t	2026-11-03 08:23:15.104358+00	2026-11-03 08:23:15.104358+00	Albert	Maghanoy	D.	\N	0	\N
03ffd937-4f2b-4984-bce1-22b9bf2a29a9	Suzette B. Bacus	suzette.bacus@gmail.com	$2y$12$3zEfbEjynFSmDtu71jo4ZOnqHsniiNPsSLBiihyq3E23b7VI6MvPm	faculty	t	2026-11-03 08:23:27.490049+00	2026-11-03 08:23:27.490049+00	Suzette	Bacus	B.	\N	0	\N
0a6013f9-0b25-4383-a1f9-f81cac3ea32d	Janet S. Ugang	janetugang@gmail.com	$2y$12$HQOa7rYOB61oSwLe3LumfuslGaLuHA7sIbu9RdRSnE3qDf6WnOHou	faculty	t	2026-11-03 08:29:43.126142+00	2026-11-03 08:29:43.126142+00	Janet	Ugang	S.	\N	0	\N
32ab44ac-a571-4128-8306-8d6f38c050f6	Oddyssy Aying Subingsubing	oddyssysubingsubing@gmail.com	$2y$12$cZKOo3ow4DGJb9Y3i9OcCuynFBFAzpbJOyaB6z2J.AIyS5odOqJ4e	faculty	t	2026-11-03 07:10:04.017756+00	2026-11-03 07:10:04.017756+00	Oddyssy	Subingsubing	Aying	\N	0	\N
bc02c426-ac11-4fb4-93d5-eb6804b7974e	Jose  Maria S Garcia II	josemariagarcia123@gmail.com	$2y$12$RlBo0db0kasVfEnaTbAbtO1qeT6NGhbMa2NUSySNtcLbaQCtIDmEe	dean	t	2026-11-03 07:20:41.672579+00	2026-11-03 07:20:41.672579+00	Jose  Maria	Garcia	S	II	0	\N
\.


--
-- Data for Name: academic_year; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.academic_year (ay_id, ay_academic_year, ay_year_label, ay_is_active, ay_created_at) FROM stdin;
0e90a55c-51ba-4d90-868d-3b91051ce5a0	2026-2027	2026-2027	t	2026-09-30 08:18:55.204565+00
\.


--
-- Data for Name: audit_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.audit_log (al_id, al_usr_id, al_action, al_target_table, al_target_id, al_description, al_ip_address, al_created_at) FROM stdin;
dc5c7aa3-79f7-40c6-a218-869dd37e746c	270ad7b7-7c4d-4823-b314-816b91773cb9	CREATE	FACULTY	\N	Created faculty profile.	192.168.1.10	2026-07-15 15:05:14.128162+00
87160eff-bf34-4bc4-a9e0-66afe47bf4a2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 06:41:41+00
015fa18d-affe-456f-b15b-5bec5985db6b	e891982b-d1b2-45c8-a64f-e2c5615e433a	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 08:14:42+00
83a61caa-924a-45ba-926b-2ea083fcd617	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:38:09+00
f3531a97-7bed-495f-98d5-0884fa2e62e6	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 08:40:40+00
909ec735-cf0e-47f7-91bc-d213068c1987	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 12:39:40+00
baf9b2d2-e2a4-40a8-80fd-dda286cea0d7	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 12:39:48+00
56257af6-4c8b-4aec-aaf9-ac05cf70edac	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 13:26:37+00
efb5f2e8-8a59-4331-adeb-d0e34106f41f	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 02:57:28+00
dc3534e1-6d47-487c-95b9-a6ea0b9cc4b2	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 06:03:05+00
2d0fb453-f494-4fca-be7d-9a09d45a8491	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:29:35+00
238bc55b-de0c-4847-8bc4-10c2672c6f7b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:30:18+00
f5411534-b394-4820-a3cb-5e9ffa3535ff	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:30:30+00
efc4e9aa-fdfe-4a4e-b545-4fac489c7ee2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 13:31:17+00
96cc191a-2ad1-4d9b-b17a-eaad32fc50d3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbs.update	c1ac7966-cb6f-4faa-972d-77d5e0d0d96f	Role: Department Chair; Route: chair.pbs.update; HTTP 200	127.0.0.1	2026-11-04 10:18:40+00
daaf73d8-6a51-43d8-94f7-c5f30a1f50aa	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-04 13:02:04+00
005b4f38-351c-4592-95c3-d62bb01338cf	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 13:32:26+00
8feb81e9-f467-4ca8-a395-dc5f0f6f6d88	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 13:33:14+00
938a7ccd-58d6-4d42-bbb0-b36248cdc543	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-04 13:17:39+00
b3f2ad0f-6ba2-465b-9f7e-8355f6df9ab2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 13:17:49+00
6a009b65-e8bc-49e2-ab7a-b028d9a988b3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:33:49+00
907cfb60-4ce9-4a33-b3fe-e0f88f85dd14	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 13:36:52+00
550c7ae3-5645-4b3d-b2c9-f3b6792fdff1	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:37:17+00
a85925bd-4cfd-4cc8-a8a7-8b74869f5d32	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:37:54+00
c2aa063f-a306-46b4-95b0-ce99cb558d13	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 13:41:40+00
5230b2f1-5e12-4e40-a778-ec13eaec3054	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:43:28+00
56458bec-018b-4a55-8a67-6d1e6235d015	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:48:17+00
bf5f4d2c-2d6c-40f4-8921-4fe9a0114009	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 13:49:04+00
e4701ecc-f65e-4a1b-80a3-4526cdc9fc00	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 13:54:20+00
fa0f99c6-a00e-4aba-9fdd-3c911adc6b76	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 13:55:04+00
515d0542-a939-426f-b81e-e571e4ebe189	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 13:56:19+00
9ba859e9-d857-4684-9917-018feec27fcb	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 13:57:24+00
2919adb0-45b0-4306-a0a4-182f5b0a3e78	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 13:57:40+00
3fd76210-aab2-4d87-9ad0-8a5c46015cf4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 13:58:19+00
2fa4d681-2a91-4e14-a497-f1f4cb263cd3	e891982b-d1b2-45c8-a64f-e2c5615e433a	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-04 13:18:02+00
ffcdf5eb-79b2-44da-aa83-f41a534b0b28	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 15:14:22+00
e153fe55-fbb3-4ed7-ae06-0516281a504f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-05 02:08:49+00
23e72af9-1728-4eb3-a877-f4a4c7bc42a1	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-05 02:09:07+00
f33ba1b4-3cb0-4a2a-866f-803971971bbc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 15:18:08+00
e3f53987-4ea7-4bef-a855-a4491966a970	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 15:26:12+00
dddcc985-c894-4124-86b9-8277cd6fe655	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 15:27:36+00
3ca74751-41c7-4c26-b0ab-51e1d7169a9d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 15:27:47+00
9222c1c0-cb47-4084-813b-e5543110fb85	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 15:29:48+00
88499df5-536b-4f62-805f-a2a64d8b373a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 15:45:21+00
36bc53f3-4eab-42e4-8fb5-7c247c69a90f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 15:47:49+00
def941ca-c04a-4ce6-9e3a-7016b807f0cf	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 16:11:30+00
4f54fd3f-b5a2-4872-9663-ae87bc5a4330	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 16:16:33+00
2a538677-ce3b-4fae-a293-62e47d4bac30	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 16:29:46+00
21a17716-3f61-46ee-a9d3-b4752540d5c6	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 16:30:23+00
49451055-0a0c-4477-bfcf-e7496684ce91	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 16:46:57+00
e8f9fd44-8eb2-495a-82f4-ec8fc5a9564b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 16:47:09+00
ac62470f-de1f-4a65-9195-290d03c1765c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 16:47:43+00
2510fa18-5040-4c59-974c-2b32ee6451e7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 16:48:11+00
d5d0766e-441c-49cb-907a-44fa943884e5	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 16:51:43+00
a896c85c-b065-42d7-9e0e-a343846c5d45	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	c436529d-538a-41f8-b83e-6b31147c298d	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-10-05 16:52:27+00
76410d06-3e72-416d-86c9-9da103a48a86	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	99319d27-b14d-4325-baab-ea2be7fd93b1	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-05 16:53:04+00
74a0219c-a898-4a30-860d-ed4b40409f3d	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 16:53:30+00
dd1c801a-6e27-4020-a463-82b05f76e260	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 07:10:43+00
4506def4-bf2d-4af7-baaf-3db0f293322f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:16:06+00
8a87265e-9fea-474c-bec6-686ad7cf24db	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 08:39:16+00
7f4b1014-eeda-4be3-91c0-a2b13ef6c2a6	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 17:05:44+00
68c47143-63c3-495c-9599-7d6ae7aafe52	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 17:06:49+00
dc4af930-749a-4963-af4b-096a0366877a	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 17:07:03+00
2e816d79-965a-4648-9977-92e8d87f4065	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 17:07:25+00
39210ac2-077f-419b-82ee-eb748054a8ab	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 17:14:57+00
77b91feb-4c7c-48cf-9db0-fd487354088b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 17:15:49+00
7b288a3b-e68e-4dcc-8f75-4f6122d3f02a	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 17:18:37+00
fbef9827-d11b-4c66-bb65-122485dcffd8	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 17:19:01+00
c82c740b-c095-4290-8c45-24c5f8d6340d	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 17:23:38+00
3d6cf910-857f-4d75-85e1-da8127933707	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 17:24:19+00
854a5a58-1d32-47e0-8473-a91d314cf996	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 17:27:18+00
36451b52-75d2-4445-a96a-c14cc0789db6	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 17:28:16+00
80ff3b10-9753-459a-937d-409ae5e6819a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 17:28:33+00
7cb49631-fe2c-4293-bb0b-682a5983c82b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 17:29:16+00
39e5404f-b152-46f6-a179-2bc4e3c09c7f	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 02:59:36+00
f1e49e01-41f0-4fc6-8372-2e8b21788ea6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 02:59:47+00
c4614437-bf00-4c10-873e-b8cc38160afa	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 03:00:42+00
f69e1b98-ed06-43cc-8d37-9ebefc5edc9c	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 03:00:56+00
6e4cb190-3d07-47fd-ba4b-c6c4b3acc1d4	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 23:48:43+00
85cd1a65-bbd5-4eba-9fbe-b056723bbe59	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 23:51:47+00
d7017ebc-9d2c-4848-a853-c44b94886f80	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-05 23:51:58+00
6c087660-4928-4dd4-88da-1372a8c722ef	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-05 23:55:37+00
2b074c5d-c11b-4fa6-a76c-f560c2cfb188	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 23:55:50+00
f4339259-e62b-4f90-97a6-e6b944437151	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-05 23:57:00+00
6aafc013-c1f2-4ef8-aa0b-a7eaf0666f46	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-05 23:58:27+00
d84591a3-70a7-4db3-91bf-150a2506292f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-06 00:00:03+00
a24ebf00-9bed-4df4-b470-e1df0f833d37	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-06 00:00:12+00
61bb4bd9-f18a-44d6-8d81-4eb0d66ec0cf	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-06 00:01:50+00
ad74ce07-1592-4515-8805-b8179fd96498	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-06 00:03:52+00
0a5113a3-d42b-4322-bb9f-a320e11249e4	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.reject	8ca485d0-0485-45d0-9a90-c6fc1794f224	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 302	127.0.0.1	2026-10-06 00:05:44+00
641e637d-dd73-4662-9151-352702736161	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-06 00:06:56+00
dba4c719-4e39-45e0-b25b-21f1442827ad	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-06 00:07:07+00
1c1163a8-38f2-4143-8ddb-cccf891493e6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-06 00:10:33+00
ee1ff9e7-4823-4bb7-89ed-c2a336a1559f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-06 00:18:58+00
8bba28e1-6800-42a1-ba10-290c5bbe7309	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-06 00:21:49+00
804c2e84-c382-495b-8bfd-0840c0710cd0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-06 00:22:02+00
7f5fd8a5-b0c0-4b16-bfc4-0f3d3f94b4c4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-06 00:24:49+00
5289e567-5afe-4bb7-9de9-4e8c5b2e4941	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 06:10:10+00
4df6f18a-7af3-4059-b2c0-e108de97fe28	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 10:36:00+00
c2eb7688-0616-41b8-b3a8-f394df1036fb	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-06 00:30:35+00
48b6cc9c-b8c2-4a8b-820a-e8e36fd3c1a4	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-06 00:33:36+00
8d3b04cd-a426-41a2-b169-aa72e12016a8	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-04 13:02:34+00
b40ec1b1-9987-4079-8b40-691ae47e1157	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 13:02:43+00
9e7c0842-54c4-4667-9901-af2bb7668940	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-06 05:37:53+00
414ce198-da63-4c4a-87a7-4515ea1a320e	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.reject	0ff7a695-dc13-4cb5-a999-23e6bde2cf0b	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 302	127.0.0.1	2026-10-06 05:38:28+00
6bcd8699-d1ab-42ec-a39f-b37b7a24d299	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-06 05:41:20+00
9222b6f3-141c-4aa0-9927-b83517fca260	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.fetch-schedules	\N	Role: Department Chair; Route: chair.pbs.fetch-schedules; HTTP 200	127.0.0.1	2026-11-04 13:31:38+00
d14e5e27-5bd1-4842-bda9-6a2ca9c5f0eb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 15:54:15+00
5cd763ee-7294-4aaf-9505-50a9f43522d3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-06 05:48:51+00
70980822-c5ba-4dfd-8ca5-64270115c7e8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-06 06:01:46+00
3c225b3c-e1e1-4d58-b73d-839cf3aa8f46	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-04 15:54:20+00
05f20aaa-debb-40cd-941f-694bda2641b4	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-06 07:02:43+00
b27de7b8-0a52-4ab7-8f38-d9705086c63d	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	a6490d96-ebb4-4e57-b6c3-ef798c2927a7	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-10-06 07:03:55+00
92c5a923-c0aa-4078-adca-953b68042f9b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-06 07:06:16+00
78c60331-f927-486f-a054-065f92254efe	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-06 07:11:33+00
1dd39c0a-1dae-429f-a59c-a5585187f418	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	166d28d0-360f-4d1a-89df-92713e4cbc4d	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-06 07:25:55+00
08f6f284-be5c-42b9-b95a-c568f60b5bdc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-06 07:39:43+00
dee37d09-b129-4637-b6c5-63e3d9bdb675	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:45:04+00
f221a976-2766-4c6e-8441-0e0ad09a10b3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-06 07:46:31+00
80663b88-0041-42fc-a24b-aab2d7007260	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:46:56+00
fd8010ab-2b56-4504-a4db-b4f94d23e304	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:47:07+00
14e2ee76-197a-44bc-b2bf-7879e69ee3a3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:47:21+00
7f40d2ba-7111-4a83-8c4d-2c29cf125f05	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:47:33+00
fde132e9-783b-4fb6-bd44-23bad231abb4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:47:56+00
14b4c112-11a2-48e8-9cf7-2af857c98b9a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-06 07:48:12+00
b132061b-a424-4a09-b3f7-c75e0a8925d2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:51:41+00
22daaf99-e09d-4c48-8d80-918030d8c156	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 07:51:55+00
18441964-969f-4550-9ff4-b28d4d83198a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-06 07:52:11+00
da0c478d-cc86-4b1d-a525-1a7d72f414d0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	99abb9a0-fcb4-4540-9217-98dfa637b1ea	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-06 07:53:29+00
6907a748-f376-4ed5-b74d-d47887df25b2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	aadbaa7b-7ce7-4dd5-8a87-8e0206162ddc	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-06 07:53:59+00
18ca4291-913f-4113-936d-3b7fb6863673	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	83587a45-571d-4366-88ae-68c0837ab82f	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-06 07:54:24+00
5e218c5e-42fa-42c1-8af3-4452a375506b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-06 08:01:52+00
3ccc0e55-a7dd-4747-92d3-246537b9353b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:04:15+00
596b192e-0590-419b-b8d3-7ce366db0cf8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:04:25+00
8d88b113-fe1d-47e0-9900-883ef0e45230	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:04:39+00
98ea968c-ac7f-40cb-b1e6-75abff673f75	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:04:51+00
5baf05e3-3c78-4188-b3a4-26c809ebbfec	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:05:29+00
f60c2f72-3eef-4690-8ffe-0232efa4bdd4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:07:33+00
0db981f3-6dc4-4a24-b8ad-e8f0bf0690ad	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:07:46+00
2c0f6257-f218-4c07-9d12-899000caf776	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-06 08:07:56+00
b70cc592-9732-4eff-ae19-137194ceed9e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-06 08:09:50+00
98f9a434-0166-4fbd-831f-a5d48a3b824f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	99aa5b27-e35d-413e-abe5-cc2f01e160c1	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-06 08:11:18+00
cd7a38b4-e768-4206-9b2a-4a9370141076	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-06 08:11:58+00
570d489c-5178-483f-8261-07163b8a5106	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-10-06 08:12:35+00
2411b336-f497-4a95-be0a-aba28aceb206	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-10-06 08:14:55+00
1947587b-5e77-480d-bbd9-e02541bfe28e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-06 08:15:10+00
58896673-599a-4c01-9f5c-51818b017c69	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-06 08:16:44+00
e51b3d30-a0b5-4e06-85ff-af0f7083ff22	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	c95750a5-61ff-49c9-b9e5-8be1e42f8ebd	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-06 08:17:39+00
ed8494bd-7256-4e1a-8d2e-a48d91b277ad	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	c95750a5-61ff-49c9-b9e5-8be1e42f8ebd	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-06 08:18:05+00
6087f056-f74c-4a1b-81b3-ffd3286707a4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-06 08:18:40+00
d1b6e8bf-5923-451d-b871-838517069f89	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-10-06 08:18:53+00
52a9b7e4-5ff7-4a26-b835-6a8ed3c526c0	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-10-06 08:21:37+00
d4463178-318b-44d2-9073-c228c4c5b5d9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-06 08:22:16+00
d4d9b6bb-0f42-4575-ad82-c113e69b9bd9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-06 08:24:26+00
eb14e867-b169-4370-a0e9-f1a3ae2caa54	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 02:41:41+00
0103f3c8-b9a1-4f26-8b22-62ee9914c523	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 03:28:55+00
ed89ed52-0306-4a04-bc4e-971c6f645821	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-07 03:34:18+00
101a890d-5e5b-4293-ab36-4bc4810af354	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-07 03:35:19+00
4af06529-8ae8-4d50-8eb5-c1ef35500aee	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 03:35:30+00
0c30a448-9584-4c34-8e42-b1290aeb35e4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 03:44:31+00
a6dbd2a8-01e7-40ec-8623-d48b7e58703c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 03:50:04+00
6734d269-76dc-4c02-bfa2-17daaa6e6a75	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 03:50:38+00
0759f1d6-c122-4f24-9cf0-d5455b23b5b3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 03:51:46+00
a930dbd4-d2e2-4402-8a1b-5a4dae33647b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 03:51:55+00
8edf66f6-89a5-4a00-9fa8-819a8fc15b7a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 04:42:57+00
2b173c95-7f6d-4cf6-b75d-6a9552cb9321	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	6e84d5bf-8df4-412d-b89e-f7f2eaab6f95	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-07 05:30:09+00
21e3fb6b-d59b-4e96-94f6-fc679279815b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	0a755f77-6259-40b5-8dc2-304ea5b9ddc8	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-07 05:30:22+00
058fde3f-f6ac-4203-941b-37b6d1dc48dc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	660d1cf3-7cdc-4276-bc0f-00241e945d8d	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-07 05:30:37+00
38956b1d-7b9b-44c6-8292-52da2f7c5f4d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	3ad488f9-ed03-4d51-b67e-50fb50affef2	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-07 05:32:41+00
c53270ce-5947-4242-96df-21abb215d773	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	6e52bef0-ce51-4fc0-addc-2da0e2c792de	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-07 05:33:03+00
6d562e37-bad5-4ce7-a313-87329f8f816a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:34:16+00
536a9ad2-02f5-4457-9466-8a8669558b6b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:34:40+00
8504c4b8-f943-4ee9-ac03-d12765c13936	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:35:27+00
24e79c2d-e6da-457b-b33c-4d7d1be1c974	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 05:38:48+00
c7ec6f2c-1ded-4adb-934f-06e165dc681f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 05:39:24+00
47584f47-5873-4fdc-a1c8-4f05e4004b11	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 05:43:19+00
df08ebcb-60a5-4130-b84c-cbcbeca1275f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 05:43:52+00
5a300a1d-d4e6-46f5-98f4-e7db01797e87	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 05:49:06+00
6c44bb20-011c-40f9-ac98-a3a6ef69e8cf	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 05:50:20+00
89ef30ab-9b5e-4a7d-afde-26f7868cb9be	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	d01a5e78-47d5-48b9-aa5c-df571d00ee4c	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-07 05:51:09+00
500aa852-af39-4c8e-9434-295d4bb7f762	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	1c6cece4-b4ac-46f1-b960-8b9e77658d17	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-10-07 05:51:28+00
541051e1-325d-473a-8565-33d297a0fb15	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	28e14207-c379-4ef8-9461-657de7927bbd	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-10-07 05:52:12+00
ae476895-f72e-4d15-87c0-f40886a4fa2f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	28e14207-c379-4ef8-9461-657de7927bbd	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-10-07 05:52:32+00
6dc94c4c-900d-44a6-902e-eb27ba5b607b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:53:59+00
f29e5984-0c5e-42c8-9983-c0c09f2eb470	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:55:46+00
09cb62d9-cc58-42e1-b741-9ee326ee239c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:57:22+00
0f80cf8a-f668-4370-817d-6c17b98ec6d1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:57:37+00
031c2fd0-3591-4912-8705-4a5ec0439ec7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 05:57:55+00
47b300d9-c9b9-4861-96f6-e1fceb5c7905	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 06:01:52+00
1727efa3-13ea-4597-b564-1a898ec19d9d	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	e7c7dd9f-cc3e-42eb-9e40-89980764cc19	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 07:12:12+00
95553d3f-8d59-4e07-913f-c8c5d8abe604	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	d57ab905-7f9a-4de4-847e-4ba3c7bcfac2	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 08:16:03+00
a0a49ff9-0f8a-402c-9c41-18136656b9f7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 06:05:01+00
00425ed0-cf51-4e73-a1f5-e3952b23a89c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-10-07 06:07:21+00
b05fdcda-c0c7-4338-9862-743eb72b976d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 06:07:34+00
aa53e5a3-6ca1-4eed-918f-0e002a177e10	32ab44ac-a571-4128-8306-8d6f38c050f6	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 12:54:08+00
94e66bae-6ced-477f-87b8-1e2457d7e659	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 06:08:27+00
fb8b9541-f58d-46cb-8758-3b2fcb4bded3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	b8474d63-164f-452f-ae6c-21371fc35819	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-10-07 06:09:03+00
c140c716-a3d0-4ad7-8950-fdfaf15761fa	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 03:06:23+00
2252a5fa-16fe-44f1-b7c9-e6c04c6585d9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 06:16:24+00
f3e8b3d9-163a-4421-9a6a-509105c02224	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 06:24:29+00
b36a7230-85fd-4f6e-8720-f7875e6c2d03	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 03:06:37+00
ee745719-7846-48f6-9aad-d856234efe17	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbs.update	d01a5e78-47d5-48b9-aa5c-df571d00ee4c	Role: Department Chair; Route: chair.pbs.update; HTTP 200	127.0.0.1	2026-10-07 06:33:14+00
20a0a8db-6ab5-45ba-a969-8837fe1ef361	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbs.update	41451ae6-c2e7-43f5-a9db-7956b4b2f1a1	Role: Department Chair; Route: chair.pbs.update; HTTP 200	127.0.0.1	2026-10-07 06:33:38+00
5157f73a-1628-43e1-b3c2-813febe2b257	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbs.update	41451ae6-c2e7-43f5-a9db-7956b4b2f1a1	Role: Department Chair; Route: chair.pbs.update; HTTP 200	127.0.0.1	2026-10-07 06:33:59+00
bac19a99-a4f3-4d47-a5e5-6d80c107b4a1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 06:46:51+00
46d4a2e5-96e2-4129-864e-83463edaa5fb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 06:31:40+00
40b442cf-0c5d-44c5-bdcf-68d68af8b589	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 06:51:02+00
83c95f21-8382-4403-bb52-eec9f328d8f1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 06:53:52+00
59b3a4f9-9094-4ef3-ba3e-95942d4d7d30	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbs.update	7fc84490-540a-4441-90ca-118c5d7a6158	Role: Department Chair; Route: chair.pbs.update; HTTP 200	127.0.0.1	2026-11-04 10:59:51+00
e2ed1bf7-9254-42bf-bc5b-6c8fdac47cdc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 06:56:30+00
04d04d45-e443-49f3-8b76-c4e5d185d52b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 07:00:37+00
e427cc4e-cb17-45ff-8287-24cd2c1df4e7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 07:06:15+00
a9109761-8cf2-4c37-b9ea-4adbb4fa4b3d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-10-07 07:09:49+00
663d7e5f-f7b7-456f-bba6-1922e4a898dd	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 07:11:45+00
30b05470-8334-4669-b0d1-039d66b494a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 07:13:01+00
dcf60b3d-f333-43e5-88a7-f5427d8a5da5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 07:23:58+00
121d95f1-fcbb-4306-b13f-bf415bf8aba8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 07:45:59+00
a16d32f0-eea7-4c53-bac0-21da5f9e8e51	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-10-07 07:48:00+00
9d0d7779-11d9-4382-962a-71140bd73b2d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 422	127.0.0.1	2026-10-07 07:57:45+00
46fe59a7-20c9-41e4-b595-1524f2df4c43	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 422	127.0.0.1	2026-10-07 07:58:22+00
003d83a3-9649-45d5-a993-07ecb8167b95	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 07:59:28+00
849e3954-df33-4e9b-9dd3-bf8c89001cb5	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 07:14:01+00
bd99c386-3f8a-48af-b22a-b6b2a6103338	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 422	127.0.0.1	2026-10-07 07:59:34+00
1f06f259-4f69-44a5-985b-ba0422826740	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-10-07 07:59:51+00
edd8e1b3-cc73-4b2f-b284-96d7fa763c51	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 08:06:14+00
02408f19-7e13-42a9-a795-f0af0e816426	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 08:06:47+00
cca3a208-c422-47ce-9c7e-8611031e90fe	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 08:10:28+00
5e64bc15-46de-4391-a354-0b1fad911966	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 08:16:28+00
4faa142d-cfa0-4387-a2cf-6140159af415	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-07 08:48:51+00
623d09ec-b9a2-43f6-b73a-3dbc4c37efa0	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e926c91b-b05f-47fc-aa0d-0b42c2a77067	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 08:58:43+00
3f30cdbb-9fa9-438a-95a9-237d8837b1da	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e926c91b-b05f-47fc-aa0d-0b42c2a77067	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 08:58:48+00
8ebb5c14-5efc-48f0-bd67-73479027ef09	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e926c91b-b05f-47fc-aa0d-0b42c2a77067	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 08:59:09+00
64a9f044-4bba-4547-8dda-4180703c17a8	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e926c91b-b05f-47fc-aa0d-0b42c2a77067	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:06:21+00
4f150748-af86-4576-9fa0-e7d1e39cada1	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e926c91b-b05f-47fc-aa0d-0b42c2a77067	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:06:59+00
3d75ecef-55fb-44d1-8fcf-a3f56659ea70	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-07 09:09:29+00
133f017d-b94e-4ded-b1e6-51d9e328ccbe	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:16:49+00
878a83cc-52f1-4f7b-ba3a-aaa4ce0bae51	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-07 09:12:59+00
af25ccdc-f440-4488-893e-2fae142e509d	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:14:42+00
975200cc-5b3d-4b67-8f23-30b15506054c	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:21:07+00
06d94a93-fffe-437b-9769-5f3d7146729c	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:24:17+00
b05ec444-b361-47e7-acd0-8b721444ad9e	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:25:14+00
a5c9b4c4-db91-4940-ad9a-6d35ee021d9a	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:25:20+00
72d49916-f2ad-400c-af94-f3e4869feff7	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:25:24+00
bc152a65-8b2e-40e6-8db3-988a03bbb2ae	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:25:31+00
58dd4630-b497-4904-8ced-cf3f8e3417e2	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	99319d27-b14d-4325-baab-ea2be7fd93b1	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:26:09+00
79c30933-c93c-4b9e-a199-1f161ed6fcd6	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-07 09:28:00+00
e37b15fd-f35d-4c9e-acd6-f451c8aeff83	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-07 09:30:24+00
de228bf3-2905-437b-a423-8274acf6b513	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	3abae64c-f024-4897-940b-fb4531d720ee	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:33:07+00
393447fa-491e-4a16-9aa1-6f1671d08460	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e926c91b-b05f-47fc-aa0d-0b42c2a77067	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:34:06+00
1d6c9348-d451-4cf5-ace4-45ae2465bba7	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e926c91b-b05f-47fc-aa0d-0b42c2a77067	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:34:50+00
85703bed-5cbb-4627-8f3c-72ded80a57bb	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-07 09:37:34+00
acf44280-69e6-46a5-af0d-5de53328749f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 09:39:56+00
d7369f77-0c41-4da7-9122-c8bfb1d36429	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 09:54:54+00
ca01a1df-173d-4bf7-aff6-40fa986d9b75	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-07 09:55:27+00
2de69b6e-2d57-4f2d-957f-80c06ab0aebc	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:56:31+00
f64d6b1c-9db5-4506-9f18-b9dbdc940528	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-07 09:57:02+00
a6e72ca5-df1c-4e87-b8fa-7991d568d9aa	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 09:57:29+00
a9b139fb-7533-4c81-bb29-db34c9e085f9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 09:57:50+00
9c82b070-586a-4879-a527-c660248f4b0b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-07 09:57:59+00
7d183ef8-769c-4b4c-9855-a15da4616513	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:58:49+00
9cf5ab28-03d6-4548-8e71-a819a9b8907b	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:17+00
bad0327f-bfa3-4910-a4f1-9d5863a2a842	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:20+00
470dd586-ee23-4921-88ff-17087f861e03	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:23+00
d190acb8-1c1b-4b34-b849-4b34c236fc78	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:26+00
61a2421e-014c-438b-bbfd-584a918ebebf	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:29+00
c167e610-3136-4c37-ac62-7f591f36459f	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:32+00
32fce6cd-586e-4df2-8b2d-ad2fabcf1cd6	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:37+00
6281cbed-1626-4549-a214-4eab9b468c42	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:40+00
59daebcc-eb22-4c2c-8f0f-3914b31ff7d1	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:43+00
bf7fab42-d2f0-4903-9f18-f7aa51393ca6	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:46+00
6fdd38be-2dbb-4294-adbf-0262c69afffd	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:49+00
8b788e77-fed7-4eab-b9a3-3fb44fe3ffaa	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:52+00
1297519b-95ff-4b8e-927b-5eb8e194d371	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:55+00
f20631d3-00f9-43c1-81ed-d527755a8830	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 09:59:59+00
bede03b4-f3c4-4270-a532-9c2bd4c3e400	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-10-07 10:07:03+00
b939bc59-8203-4de6-829b-97e3fe7648a8	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-07 10:07:25+00
55cb1752-906c-45c4-866b-b43d7b633cc1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 10:07:37+00
aeacec94-9323-4df3-911c-104f669c6870	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:10:40+00
21a3a954-f092-4374-adf1-5768b67887bc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:11:57+00
0b9f02c6-ca1a-4c36-8e6b-4fb8c12e75dc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 10:12:27+00
743e4454-fb1a-4e83-8665-273acf57ea96	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	5732476c-7557-4185-a926-e2232be20e7b	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-10-07 10:13:30+00
705c86a2-02b6-4625-a75d-5eae43e36fc9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	d5d4df1d-f034-4da5-b72c-83f93cc49215	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-07 10:14:09+00
ab543912-7882-4e82-9dd7-2a886a2476b1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	9325480d-67fc-4b9d-b3e3-8ec424a30672	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-10-07 10:15:41+00
a62d412b-03d7-4f8d-9f5a-a98641503377	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:19:45+00
196959a3-3ead-4d3d-a70e-4483d5c6e388	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:20:04+00
fcaf2f74-0241-4697-82fe-ebacaa1c7a55	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:20:25+00
600d3d78-bb9c-4501-9cb5-0a8976dc0b68	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:25:53+00
b6af7064-cea2-46d0-a7b3-7cffc7dfcf29	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:26:23+00
b7cab61b-ca44-464f-a5a1-478e4de08ca6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-07 10:26:44+00
a85e8b87-f448-4414-a6ab-b79c6812453d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 10:27:08+00
7e298cb2-f63e-415c-871a-2bec6b35108d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	286e0afb-e074-40df-bf70-61b90dfa8b0f	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-10-07 10:28:20+00
f20e77f9-7dd1-4b7f-a26e-aa12a4dc5b3a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	286e0afb-e074-40df-bf70-61b90dfa8b0f	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-07 10:28:43+00
7d4952db-a8d5-4980-a362-ca07d6c1e92d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-07 10:30:57+00
0281e22a-aff6-48cc-883b-6e5db143b14f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 10:33:42+00
f7f6c804-bce7-4ab3-8cac-86a12b5bea93	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 10:40:41+00
864147c9-cd9a-4704-b4b4-bdf4269f6012	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 10:41:01+00
bb826341-39ca-4144-aba2-b786e1b3d1f9	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 10:41:13+00
a263769a-f0d5-4b62-913e-c064559e9cb5	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 10:41:40+00
dbbd98cd-0971-48fc-9e26-e20f6c67aee2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 10:41:54+00
f7315f65-0c60-42ee-8fd7-a3031dca3716	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 10:47:26+00
d67d1d12-2e1c-4d80-a55b-e3c9a7ae8d4a	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-07 10:47:50+00
bf6f59a8-e9c4-4e3e-b606-924e53653fc8	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	b8c258c3-2b38-42f6-ba35-6ab99c484b20	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-10-07 10:48:29+00
41d30cdd-7aeb-4bb6-870f-314262f2a888	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-07 10:48:49+00
932c1a62-2998-424c-9700-2d9d60a320eb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 10:49:03+00
0fba92c3-4dab-4fb1-b5af-70df3c227018	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 10:49:49+00
1b226b14-a8e0-44d7-8275-9935936317aa	32ab44ac-a571-4128-8306-8d6f38c050f6	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 07:14:08+00
3fee0800-c0c2-4a89-8d63-ed73911e1e8e	32ab44ac-a571-4128-8306-8d6f38c050f6	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 07:17:49+00
2d417578-b1a8-48c7-910b-33b412a588b7	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:18:51+00
ff22ce64-8fbb-40f0-a7fb-2ed08862448c	32ab44ac-a571-4128-8306-8d6f38c050f6	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 13:00:57+00
85a77b5b-7931-41ab-8adc-d6508c9aad97	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 15:38:52+00
45792044-991d-4b70-96f3-ad90a994ec14	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 15:53:11+00
ec8e5314-493d-42a6-8406-1466f928e6ba	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 13:01:10+00
e87c567e-34d1-45c7-a97f-6be4a7501b56	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 03:15:09+00
a660b470-a5ca-4285-b1e3-3bf887950fe6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-07 15:59:55+00
e5f7d218-276b-4d8e-8e23-1d09660720d0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.notification-preferences.update	\N	Role: Department Chair; Route: chair.profile.notification-preferences.update; HTTP 200	127.0.0.1	2026-10-07 16:00:40+00
19da16ed-6c69-406a-b665-39c53618565c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.notification-preferences.update	\N	Role: Department Chair; Route: chair.profile.notification-preferences.update; HTTP 200	127.0.0.1	2026-10-07 16:00:46+00
7ab35430-f1c0-4fa7-876b-b015be7cda31	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.notification-preferences.update	\N	Role: Department Chair; Route: chair.profile.notification-preferences.update; HTTP 200	127.0.0.1	2026-10-07 16:01:07+00
1380f540-a18a-460d-8dac-36bf958313f3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.notification-preferences.update	\N	Role: Department Chair; Route: chair.profile.notification-preferences.update; HTTP 200	127.0.0.1	2026-10-07 16:01:19+00
c0cae2b9-85db-46f5-ac9f-45b5a635eb85	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.notification-preferences.update	\N	Role: Department Chair; Route: chair.profile.notification-preferences.update; HTTP 200	127.0.0.1	2026-10-07 16:06:52+00
8c1d748f-95e0-4f68-ba26-128e19c024a6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-07 16:07:10+00
32aa923a-1c22-44d1-b9ec-6626e2b5c585	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 03:15:21+00
fcb2f039-7648-43e6-9ed4-de43873ec7aa	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-04 08:50:22+00
890fe94a-fa2c-47a8-96ec-455f42f38f45	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-08 05:17:48+00
0086eca7-654d-4218-8506-78b92d95e59f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-08 05:20:32+00
e0a660ea-5a72-4635-81d2-06d3cae17d99	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 05:20:42+00
8ad80a4e-365e-4986-aa9a-0eaeb1fc45c2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.profile.avatar.update	\N	Role: Department Chair; Route: chair.profile.avatar.update; HTTP 200	127.0.0.1	2026-10-08 05:21:39+00
e31c5ef3-d797-49bb-b01e-ed19da6c30aa	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.password.update	\N	Role: Department Chair; Route: chair.profile.password.update; HTTP 200	127.0.0.1	2026-10-08 05:22:26+00
76772fce-b495-44a1-9073-36df9edf678f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.personal-info.update	\N	Role: Department Chair; Route: chair.profile.personal-info.update; HTTP 200	127.0.0.1	2026-10-08 05:22:31+00
b98d5240-2ae1-49ba-918b-6fa56b78f524	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 05:22:42+00
8e91356c-551a-420a-aff7-f2f539685f8a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 05:23:04+00
ff8191c4-fb31-4b5c-b5c8-3de8010118ae	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 05:26:57+00
9db77ff2-e250-44c8-bf27-df98f13e45ef	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 11:07:10+00
efd5b93f-4a01-471c-8da8-f09ec4140091	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 11:07:59+00
6e1a4617-a896-427c-8be8-1973c4efad49	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-08 05:31:21+00
c9507b21-a9ce-46e2-9375-e6591a91e189	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-08 05:37:12+00
054ba346-1421-4c8f-81ff-3cdee8ef396e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 13:03:34+00
c3cc906d-7fe8-4dbd-b74a-77e6b1abbc9e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.fetch-schedules	\N	Role: Department Chair; Route: chair.pbs.fetch-schedules; HTTP 200	127.0.0.1	2026-11-04 13:33:00+00
4bae8cc8-a61b-4ea5-b223-a9225f7e693e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 13:33:22+00
28e45773-404d-4f83-aae4-f34ca3d2ea60	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-08 05:43:25+00
bf4ad44e-4804-4d59-8aaf-605fff4acb22	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 13:33:33+00
c9e6047e-51a4-42db-8f74-a5e598ef9ff0	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-08 05:45:40+00
e8c07507-3598-4dfe-9d69-6b7aa7d69ff6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.fetch-schedules	\N	Role: Department Chair; Route: chair.pbs.fetch-schedules; HTTP 200	127.0.0.1	2026-11-04 13:33:55+00
90de2589-fc4c-4bd3-9e1b-5994875a43b4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.fetch-schedules	\N	Role: Department Chair; Route: chair.pbs.fetch-schedules; HTTP 200	127.0.0.1	2026-11-04 13:34:34+00
a3ed57b1-a394-49ec-80df-f6e3a89639af	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.fetch-schedules	\N	Role: Department Chair; Route: chair.pbs.fetch-schedules; HTTP 200	127.0.0.1	2026-11-04 13:34:36+00
263f7534-fb1f-492d-982e-51a96fe08d37	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.fetch-schedules	\N	Role: Department Chair; Route: chair.pbs.fetch-schedules; HTTP 200	127.0.0.1	2026-11-04 13:34:39+00
6c80d444-d62a-4a45-9020-b257ccb768ef	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.fetch-schedules	\N	Role: Department Chair; Route: chair.pbs.fetch-schedules; HTTP 200	127.0.0.1	2026-11-04 13:34:42+00
7d36c3a8-f21a-4e6a-90f5-df24cd06b666	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 13:35:02+00
559d7ce9-af9e-429e-a7b7-0ddc7adb0552	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 13:35:13+00
70f9e12c-98e8-4f9a-ac71-40dea5f3e489	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-04 15:54:33+00
fcf61076-c9f9-4e26-bef7-92ccac7fb3f2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 06:07:58+00
7bb5999b-9f41-452e-86c3-c8855fb3aa8e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.notification-preferences.update	\N	Role: Department Chair; Route: chair.profile.notification-preferences.update; HTTP 200	127.0.0.1	2026-10-08 06:10:10+00
dd76528b-096d-4217-97f8-acae5ce03f5f	03ffd937-4f2b-4984-bce1-22b9bf2a29a9	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-04 15:54:49+00
981c6311-9128-4410-aa8e-244373f3c60d	03ffd937-4f2b-4984-bce1-22b9bf2a29a9	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-04 15:59:22+00
e97310c2-4b8d-4760-862e-7df4a5ae8038	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 06:13:36+00
3653400d-bc81-46d9-95bf-9d8a8aff1300	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 02:09:18+00
49cb501c-3d2e-4c96-8f2c-9d65e9110821	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	8a72daaa-ba58-423b-984e-a34c65a2e619	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-05 02:33:53+00
9a4ced6f-20b0-4e36-a3eb-24e7983009d1	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 07:21:03+00
4c5a13ab-a40f-4bf3-8964-90a7dba96719	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	a1fa86d6-84d4-4a11-9bba-251e088f4634	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 07:21:54+00
aa0a1e9e-4b27-4d0b-a4a0-d94ade9eb051	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 06:27:50+00
fb952213-b88d-4287-9d85-d6392a465679	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 06:34:44+00
0b0b2031-3f04-498e-af47-8a4670fba696	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-10-08 06:35:31+00
c670ac8b-b96c-437f-9c19-ca5862090f22	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-10-08 06:36:02+00
cf74f1d2-4a6d-4769-9dd5-76f222e941cf	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-10-08 06:37:09+00
4f50f3b4-e3bd-45e1-852c-631a21909e8b	d2468e93-6a49-4532-8eae-b5ab3f0dd090	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-10-08 06:38:33+00
fba18c40-5d68-4bd5-9232-640d2a30d386	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 06:39:07+00
7a89b9b3-1a8a-4626-87a1-6a2058304076	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-08 06:50:02+00
cbdc918e-a490-45b2-8995-0c32ba6a71a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-08 06:50:15+00
c1087e2b-6240-45d2-8c4c-2e7678ee21f2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-08 06:51:30+00
ebfff58a-972e-4050-a1cf-6cb0450f96a6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-08 06:51:46+00
e0b4cec7-4507-4608-a563-e04541247137	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-08 06:53:46+00
1b29d8a1-7c1d-402e-b763-5bf5fdd3eed5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-08 06:54:53+00
23ee2e0d-c4a4-4667-a68f-2f71c941f1d3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-08 06:56:28+00
b2983e4d-ffaa-4213-9c68-2591fd56dafb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-08 06:56:43+00
297ecca9-0ca2-4eb4-89c0-8e3c1bc1dc8e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-08 06:56:50+00
3a1ae49c-47fc-48ca-ab98-89c8c2fd7082	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-08 06:57:02+00
015f6304-8cf6-4b61-983c-938416c69f59	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 07:01:17+00
01ce46e9-4eab-4ca4-9dcb-3d426f989a73	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 07:22:05+00
35880267-40fb-4595-a977-b88f173a1782	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 07:10:53+00
1092eaf8-1856-439b-acd4-810204194974	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 07:11:27+00
986ad880-d5bf-4b0b-a43a-d9dac6b45739	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 07:11:56+00
8e1bb790-fef2-41e2-9cce-eeb761481dd9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	4f8aa09e-f4f0-4b09-b7cd-60e1f79c4f5d	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-08 07:25:21+00
34029b61-fdca-4720-a87d-2629259d47cb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	4f8aa09e-f4f0-4b09-b7cd-60e1f79c4f5d	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-08 07:25:33+00
53eb8d71-353e-448a-a177-30dbbe5086b0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	4f8aa09e-f4f0-4b09-b7cd-60e1f79c4f5d	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-08 07:25:40+00
7d9017e4-b5e2-43ef-a8b2-a09ee3a148e2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 07:31:14+00
237f7ce0-c88a-45a2-8db5-8606e86d4e86	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-08 07:36:16+00
19d4122d-bbea-4f3c-9a89-60084b8e2220	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 08:01:05+00
07589b56-300d-4df9-a897-f6d4c1f2a94c	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 07:22:15+00
c99f19cb-b257-4ef6-897e-944bcb685578	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Created / Submitted	dean.profile.avatar.update	\N	Role: Dean; Route: dean.profile.avatar.update; HTTP 200	127.0.0.1	2026-11-03 07:27:38+00
c2471309-c491-452d-8b27-08bdc36cb22b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 13:03:41+00
cb055e47-db9c-4027-80f0-560f8462c438	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 08:11:10+00
e81cecd6-8593-42de-927f-59db9676a2d2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 500	127.0.0.1	2026-10-08 08:12:15+00
efff07e6-3610-4167-bc1b-e7bb693f3f54	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-10-08 08:19:35+00
7f9ce966-5963-4ee3-9663-5e652b814f75	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 08:19:52+00
219565fa-2d9a-40a8-b0fe-9ce2aa8f18fb	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 03:36:50+00
d55205a5-c383-447d-8804-37c05b26ff6d	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-08 08:21:01+00
443ab7ac-6b08-40e6-b109-4a93fee93b7e	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 03:37:06+00
c73ded74-def5-44d5-85a6-1c1ad7eabef7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 08:21:10+00
74b995b5-5ec5-4f07-8c2d-e3b10f1f7e2f	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 03:37:26+00
dbb8e4cc-d88d-4f0f-8d75-e9d20423129a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 08:21:26+00
0aa916ba-bf74-4e64-8eaa-b1f6921f863e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 08:21:27+00
50f55afc-1447-49da-80f0-d35e520a9efb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-08 08:24:36+00
e1ae04e7-50b3-4993-a8e3-a2c278aee354	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-08 08:25:35+00
7b4ec449-6429-489d-97c1-dad3e212465e	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-09 02:39:46+00
cd5c6cae-d619-4530-9411-444965d4ac25	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-09 02:41:31+00
f62a8275-33bc-4742-8de0-152a98d58aad	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-09 02:41:55+00
ecd54cca-2a20-412b-b5ea-018cda196e94	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.save-draft	\N	Role: Department Chair; Route: chair.pbt.save-draft; HTTP 200	127.0.0.1	2026-10-09 02:42:26+00
591bfa39-d73c-4611-827e-e710563b8812	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.save-draft	\N	Role: Department Chair; Route: chair.pbt.save-draft; HTTP 200	127.0.0.1	2026-10-09 02:53:25+00
6b12938a-10d1-4727-8a24-1459d6e0256e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-09 02:53:47+00
f5a647ba-c9b5-4792-8ecf-9d488f6f3371	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 07:24:05+00
dfec7e9b-f635-41b8-adcb-17bc5201ec1a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-09 10:29:58+00
8d6a0ee0-1b07-4e79-9395-cacc5c643c4c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-09 10:36:40+00
23a47e8c-b65e-4d7a-83d7-de1a144a2ef5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-09 10:42:52+00
c365b391-ac77-4dfb-b488-68481194a434	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-09 11:03:23+00
9a31a721-d6b8-446c-b18b-82f789c9ba78	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-09 11:03:45+00
7859ef8d-0a6f-4309-9762-ddf7f68ae739	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-09 11:36:43+00
83f68ff7-e516-4a8f-8098-9176866b0f95	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 07:24:20+00
aa9d11fa-6966-40ce-bc99-7ec0687039df	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-03 07:26:21+00
998467f5-ff24-44bb-844b-90bccc7eb5ff	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-09 11:43:26+00
17fd7b9e-84dc-49b7-88de-953669a5d07a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-09 11:44:51+00
8a8d81f7-b3d9-40a9-be25-1b228da39345	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-10 07:44:58+00
a6915785-bdc4-41d5-8938-729bde2344ba	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-10-10 07:46:44+00
62334e72-4046-46e6-9fcb-9c86585d3972	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-10 07:48:02+00
45491d48-9fbf-4da6-8063-94b601268aec	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 07:26:29+00
243351f3-e88c-4584-af1c-4474643300b0	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-03 07:26:59+00
56382311-864e-4184-aab2-b01249c9908d	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-03 07:27:12+00
42f13f79-0100-4cd8-a163-b6af1826a0e0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-10 07:51:31+00
632a97f4-820e-429d-9db5-9c829f728548	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-14 05:22:02+00
e1fd80e2-1cef-4b6c-af74-fd6c54962473	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 422	127.0.0.1	2026-10-14 05:40:54+00
de3c4074-7d28-41b2-bbff-ea9cd97db5f4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-10-14 05:41:12+00
a00b0dbd-1605-4c73-aa30-2c4863e19afb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-14 05:43:20+00
7e0d6e29-4372-4807-b14a-b17d40b1b2c6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-19 10:21:35+00
78ed6a5f-14c4-43f3-b4fd-e82b53f4472a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-19 10:21:48+00
7808361d-2cbc-48ae-9ee1-a76d7a259ee2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-19 15:38:34+00
3d585a11-7a7b-4341-9fe3-6dedb8d97101	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:24:55+00
d9adb529-5ff1-4bcc-b915-de2fb0530f0d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-22 10:37:49+00
030b2727-ec2f-4bd4-ab26-f9693e7c5e81	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-22 10:38:08+00
ee501a45-a0a5-4f11-b5bc-017ec38dd30e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-22 15:09:09+00
8f6ef65a-6919-45e6-be72-eb30e9ea3b4b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-22 15:09:23+00
c98a0cf0-9dfb-4da5-8afc-12ad0f9713ca	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-22 15:33:55+00
b1400134-51a3-4d83-a964-c8cd0333807e	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-22 15:34:54+00
f7027272-c033-4b62-bda0-98463ad1ad8e	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:27:31+00
30e06daf-1837-4f1d-b204-36c3a47d55ea	80e78fb0-6908-422c-ae9e-cc31a7a29f04	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 08:27:58+00
db1d054d-4dfd-45eb-b3ea-3dd85c7ad866	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-22 15:56:59+00
afe20eb7-1d05-4f69-a533-38b80892927b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-23 06:31:32+00
f059af55-c6bd-4883-97d9-b8528cfb8f08	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-23 11:25:10+00
cf4b452d-0239-459a-8179-bb939761e55a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-23 11:25:30+00
57f180fc-70b8-4cea-81ab-75ed73600a46	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	8f56fa7d-5767-4df7-a072-450ed0a6275c	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 08:28:43+00
122fc8ec-b03a-49d8-9a7e-8dacc5d5bcea	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:28:53+00
301e3bf1-4658-4c09-858a-713ed729b7fa	03ffd937-4f2b-4984-bce1-22b9bf2a29a9	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 08:29:04+00
06c9de55-1ca3-4df4-8873-51ea07ede271	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-03 13:06:43+00
4cb1ec97-85ec-40fd-8d58-7552179a5f70	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 03:37:37+00
cdd8050b-528c-4567-b2b8-ce8c82175d12	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-04 08:53:51+00
cccca9c6-622f-46c5-935a-1ce46662fff8	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-23 11:37:58+00
242aa9d3-c7b3-4401-923e-d8f38e9e5f6b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-23 11:52:32+00
d2dcbe75-3a18-4443-b123-54cc2311693e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 06:45:04+00
fc031665-4719-47d0-af17-40f107660d67	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 07:31:44+00
2390d437-30f3-41f5-991e-f2c7005cf6da	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 08:54:07+00
ec290f29-575e-4be9-986b-52fceef54126	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-04 11:12:20+00
8b77489b-b334-4c24-8e98-096af470a152	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 13:05:23+00
eb1878d0-a623-4670-b321-c5896247f856	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-04 13:46:29+00
41b4e921-6ad7-4595-a934-dc01f2ba836f	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 16:06:56+00
4d8b134f-85f6-473c-9175-2f9afe626013	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-05 02:21:38+00
38416a25-3151-422f-8bea-65d2db5f8d72	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 02:21:47+00
0c1f0c96-f04d-4bd1-82aa-56f8c6f7dbed	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-05 02:35:32+00
a96c49bd-cd33-4c49-b70b-4c445bbd3a2a	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-03 07:34:17+00
106024c8-0ce6-4d4c-bd2f-b48b0bc6e039	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 07:34:52+00
48879df8-5478-41ce-9773-5810a6657a57	0180a717-2d4b-4d0d-a418-153b7b24911e	Created / Submitted	chair.profile.avatar.update	\N	Role: Department Chair; Route: chair.profile.avatar.update; HTTP 200	127.0.0.1	2026-11-03 07:36:51+00
0bab72fe-a138-4bd4-9e5c-90fd7a5d86df	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 08:44:13+00
aae61dbf-5e4c-4ee7-a4f3-c03f52f70b0f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 08:55:31+00
b93e1788-e07d-4478-a23c-ba010ea541c7	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-24 08:55:52+00
cbcf7a75-a357-4b7f-b091-276a0b81b82f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-24 09:02:56+00
9e421554-12ac-4157-91b6-ab801477b0e2	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-24 09:04:05+00
fe60a750-5beb-4d62-8ecf-02a81f1e8b23	270ad7b7-7c4d-4823-b314-816b91773cb9	Deleted	admin.users.destroy	30552f5d-6983-426f-8e9f-2854ab12fc24	Role: System Admin; Route: admin.users.destroy; HTTP 302	127.0.0.1	2026-10-24 09:04:51+00
945a49d0-7fe7-4729-a3cf-617aad146f43	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-24 09:05:02+00
e944e0e6-40ba-43eb-aa50-77227770f81b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-24 09:25:20+00
fa924821-db50-4951-ab03-60feb4eccb02	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	5a7014b0-d782-43d3-bd94-5d11cf36e5b5	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-10-24 09:25:59+00
0ff97bdb-4700-4181-9f84-6263a96e75a8	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-24 09:26:11+00
b60a65d6-3ad2-4171-ae00-21b257a9f9cf	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	e8cd8376-6a99-4344-8446-9159c05a9fe5	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 08:25:30+00
ef27a2ad-4e5a-4792-953b-c33663d04816	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:25:52+00
ca2fda86-861f-4c2f-89bc-3101a4db9b67	80e78fb0-6908-422c-ae9e-cc31a7a29f04	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 08:26:31+00
3c688afa-eb68-41e6-a963-1b38adedd205	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 13:37:11+00
5c043309-1e46-46af-8827-d47992a9c6d3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 04:02:28+00
7fc7f05c-9d3e-4286-9046-e5c870167520	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	b8474d63-164f-452f-ae6c-21371fc35819	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-04 08:57:38+00
775f388b-9211-4d88-911e-eb01e048dbb4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	41451ae6-c2e7-43f5-a9db-7956b4b2f1a1	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-04 08:58:04+00
9f6b1c1f-67f1-4444-8807-1d4b820b2e64	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	d01a5e78-47d5-48b9-aa5c-df571d00ee4c	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-04 08:58:20+00
83de1235-ea76-4fc2-88a3-efc4a9286a09	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	1c6cece4-b4ac-46f1-b960-8b9e77658d17	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-04 08:58:34+00
b3af62c2-5ef7-40af-8555-fde8043967de	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	28e14207-c379-4ef8-9461-657de7927bbd	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-04 08:58:53+00
89b6e4f7-c12d-4553-bc9f-d5f172e560d2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-04 11:14:11+00
52e8535d-9399-4621-a86b-90a36f003972	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 09:47:58+00
e43ca6db-4132-4651-8990-e5f0c05afc55	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 09:49:22+00
b6699a62-4ed9-4464-a748-43294755ccb9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-04 11:14:35+00
db47e0f3-57db-4274-b311-29c0e985f542	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 11:14:56+00
1b029a65-69a8-4ccc-9439-1f1ecab6cf85	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	4bdfd2a0-265b-4ed3-89cd-696011f3a8d7	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-04 11:15:16+00
26e0b962-8407-4ef3-8627-8bc961c8caaa	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	4bdfd2a0-265b-4ed3-89cd-696011f3a8d7	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-04 11:15:30+00
5edead96-1d6d-416b-8799-2cc11b7deeca	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-11-04 13:07:30+00
1a7e58d1-d1d3-441d-b28e-1a769dbf9e1f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 13:07:43+00
d07c70e3-e589-4214-8fbd-03da80147f63	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 09:55:17+00
38fea78f-4adc-4833-b14f-9fad44816dce	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 10:14:58+00
7dd2f63d-8419-433a-a801-89c4e300de25	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 13:07:57+00
68fe2572-26ee-4c33-b638-ab5115bb94d8	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Created / Submitted	dean.pending_approvals.return	a36a47b5-6030-4fc0-b7eb-6dd251e84c6c	Role: Dean; Route: dean.pending_approvals.return; HTTP 302	127.0.0.1	2026-11-04 13:09:49+00
198849a8-b85d-46df-a420-e8791578e639	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 13:07:38+00
103286ce-a0f2-4010-a29f-3e47460089ce	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 13:09:51+00
a486f0e7-e859-49fa-b4aa-64cd9a02beb6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in with passkey	webauthn_credentials	01a16e08-0d3e-7211-981f-fac062f7eb99	Successful passkey sign-in.	127.0.0.1	2026-10-24 13:10:26+00
4335b6a5-6d14-4d23-86a3-0c98c1f7647b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 13:10:39+00
5d799a9e-c5d8-4e8e-a697-99d0aca616bf	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 13:09:56+00
58f7e75e-6120-47e9-b51b-adf8005aedde	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 13:10:07+00
087e2c83-3009-49bf-8c5f-c166335f9c9c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-11-04 13:10:48+00
d6693c6a-b917-497b-b746-cd7ebdad7dc6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 13:10:58+00
1cc88619-ca88-4a96-8877-bcbb3282e850	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in with passkey	webauthn_credentials	01a16e08-0d3e-7211-981f-fac062f7eb99	Successful passkey sign-in.	127.0.0.1	2026-10-24 13:12:33+00
1868fe8b-4dc7-431e-9c8f-70a91731c40c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 13:13:10+00
02873975-5612-4019-85aa-a0549919a90f	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 13:11:07+00
9fd57ebb-e16a-4776-98b4-e56e1572eac4	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 13:12:02+00
148be519-8435-463e-820b-8e1ab8a59712	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 13:15:21+00
755647b4-ac27-4b6b-8768-dd3bd868f8f9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 13:16:40+00
f579461c-bdca-4bfd-9331-50ad7f29410e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in with passkey	webauthn_credentials	01a16e0e-4a73-70d8-8cff-228146efd7ac	Successful passkey sign-in.	127.0.0.1	2026-10-24 13:18:08+00
fa67509d-5fba-4961-b43c-de3e55796067	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 13:18:20+00
3cfab12f-9da7-4752-b4e4-4d212fb1ac71	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 13:38:31+00
696d3620-392a-40b6-90f6-3286e6ac4d74	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 13:53:09+00
e141756d-44ef-4c0c-8c11-5b225b56de1a	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 07:37:18+00
d79f05ef-790b-4f66-a4a1-e04c852673b9	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-24 15:50:17+00
d2e39d03-765a-418a-a46b-2221eb5fce21	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-24 15:50:38+00
f2028a1b-2076-4006-9c54-1577da214399	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-24 15:52:53+00
3cfac692-ab4f-40cd-bc37-c04217432a67	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-24 16:15:29+00
23b77e7a-8c69-4210-8fd9-6c3ddd83995e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-24 16:25:12+00
b1208fc0-73da-47ed-9dbb-1127cfeb64bf	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	4f8aa09e-f4f0-4b09-b7cd-60e1f79c4f5d	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-24 17:54:31+00
2c1204c5-9eca-4649-9a86-1528ca78594e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	4f8aa09e-f4f0-4b09-b7cd-60e1f79c4f5d	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-24 17:54:39+00
06ef7bae-d268-4cf8-9976-6fa9f447a030	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	4f8aa09e-f4f0-4b09-b7cd-60e1f79c4f5d	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-24 17:54:53+00
e51df996-aca1-4032-80a9-804c38aeaf60	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-24 18:06:24+00
d2f7b609-db64-4e03-94b1-09f7314c429e	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:30:05+00
0fd154b1-2217-443f-aa57-8ef230f652d9	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	1fb1299f-33d4-4e86-a994-b5ad75e7b435	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 08:30:30+00
83f136bc-7d62-4d6c-9e43-1290d7461f33	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:30:41+00
56da367f-30cf-4cda-bc8f-e02e91cf5b6f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 15:18:30+00
156953c6-fb92-4433-9b53-85c2bc2fafbc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-25 07:54:44+00
04e953cd-4053-4725-aaa5-da04aaba6c2a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-25 07:57:16+00
ccdfe330-9ac4-4eb2-9de6-cea0036d234f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 05:37:28+00
c58698c0-a5b1-4b1e-ae31-4419701847ad	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 05:37:38+00
217ece45-c5ab-4470-aeb6-28af527c9113	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 05:19:14+00
0cd6c268-bb3c-410b-bece-5b02e1631e3c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 09:06:48+00
d896fa24-a870-4090-8225-6db2b8bc9257	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-26 05:30:00+00
a29342e2-ff2f-4bae-9196-6a63060b3c29	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-26 05:31:02+00
e65f3cc4-4e0c-4b9c-94d8-6b70c2673da7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-26 05:31:53+00
0e100eaa-0222-4f16-9851-4425f5f0c524	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 05:34:31+00
2031e6a2-5c28-45af-a23c-24f856c4152b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-26 05:50:22+00
13e4b280-0ebb-4b32-88fe-447448c2f3d6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 05:55:11+00
56b91a7c-a08a-4c2d-9c30-1f6947198fe3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-26 05:55:24+00
fe824815-45f5-44fb-ab9e-467d6449bc82	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	72919f13-da69-4641-8d31-04d53d8654eb	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-26 05:56:16+00
c91e48dd-c80b-4a2a-a696-7e2e60f962cd	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-26 05:57:19+00
ca5de4b0-9b18-4b69-9402-74f022f2deeb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-26 05:57:47+00
39ad9db3-f6c9-4ffd-8bdb-772f3cb647c2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 05:59:12+00
caa73b0a-0466-4634-a120-faa5bc0ef7dc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 05:59:36+00
7f82b5dc-4336-46f9-abfc-f3d1de291e21	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 05:59:52+00
b7f2c6f1-414b-41ee-85af-9761fdf07c53	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 06:00:04+00
e505cf29-b21c-418d-85d1-96b33b7410ca	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	6f3509ac-606e-4d32-9bc6-3df0e6d41410	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-26 06:10:33+00
79594cdf-3362-4bf6-a1d5-cc2d054d3bdd	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	941738d5-2e64-4ec7-b098-c70eaa0b7aa0	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-26 06:10:44+00
d85ce931-4097-4de7-a35d-09550fe3d75b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	17b4ff84-96cd-4819-80d9-e3a2c4acaacb	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-26 06:10:54+00
ef043067-c694-4963-b736-a1d3f49a503e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	3225c1e2-d642-4acf-a902-32da7ed36f86	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-10-26 06:11:08+00
f0198a54-e9be-4e7b-8ad2-1408535fc519	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 06:12:01+00
87625798-9ce0-4e55-95c3-c1b254963949	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 06:12:55+00
cb7249c0-b68a-4b5c-843d-ee8e789535b0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 06:13:11+00
0e09397e-2447-4c02-a50e-40525da86a65	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-26 06:18:09+00
0f4d6cc1-9598-42f4-b9cf-236cd45934bd	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 06:20:18+00
049b4d46-d4e1-4ae7-886b-b51a0973c711	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-26 06:20:26+00
a0fd89af-717e-47ce-bc56-09806d98d505	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.rooms.store	\N	Role: Department Chair; Route: chair.rooms.store; HTTP 302	127.0.0.1	2026-10-26 06:21:23+00
6ebb4b26-5e74-419f-bb6e-e6fdb1ae9ea0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.rooms.store	\N	Role: Department Chair; Route: chair.rooms.store; HTTP 302	127.0.0.1	2026-10-26 06:22:49+00
12fbd9c6-cfa4-4fa5-93f6-f03e128c0536	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-10-26 06:32:58+00
2fba7651-f5af-427c-ac17-b523956d593b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 06:33:22+00
05d91c00-e1e4-4b58-bc4f-86aef0d716b1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 07:39:15+00
d483bd64-4abf-422b-afda-415c2c9f3b0e	03ffd937-4f2b-4984-bce1-22b9bf2a29a9	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 08:30:59+00
d40cea0a-b007-498f-ae29-7c106dfd7202	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:31:11+00
0009e178-62b4-45bb-832c-0e6bf280fbb3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 06:35:20+00
ee1eb4ad-0086-459e-8bf9-3db7b7074fb8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 06:41:29+00
f09e4d77-50d3-45da-9404-25826de9458b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 06:41:42+00
ea8dad14-d5fa-4554-bd1d-49fabd0c309e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 06:42:37+00
7eed97e2-ec0a-4f1a-b651-2f8fd5aa8bf6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 17:42:30+00
01b34e0e-6c7f-4d40-8db4-b100f3ea00cc	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 05:50:36+00
106a3954-f38e-4172-b00e-c095f2b7006b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in with passkey	webauthn_credentials	01a176f2-4224-7056-82c8-dfafe8d8ea36	Successful passkey sign-in.	127.0.0.1	2026-10-26 06:43:40+00
72742f81-4127-4bd1-a08a-dcfa62dfcf32	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 07:02:55+00
29095929-0704-4340-aa74-0950f6aec837	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 07:03:07+00
2d30ec3c-810f-43a6-a499-02d71bce815f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 07:07:43+00
5630da6e-c653-4cff-bddf-22c1e67f03ee	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-04 05:51:42+00
da967846-055c-4fed-98c8-37bfb8ddc186	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 12:22:19+00
bae0395b-d209-42d6-9c6a-bcb366a234b7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 13:34:33+00
5087f131-1198-479a-b94e-804a904d210f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-26 13:34:45+00
b0068dfc-a626-4f8f-a79f-30cf695117f1	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-26 13:36:56+00
2191d131-527b-4b45-a1ca-26f3705b5952	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 09:21:40+00
f2189d53-1fd4-46f5-a96f-e728837c6ee5	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-26 13:38:35+00
d9228e1a-8142-45eb-8780-6c67c317d9c7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 11:25:49+00
c7f8e39f-12d9-42b8-86ab-94f7c058099c	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-04 11:25:58+00
aef434a9-767f-4cf3-9f3f-a85e0e6d9622	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 13:12:16+00
0e6a2df0-0ab6-41c6-8add-bc3bf8baaee6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 13:43:15+00
a32fec8b-2fbd-4459-88f5-7f992e59e511	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-26 13:52:04+00
3770b241-effa-40da-955d-67bc26c8a94c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 13:13:23+00
dd75c210-0c66-4aa6-98fa-bf3b68921acb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-04 13:49:10+00
a2f7e008-6c86-4cc6-abc1-007347f8028b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 15:44:59+00
f0622006-004a-4842-80f8-5e38b6ed8f58	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 16:00:42+00
591f649c-32ac-4dcb-ae1d-17dab8c957c1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 16:18:52+00
2a0c7509-08bf-4af5-ae0d-c6bf5e973be5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 16:18:59+00
33e31782-2cfc-454d-adb2-855a62d09038	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 16:19:54+00
a10ab303-2cdf-46e8-82a5-24deaa4bfe64	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 16:20:05+00
3373f224-4805-4444-a94e-d3b11ac838d4	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-05 02:27:25+00
2426c49b-dacf-40eb-8af1-ec4c29302b97	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-05 02:27:46+00
5addde50-7b37-47f8-87db-ad979469312e	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-05 02:35:46+00
fdc33f31-dc8f-459b-9f2a-bf79b03a67d3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 02:51:05+00
b6d45bb9-2b58-415c-b1a2-9c01ba5864f3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 02:56:30+00
e0063c1e-624e-475b-9ae3-92022b6c7199	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	57ee0c8d-7afe-41cd-b43e-7754006959f9	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-05 03:05:26+00
5c8906c5-3c91-4b58-b8b1-b18bf0057168	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 16:28:05+00
58d2df9a-4f84-42f6-a2ea-b097f8367463	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 16:36:07+00
d0f2f9da-000c-4c9a-b4f8-cd3a501adef6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 03:15:40+00
cfa680cd-6142-4a81-adc3-b2304fce95f8	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e891982b-d1b2-45c8-a64f-e2c5615e433a	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-05 03:16:29+00
bdacf20b-562c-44c7-97af-5250a42bc1d0	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e891982b-d1b2-45c8-a64f-e2c5615e433a	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-05 03:16:50+00
594b4359-0df6-45ca-8d9b-8674723565d3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-05 03:22:52+00
e2834cff-274d-423d-99ca-76ef449c2e97	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-05 03:54:52+00
6d37ed71-e1a7-484a-9e2e-149c5efcb295	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-05 04:02:20+00
bda059e4-f06a-4604-8337-45bbb965f01a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 17:08:43+00
e6abf8ee-3368-4ea3-b15f-a6989804718e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 17:17:39+00
24ecc85e-c00e-49b9-99fd-ac051e92dcd7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 09:14:33+00
978c7152-58d9-40d3-8c1e-efcd7db7a69a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 10:45:14+00
4d154639-f0b8-4905-9a97-7d3bedf9b689	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-05 14:46:45+00
7ce37846-5059-461d-a421-3b08b7d664b2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-05 16:37:02+00
f641c527-7df5-4a49-8bf0-73c96870de33	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-26 17:45:53+00
a43a7832-5b9e-489b-9c62-48f3a5682f63	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-26 17:53:56+00
dde6678c-34f8-4a1f-8aa5-92ac1acb04e8	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 07:45:18+00
31ed430d-45dc-4fa2-895b-ffb11bf741aa	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	f4621259-120e-465b-9ec7-567c11393625	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 08:32:26+00
c5b40394-05f7-41be-a551-2973ffb28b51	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 08:33:19+00
4bb0b311-dd34-47d9-9ee4-cc7ea016bc14	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 03:43:10+00
418262d3-85f4-439e-b7f5-071b75d61786	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 00:22:16+00
a7c030c4-49a6-406a-bde4-f84080f52ea2	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-04 05:52:25+00
0a3e60b5-e422-42a3-ba6e-1265620ede8b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 03:52:46+00
978377f5-1c80-42b2-a0e8-e5774fba8f14	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 03:54:52+00
8d023f8d-c876-4181-a7d5-98f5fc375391	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-04 05:52:29+00
dfe125eb-222f-4414-a165-5f78843f2297	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-04 05:53:39+00
0fc11dc7-7ad9-42d8-a79c-248e0b054e66	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-27 04:06:25+00
c95b2744-bb5a-44ce-8e7d-ee32b9f2c082	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.reject	168f3fdd-6615-4e26-8c6e-1ef2ff17c493	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 302	127.0.0.1	2026-10-27 04:07:26+00
a85132a0-b76e-4907-9b0c-023e44fb947e	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-27 04:08:04+00
cc5f5efe-03ee-4e13-82e6-ca8c86164053	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-27 04:11:13+00
496fbcc3-61e2-46b1-a593-c15bb01879db	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 04:11:19+00
a68be3b1-eaaf-4f0a-99f1-b2db8d929eca	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	85e50b88-29c1-430c-9f9c-00b8567b5e68	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-10-27 04:11:59+00
eb36e8b6-c40c-4eaa-be80-5ca54886b687	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-27 04:12:11+00
b6693448-4d43-401a-944f-189c5f4d8282	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 09:57:17+00
8cf246fb-18bc-412f-a6ab-d4d40d72fde5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbs.destroy	232f6a43-87f3-433f-8048-bd2d51eb58ad	Role: Department Chair; Route: chair.pbs.destroy; HTTP 200	127.0.0.1	2026-11-04 09:59:34+00
21d0db7a-5097-4bac-9070-63c719eaa8e5	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-04 11:35:01+00
93ee39ea-0a41-46a2-88c9-4d2273c17613	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 11:35:16+00
375e12bb-c65c-4cdb-9062-4f47a92bbe52	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 13:14:10+00
ed5d4d17-4f87-4c6d-890a-6ae089956710	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	8a72daaa-ba58-423b-984e-a34c65a2e619	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-04 13:14:35+00
00d587e4-54dd-43c0-b1e9-c6f243bd06a5	e891982b-d1b2-45c8-a64f-e2c5615e433a	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-04 14:24:35+00
15a96019-b3c0-4490-97c4-07a25bd45c76	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 04:20:05+00
f5af51a3-16db-4f72-be75-3fa244aef0cb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 16:21:41+00
5a323c0d-3b5c-4861-919f-0988cfda0307	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	8b3c02b2-40c2-40f8-9ff6-90a8d4ebb4f7	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-05 02:31:07+00
b2e19db4-192d-45e0-ae44-dd1d0786244b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 04:21:47+00
e3152bdb-dbdc-4dfa-8305-df835a74da31	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 04:27:22+00
3dd520ec-d794-415f-be6e-2f7d621a6f59	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.pending-accounts.approve	8b3c02b2-40c2-40f8-9ff6-90a8d4ebb4f7	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 404	127.0.0.1	2026-11-05 02:31:17+00
c993dec3-958a-4e0e-bfc9-9be08227edfb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 02:48:58+00
ff541ae9-31b9-4194-921f-59c56dab10c7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 04:39:25+00
c2e0e12e-084d-4492-84de-0f3852ca973b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-27 05:02:19+00
973fff7d-d700-471a-b58e-2bd08cae53c8	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:06:40+00
900c0bb5-e1ee-44b2-875c-e1a58d263239	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:07:56+00
e40c65d6-d91c-43e3-b029-6b4f009d66b4	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:08:27+00
3380edd3-3e05-4542-a9ed-910a4503033a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-10-27 05:08:44+00
f11e060b-1ee0-4952-99ec-77f4e5f5a603	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:09:01+00
982617c9-4f44-4ed8-96d4-2e4543b5e748	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:09:57+00
d2b958b2-2c01-4fa1-ab3d-c71e76c4d160	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc3-0846-70f7-838f-78f46a9c229f	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:10:15+00
323f1581-4c51-4ab3-bdac-2f78ece3d264	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-7ffa-70e7-ad0b-1425dec9e6cf	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:10:30+00
71d2b91c-f828-477f-8074-20744eeacda7	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-0543-7291-8245-b4aa9535a39d	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:10:42+00
5777f37f-396f-4518-8297-f5e0a345573a	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc0-df9f-71d9-9c74-d3c001aa7636	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:11:32+00
20d8b710-9f85-430e-877b-3825e58755fb	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-0543-7291-8245-b4aa9535a39d	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:11:42+00
de228de2-1ab2-43f0-bb05-6ba7bc8d0c62	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-0543-7291-8245-b4aa9535a39d	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:11:46+00
47177709-824a-461a-88e4-2194c9d3d964	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-7ffa-70e7-ad0b-1425dec9e6cf	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:11:55+00
22df1bc8-25c4-412c-98f2-655872478176	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-10-27 05:12:04+00
6180869a-29aa-4843-a223-296f28a3c463	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc3-0846-70f7-838f-78f46a9c229f	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:12:22+00
adda692c-eb37-48e9-b55e-d54dc4d75ee4	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc3-e393-732e-936f-8d7d53bd790e	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 05:12:42+00
37500127-dfd8-4446-9fc8-af73c5e85dd4	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:19:15+00
cbf098c3-e609-48ea-b2e5-753a5c20bb05	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:19:52+00
457ef7ed-9c7c-4a4c-b44d-0d4bbfc5cea1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-27 05:20:25+00
7967e9b8-9676-40e4-af12-097f4fa0b7e2	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 05:20:51+00
27e7d159-389d-4678-ac0f-d5d79c6a4b59	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	2b58fd60-f2fb-4920-84eb-85cec962a211	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-10-27 05:20:56+00
093c8b9c-b804-4bd5-bd0a-04939f4e6e08	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 409	127.0.0.1	2026-10-27 05:22:14+00
ea2f67ff-856d-47f9-8140-55d57032f157	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 409	127.0.0.1	2026-10-27 05:25:15+00
9d1e691e-b75f-4ebb-938b-57665a4c40d8	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 409	127.0.0.1	2026-10-27 05:27:56+00
88cf8d3b-29fc-4add-9251-d6496824867a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 05:40:01+00
646cc510-e8c2-48f6-87f6-6ed9f2e4114c	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-27 05:40:12+00
d1b9299f-f4b1-4952-aa2e-102690bb104e	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-27 05:47:09+00
39c7a43c-71ce-4fd7-a520-8f2067fb780c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 05:47:26+00
f3f6da94-66cf-4d9f-978c-fade011347b1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	0ea3a218-045d-4506-a906-34ee83d7b2af	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-27 06:01:35+00
c4df4c9d-ae46-4c0a-bc98-2d5d62af0379	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.mis.code	0ea3a218-045d-4506-a906-34ee83d7b2af	Role: Department Chair; Route: chair.mis.code; HTTP 200	127.0.0.1	2026-10-27 06:02:01+00
801083d1-83c7-4622-a40f-6850c192330b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 06:07:32+00
7ff4e883-07ca-49b4-9314-4b805aceab15	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:08:27+00
e20875e9-9807-4d27-89f3-3b4e01841511	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:09:29+00
9102e8c3-518f-496e-a468-e3b344455baf	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a176e0-3cda-73eb-abd5-d05a057346cf	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:09:42+00
afa6d770-11b2-4f7d-af26-e30a6cdfeb91	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bcd-ddbc-729c-84e8-c2965d70ec2c	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:09:59+00
772184a6-4983-47ae-8205-e5ba3d098a01	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc0-df9f-71d9-9c74-d3c001aa7636	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:10:26+00
4968f1c6-c641-4634-af20-937179676769	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-0543-7291-8245-b4aa9535a39d	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:10:36+00
b66fd881-8417-40eb-ab76-853844e7bcec	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-7ffa-70e7-ad0b-1425dec9e6cf	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:10:43+00
65606b5f-1517-4f29-b0e3-96e03a83e75f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 06:10:53+00
d0f85ced-52f4-48d1-ab9d-188fc5456923	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc3-0846-70f7-838f-78f46a9c229f	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:10:57+00
e5742875-a765-49a0-be57-6cfda92ae391	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc3-e393-732e-936f-8d7d53bd790e	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:11:20+00
466b3011-8ffe-4f95-88f2-bbec585e52d1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 06:11:32+00
051af83a-aa78-4769-8525-75f01a6e8ce1	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bcc-65bb-7120-b431-3c89f25ccded	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:11:36+00
c6e1cd4d-5340-428c-a17c-bd098ad0ff0f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 06:11:45+00
1bb41f81-9196-4460-8148-4e5e4c1fbb5d	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bcc-f742-7317-adf8-51246c2d8cdd	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:11:46+00
f0a03e28-34f8-4978-9b13-546a4f740563	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 07:49:05+00
8262a92b-f041-44cb-9d22-1fefa0bcd7d9	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bf9-6f71-7398-b483-83e9a64e5ffc	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:11:57+00
92d16f5e-c34f-4fd6-97dd-fc784c68303c	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:12:36+00
11abdfd9-ed7b-489d-93b4-64627dd4a85c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 06:12:54+00
7e838127-634a-4592-bdba-8c4f32bb6bde	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:13:02+00
0a25a5d0-2519-4a0b-ac24-cda8758e6f93	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 06:13:21+00
265fed18-7b71-475e-a240-77566bd2e3c6	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:13:29+00
2dcb5ffd-49f6-4bee-8ce7-d1b6295dab14	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:13:50+00
3a134598-7531-42eb-91cb-b96fd5ba0cbb	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 409	127.0.0.1	2026-10-27 06:14:16+00
d8f43b9b-4fcf-4072-b76f-11f6f6e07b61	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:14:35+00
8195e5cd-3b34-4c6c-961f-8a320bd40de5	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:15:21+00
7bc8205c-49d9-4962-a1cf-c4399ef5e708	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:16:51+00
f838cca9-6977-4861-8903-eade80365ad0	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:17:21+00
8145a433-1a9c-48b9-b4bb-4cb6bb7c3999	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 06:17:52+00
78188b7c-515a-474d-911f-c1789401e97a	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:18:04+00
a0255d8b-3fa2-4db5-8315-de50b8c5d655	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:18:37+00
9d228566-5950-42a7-9fd1-b5c80c9f893c	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:18:55+00
0d05743c-e4b5-468b-89d1-73b0fdfda07b	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 07:50:58+00
2cdba7a4-01af-46d4-af7e-a02680a6911e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 08:33:06+00
997266c2-2394-45b4-b449-3696352d9b13	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:19:37+00
19dd097a-f59b-4ed9-8bb6-8695bd3030f1	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bcd-ddbc-729c-84e8-c2965d70ec2c	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:21:58+00
38ff41ae-a51a-413b-bd44-b25b97967b69	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a176e0-3cda-73eb-abd5-d05a057346cf	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:22:08+00
2336cc4a-68e5-481b-8fb3-e202d5bc197a	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc0-df9f-71d9-9c74-d3c001aa7636	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:22:15+00
16419009-1948-4902-915d-57fbf8244ef3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 07:51:07+00
7e90e375-0751-404d-b3b8-77e0f60a4963	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 06:22:30+00
b1ffeca9-15f6-43ba-8f14-88505b0875d1	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-0543-7291-8245-b4aa9535a39d	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:22:44+00
0678ecf6-7347-43c2-9a51-df7b5f65bf7e	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc0-df9f-71d9-9c74-d3c001aa7636	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:22:53+00
e41b87ff-f43d-48bd-9f98-ff42b749b6fd	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a176e0-3cda-73eb-abd5-d05a057346cf	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:23:01+00
d924498b-b85f-49f1-9eb3-308115df4835	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bcd-ddbc-729c-84e8-c2965d70ec2c	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:23:11+00
106b2784-acfa-4140-ae2b-df585fb2328b	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc2-7ffa-70e7-ad0b-1425dec9e6cf	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:23:31+00
cfd0453b-446a-48ff-8d13-60e9f847f8c8	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc3-0846-70f7-838f-78f46a9c229f	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:23:43+00
109dc5a9-3798-47d4-9f19-6bfd351bdd86	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 06:23:56+00
1425e38a-7b94-4cc7-9bb5-1e127a449145	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bc3-e393-732e-936f-8d7d53bd790e	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:23:58+00
16a2882c-8b14-48a9-b828-ef4dde3a5621	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bcc-65bb-7120-b431-3c89f25ccded	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:24:43+00
7cee736a-1db3-4d51-aeb8-ea9f6529ef66	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bcc-f742-7317-adf8-51246c2d8cdd	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:24:56+00
577e438d-ac75-4b84-9ff0-56d48afe899a	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bf9-6f71-7398-b483-83e9a64e5ffc	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:25:24+00
59bd86b7-3c44-439b-bd96-7b364496e2c1	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bfa-6247-73a9-b57a-90b0e8213149	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:25:34+00
0f5c3b60-315d-44ca-bcae-19c612b5dac5	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bfd-3b82-7254-8b81-4481eef3759c	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:25:43+00
58decdce-c964-470d-95c7-abe3c11c9135	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bfd-a2c0-717d-9ac9-7a2d9664898d	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:25:57+00
56be96bc-7048-4956-97d0-38eaf6e431c4	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-03 08:33:39+00
205ed580-af1d-4774-a71c-ac08293fd49e	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bfe-0c49-70c5-bf4a-04ed2299b141	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:26:08+00
df62be88-c899-49fc-b5ef-63961f2b9e7b	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bfe-5c34-71d1-92de-103dcb66defe	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:26:21+00
be1aa273-bd9e-4509-abb9-aab9e3fb4c50	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bff-0d7b-739d-b84a-467ca4fc5fa7	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:26:34+00
c5e05d92-8710-453e-86e1-e26cdad2bd5a	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17bff-bf7f-7254-b603-6c7f491731ee	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:26:44+00
490add58-d5eb-43da-bc27-6d3c72b52c55	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17c01-22df-7386-9f83-908d579df931	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:27:08+00
85bb2789-5ed8-4fa9-a20b-78aec396ee92	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17c01-9594-7329-9a15-d70c42621d70	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:27:18+00
02a4c5c9-c77c-4e02-b731-75b8be981737	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17c02-3fe8-708f-a00c-04376fddf85c	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:27:28+00
43253411-d132-4e0c-a9e0-e8e57b93deba	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17c02-bfb8-7070-bbde-b163456119e5	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:27:41+00
c199b119-1740-4bd6-986b-a9ba72bad3ba	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17c03-045a-71f0-8a7a-b235f135a727	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:27:51+00
04ad03e5-6dd0-4c7c-a8d2-7ed8aa5eb55a	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.rooms.update	01a17c03-a9f0-73a5-87be-99e46e353412	Role: System Admin; Route: admin.rooms.update; HTTP 200	127.0.0.1	2026-10-27 06:28:03+00
51f7661b-fad1-4636-8038-12894cf4210a	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:28:27+00
3b68129d-38e1-46a7-ab8d-74efd183807d	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:28:50+00
846ed08a-ab7a-4e59-9165-872a06fec139	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:37:25+00
649f648e-ed7e-4c5b-969c-e0429c83613a	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.rooms.store	\N	Role: System Admin; Route: admin.rooms.store; HTTP 200	127.0.0.1	2026-10-27 06:29:13+00
e2c1c077-9172-4370-bac8-5bb47178c17f	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 06:29:22+00
ccc07b8b-8409-4757-aa47-34a1bdf8c124	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 06:35:03+00
467aa58c-d828-4ddf-b494-8e77fc533463	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 06:36:31+00
68b04dd4-02b4-4084-a8c4-ad269d61776c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-27 06:40:33+00
95e644a9-4ef6-42bf-ac80-8b880e5bb32f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-27 06:40:48+00
68a787c1-dde8-406d-86ad-1f3583c5186e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 06:41:28+00
1a9f456e-0109-4580-b47a-fd599191f31c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 01:11:16+00
e52d4159-058e-444b-ab1b-ea10a37f442b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-27 06:41:51+00
03d4ea77-a583-4437-b7d5-70cf81e14ffe	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-04 01:11:36+00
c76641e3-5726-47b3-8995-0984c4cf36da	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 06:42:38+00
06217729-1527-4102-9e34-884e739dc5cb	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 07:17:26+00
cd269ac5-f891-40e2-a605-41dbc8a4b6b6	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 05:54:33+00
c7344531-09e0-4270-ba3c-0d96c3f5be61	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 05:55:52+00
b9aa8bb9-17f8-4b13-a613-271c77b36f30	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-04 05:56:08+00
5456639f-674e-4aad-87cd-c8396639d47f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 01:52:17+00
2e8ff170-96c8-4eb8-99d4-974bf22cdbe8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 07:34:30+00
1839ed97-e331-4cd5-9355-097c9862a0a4	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 07:52:26+00
b5dc0de6-3edf-483f-9ef9-def073038dc9	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 07:34:45+00
0226691e-a919-4d48-999c-a9daa097158f	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 07:34:52+00
705ccd9f-5612-4261-b9a3-ea4506ac5055	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 07:43:06+00
945a1fca-dce2-475f-b98c-07ad0b6b5fd1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 07:44:03+00
ba7460f0-ea78-4955-b220-a947c9d98793	0180a717-2d4b-4d0d-a418-153b7b24911e	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-27 08:01:20+00
c652693c-43e9-416e-810a-962a8d92ec3a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-27 08:01:29+00
d84f820c-3127-49e4-a6ab-16908618294a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-28 02:54:18+00
2d51956a-797b-4485-8a7b-8e0ff6ebfbe2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-30 06:19:51+00
ef780533-f12d-47b6-a024-d7938d1ebb42	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-10-30 07:03:19+00
58c48d1f-3ca5-4755-ac7a-6aaf6cec0fb1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-30 07:04:13+00
109e65d2-5d00-455a-b4a6-458274749946	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-30 07:04:37+00
e31f29b6-5aac-4269-a6fc-12582cc62640	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-10-30 07:05:08+00
8559ebe6-0602-4b82-9a42-5c87333f838f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-30 07:16:20+00
8be2f61d-c73f-4947-b652-d46715d8e8e4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 07:52:40+00
a5d9b17c-ae97-40ca-b998-96e19f2a2b5f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:33:45+00
62098b5e-736c-40c1-bf76-f1c09bea1a1f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-30 07:22:27+00
c380e770-e28d-47a1-acc2-cf488f3d042c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 03:51:45+00
8e14b973-d7d5-43e8-9502-a6ac42946491	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 04:54:29+00
8cfe3424-b58e-454c-983b-6eb25a44725f	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	fefa2366-a955-46f1-bb2b-5d243dfe86b7	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 08:34:10+00
8c151c72-7f8b-4403-b9a6-db4cd4982b77	d126364e-680c-4fe5-879b-52a849cf8e19	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 08:34:39+00
1a474da9-8305-484d-bc55-0a59f82ed155	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 05:49:28+00
80358b08-bbd8-42fd-a73b-c12fda06f8dc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 06:03:00+00
4b37d6f4-7c2c-4b88-b7a2-a736e73e27c0	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-10-31 11:32:15+00
c58cbb14-5823-4a4b-ba72-9c1e1b8a7bd2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 13:08:40+00
bddfd0bf-fe75-4801-9b85-7d2b0ef9278a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-10-31 13:31:48+00
8090ccdf-a2b1-43ea-a78a-7666defb4ef8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 13:32:03+00
2408d4b5-a254-4175-8d7d-f9f3ef9c7810	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 13:32:13+00
6037b085-744d-4491-b462-523864376827	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-10-31 13:44:49+00
533ee9f1-1b56-410f-8e4f-bc5b686f8c2b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 13:45:05+00
8652f537-96c7-49c1-98ce-30fca7a9f31a	d126364e-680c-4fe5-879b-52a849cf8e19	Logged out	session	\N	User logged out; role: faculty	127.0.0.1	2026-11-03 08:36:16+00
cfd87ebb-7e6d-4916-91d5-157c2b2a1f5c	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-04 02:14:29+00
5022a8aa-4be1-4d7c-b099-1a98847c19d6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 13:46:15+00
3b329e3e-ef87-4e2c-b25b-1ebaf768c4e3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.submit_dean.store	\N	Role: Department Chair; Route: chair.submit_dean.store; HTTP 302	127.0.0.1	2026-10-31 13:52:40+00
0801a3dd-7ab7-409f-bb7a-4191237c3d86	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-10-31 17:46:06+00
216bb058-c576-4c90-8193-1788ee3a1f75	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 17:46:50+00
82bb67f4-64de-4745-98d4-e2b12befb399	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 18:48:46+00
22479ca7-89f1-430c-bba7-cba6c259b01f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 20:17:58+00
cf17f6b0-4a79-43ca-8267-6bea755803f3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-04 02:14:42+00
3b4bdfff-1476-4f60-8025-4bedaaaf3dcf	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-04 05:57:40+00
327b6872-a499-4459-a0a8-1eafa3eb2478	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-04 05:59:56+00
5d7021ee-cfa3-4c1e-9b63-63c64f9fbe3a	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-04 06:00:35+00
faf13506-6217-4625-9016-ff09a5e8e3e1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 20:35:56+00
e4d2717b-5537-4045-a10d-b06f825469ba	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 20:37:26+00
05653ebf-2ed8-448f-83a6-03971c18732e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 20:37:38+00
3ffc8a5d-69f5-467c-bba3-420787eb6e9c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 20:42:29+00
7ab05507-1f1b-41ca-bebf-40d4626734e4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 20:42:46+00
493edbbc-1f06-4d2d-ab4e-0aa0d2611aaf	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 20:46:39+00
779ceac2-cd33-4909-b441-a36f45fa0e67	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 20:47:07+00
445b1c32-f792-49d2-93c6-dd2dd1096151	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 20:47:37+00
d1ec16ab-eef6-43bf-9bcc-7810911988a6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 20:58:50+00
8d2dd43c-1733-4dd6-8829-46328a49fc6d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 20:59:26+00
e78e5005-80d4-4681-bc51-3971e13279d5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-10-31 21:04:25+00
93f05dc7-5ba7-49a4-bfdc-073d8ace1eb1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-10-31 21:06:26+00
7d70f461-f698-4a68-8b80-0dbe596a995f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-01 01:11:03+00
2956ddd2-dbd5-408f-b4d0-2c0bb0fb0c7b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.personal-info.update	\N	Role: Department Chair; Route: chair.profile.personal-info.update; HTTP 200	127.0.0.1	2026-11-01 01:16:47+00
c5a34cce-72b0-4600-9b3e-a11cb7cf7d02	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.profile.personal-info.update	\N	Role: Department Chair; Route: chair.profile.personal-info.update; HTTP 200	127.0.0.1	2026-11-01 01:16:50+00
0119fb8b-7cf3-4f7e-823c-a14a48630ad9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-01 01:17:40+00
4fbee5dc-83ca-47c7-a7f0-7f903d22c734	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-01 01:22:56+00
1260ee12-cdbe-4522-898b-ad33ab01c998	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.pending-accounts.reject	908da125-4569-4540-9f37-6cfad23dba3d	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 404	127.0.0.1	2026-11-01 01:24:39+00
de98c06d-19cf-41a4-8804-1ef69777f132	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.pending-accounts.reject	908da125-4569-4540-9f37-6cfad23dba3d	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 404	127.0.0.1	2026-11-01 01:24:43+00
908ce53f-a8bc-432b-b594-23fbf0923113	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.pending-accounts.reject	908da125-4569-4540-9f37-6cfad23dba3d	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 404	127.0.0.1	2026-11-01 01:26:56+00
85fad473-0b9a-44ad-9b37-753cdde18da1	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.pending-accounts.reject	908da125-4569-4540-9f37-6cfad23dba3d	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 404	127.0.0.1	2026-11-01 01:27:04+00
909136dd-af90-44f7-b1e9-89bdd9f6bb07	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	5a02482f-b018-4aaf-825d-adb78c5737a8	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-01 01:29:00+00
3b6f98e8-a0dc-4663-88b4-cd9872fd6a09	270ad7b7-7c4d-4823-b314-816b91773cb9	Failed Created / Submitted	admin.pending-accounts.reject	908da125-4569-4540-9f37-6cfad23dba3d	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 404	127.0.0.1	2026-11-01 01:32:21+00
4f1903de-95ef-4cbb-92ac-54756f3817c2	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.reject	1d27e246-7ce6-437c-9f56-3d36a849f257	Role: System Admin; Route: admin.pending-accounts.reject; HTTP 302	127.0.0.1	2026-11-01 01:32:43+00
85150ffc-d57f-4ca1-8b21-03715b6163e9	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-01 01:38:44+00
e27b33f5-f472-424b-afb8-57e34e8f252c	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-01 01:40:50+00
f0b6ab5f-2b02-4199-a206-40a250c9cf08	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	225825f2-93a1-4593-9ae5-1a4c85023c99	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-01 01:41:36+00
871e6123-97d9-4b6d-968d-4a5bc94e494b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-01 01:53:18+00
37a2e9ea-1858-43bd-bd5b-23304b5bcf75	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-01 01:58:39+00
64cfdcd1-13aa-4a16-bc6d-57c7663adc92	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	3c10ea20-fcb6-4bc3-80ec-945f5a955302	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-01 01:59:01+00
f1621973-9276-49dd-a764-d2dd927b89ce	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:06:31+00
3362e0a2-dea2-471f-a2ca-ccfe276afcfc	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:03:19+00
cd6601c1-cf1a-4714-a186-4746aa7762b8	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:03:24+00
10db0533-9b97-4fc0-a9b2-6f8a17952747	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:03:26+00
bb0426a2-cda9-466f-9110-8e16421dd5a6	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:03:28+00
a11898e6-174c-4da2-9956-633b114852e2	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:03:31+00
5181cb3c-914e-4c95-b15b-a3f8b84b8fb5	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:03:34+00
11650333-07b8-4b35-9d30-24a5af8af0e0	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:03:36+00
3c9b5f9c-e16c-42c3-a5e9-72a77d13f1a7	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-01 02:04:23+00
87573dc7-2e9b-4aef-ab4f-323abbff47a2	e891982b-d1b2-45c8-a64f-e2c5615e433a	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-03 08:08:11+00
f8e2750d-0990-4d7a-bd54-cf12b9107804	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-03 08:34:05+00
5038b3e3-6fad-448c-a0e6-0c1a9079fab2	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:34:21+00
15276697-26cf-48a1-b0a7-869a176c29cd	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Created / Submitted	dean.profile.avatar.update	\N	Role: Dean; Route: dean.profile.avatar.update; HTTP 200	127.0.0.1	2026-11-03 08:34:49+00
c4993e10-14ce-457e-84fd-f24512c16854	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:36:28+00
b8e5b3f3-c5f2-4e02-8592-9d68993060f0	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-01 02:08:12+00
3f7f2917-329e-4403-8dd0-098d8249959b	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:09:20+00
e21c3eff-429b-4473-bb4c-22ed9edb5c93	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:10:02+00
d5c0ddb7-a192-4347-9e9e-77e02f1a6c48	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:10:23+00
fa504945-0c34-4ec1-abf7-b540dc42d1de	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:10:37+00
5ae17491-18f9-4774-ab41-b801ecf95554	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:12:30+00
b9e3812d-a9c5-4bac-abe9-8951d7bb7795	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:12:51+00
366bf5d1-ff7b-4e6d-95a3-5b5b15a7a0ed	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	d42f3fb1-34f9-4e1c-8ebe-c401d9fe1112	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:16:17+00
66e39993-769e-4953-8a52-eb119c7779cc	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	d42f3fb1-34f9-4e1c-8ebe-c401d9fe1112	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:16:39+00
94179cae-c5e1-4eb0-ad63-fc95f2bc532e	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	d42f3fb1-34f9-4e1c-8ebe-c401d9fe1112	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:17:00+00
aad66855-7fa7-4e16-87d3-7e5eb509e268	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	d42f3fb1-34f9-4e1c-8ebe-c401d9fe1112	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:17:41+00
8df31a76-6fb5-49c4-a5ce-da87e956fb35	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	d42f3fb1-34f9-4e1c-8ebe-c401d9fe1112	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:18:12+00
7117b1f7-bbc7-46b2-bc47-c240961b95ad	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	8e71ea7a-a33f-4eb3-89d0-61f9013a7031	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-01 02:19:29+00
16d6b5b8-56a3-4539-99cf-4aae83b0dfb2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-01 03:35:34+00
8534dc97-61cf-4087-b83d-d28f653950e7	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-01 03:47:39+00
39564087-3080-4026-b688-9f4a9eea2aaa	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	6257f202-a6c3-45aa-b511-4296e1f50ac7	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-03 08:07:14+00
3e210d03-1fd6-4d2d-b5bd-f2ae66faead3	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-03 08:07:39+00
a2c1fcfa-ce5b-435a-8dd1-453e8806affe	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.notifications.read-all	\N	Role: Department Chair; Route: chair.notifications.read-all; HTTP 200	127.0.0.1	2026-11-01 05:12:00+00
a19e5004-9ac7-4744-bee0-7e1907cc7a13	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.notifications.read-all	\N	Role: Department Chair; Route: chair.notifications.read-all; HTTP 200	127.0.0.1	2026-11-01 05:12:03+00
d73c7d8b-8d11-4597-851d-e5ea815d0098	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-03 08:37:31+00
59690dce-9ea8-4a08-8738-0b004c9d7fcb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-01 09:35:19+00
f1a29668-c6f4-484e-9674-f37c282c13ba	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-02 02:59:36+00
adac2943-a7ec-4436-9fbb-b4cf41e63709	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 01:13:43+00
f75f8fdc-8655-4c22-8369-a5a78d082413	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 05:51:52+00
44a8eb32-5d9d-4bc8-aa7e-0eef4c882837	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 05:53:31+00
a73fe8ec-db5f-4712-ad2c-bf95af76bf80	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 05:54:17+00
8dc7a79a-f0e7-49c3-8040-8c5a47487055	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-03 06:33:28+00
68abb53f-f33e-4d33-9929-51be9a403a1d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-03 06:33:45+00
ab92b1f7-b5f1-40a5-a18b-fd9578272367	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-04 02:31:14+00
76a7eaa9-9156-49f0-9cd7-127b6ae19554	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-04 06:00:52+00
99fe511a-e798-4bfc-ac64-b069094924fa	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-04 06:02:41+00
1da8777f-1ece-41b4-8d96-82d3509c017f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-04 10:18:00+00
30310073-573b-4f97-8f63-c7289f2f5906	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 12:53:36+00
f27aa14b-d2c0-4229-8e10-d7d0aa6ff75e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 12:53:58+00
adebfa73-03b4-49fd-9b26-1801dd745b3e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 12:55:11+00
f0e912af-cb83-4ac3-8f7e-5202e783b4ff	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 13:15:38+00
3a81f21d-2d89-4603-b1e5-3b88d5f91e1e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-04 13:17:06+00
4ff7c4b1-9f14-479a-865a-87b3a0fd5077	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	322ca05e-404e-4638-9c3f-ece2f95d8996	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-04 14:28:35+00
41f2a9d4-79e2-49dc-849d-d60051c15c52	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	ba231c3b-b91d-421c-806a-ed6f3da49bcd	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-04 14:29:10+00
1575652f-8ab6-4e0b-ad6b-ecadd44ef7e2	e891982b-d1b2-45c8-a64f-e2c5615e433a	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-04 14:29:21+00
820e98cb-4ead-42f1-b367-bd4fea3d8bcb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	ba231c3b-b91d-421c-806a-ed6f3da49bcd	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-04 14:30:44+00
3f8489da-ee66-4dc4-a162-c54f86153e0d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 14:31:48+00
c3e87611-edf6-480f-a114-875abf4e1803	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-04 14:32:05+00
f732cb08-7f6b-4fd4-9391-53b493c85d5e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-05 02:03:29+00
6df40da0-0086-4eec-bcc9-f54485941eaa	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-05 02:04:03+00
cb640e2e-9472-42de-81d6-b599ec8d23a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	2308c1f5-88ca-4dfa-88e4-75f411ba24e3	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-05 02:33:12+00
88349ff6-ed12-4de0-ac75-5de38173ee8b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	5f8d8d4a-41ed-409d-ab8c-f6f90f647d83	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-05 02:33:31+00
7501f2d4-43dc-4712-ac35-cc7dfbe915f7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-05 02:50:29+00
ee454f2d-5d48-4de5-ba87-b612e4096db3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-05 02:53:17+00
28a54813-de7d-4e03-8e83-b59ba59256cc	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 02:53:51+00
193f6074-b433-46af-ae8b-d83644b3203b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 02:57:23+00
ca0bf892-a503-4c16-b9ef-4019e5adbaf9	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e891982b-d1b2-45c8-a64f-e2c5615e433a	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-05 03:14:01+00
7fa795b0-c5b7-4e39-ab71-c0875e8717be	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	dcd87c9e-0236-4c08-839d-4894dace7cfe	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-05 03:16:13+00
9d8de827-18fa-4ebb-b7a4-4054b3d7581e	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e891982b-d1b2-45c8-a64f-e2c5615e433a	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-05 03:20:43+00
f90939e1-1906-4d1a-a339-b2306b922a42	270ad7b7-7c4d-4823-b314-816b91773cb9	Updated	admin.users.update	e891982b-d1b2-45c8-a64f-e2c5615e433a	Role: System Admin; Route: admin.users.update; HTTP 302	127.0.0.1	2026-11-05 03:21:06+00
85161462-c479-4589-9481-e15953b57162	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 03:24:22+00
a04d83e6-7ce9-4dba-b649-c26257e4b3ee	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 03:56:34+00
6e888ca3-c4af-4ce0-b281-9185982144b2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 04:02:45+00
90d9e05b-d172-4115-95f3-17a20db7f432	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 09:38:10+00
a33e0ffc-2dc2-4d30-8c62-137bc7817ab5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 14:40:43+00
437ccb7b-373a-47a9-bc76-05fe94c37804	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 14:41:41+00
b9e3dcb2-8e52-4533-81c1-c9a192f9fdd5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 500	127.0.0.1	2026-11-05 14:59:07+00
2b008f5f-b70f-4398-84cd-c8ac7d220630	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 500	127.0.0.1	2026-11-05 14:59:53+00
5bc58ce2-8268-4738-99fb-552888b1e219	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 500	127.0.0.1	2026-11-05 15:00:29+00
55c61111-81fb-4f59-bc03-807df3fafa02	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-05 16:39:01+00
8c790f4f-786a-4c16-a5b9-1f7c3188199f	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-05 16:39:49+00
46e7fd93-0ba7-47c2-ba86-0065822fcf94	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-05 16:40:09+00
070d9a8b-06ca-444a-9ef7-6e4ea42e9448	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-05 16:40:43+00
22198362-5943-4cb9-84bf-540c5ec0d521	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-05 16:40:54+00
be15babd-dca9-4ad3-b0f5-ff635d123971	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-11-05 16:43:57+00
8844f81d-23be-4868-be47-937c91c253c4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-11-05 16:44:48+00
91a5ec56-9116-4da8-94bd-75fceb6ba59a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-05 23:18:13+00
0a67ff9a-a614-4eb3-91e2-85fa2e9b9936	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-05 23:18:26+00
76b428ee-81c0-40a7-92af-e1d31f0d3d1e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-05 23:19:57+00
88af3f90-be1b-4d4a-b86f-116ac109c626	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-05 23:20:03+00
d95ad414-f1b6-46a1-933e-a353cfb50bb5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-06 00:05:26+00
1abdcc42-793b-42ca-a6b7-9a245762ea45	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-06 00:05:31+00
33dac26a-4e48-43b8-b693-14713fe225c1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-06 00:06:26+00
f3b0a7e9-129e-4361-9cb7-44249a1f724c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 00:09:23+00
1eb49913-b4d7-43bc-b25c-1f5d05406f5d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-06 02:38:40+00
752dfb73-19d9-4d34-9024-33c75749cb3f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-06 02:39:53+00
52a8c797-11f7-410b-bd00-080f3fa569fa	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	630e7908-2598-48ae-9a2a-a1988112f12d	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-06 02:40:31+00
8b6c3e82-f57d-46a2-983c-43ba8be071df	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbs.destroy	630e7908-2598-48ae-9a2a-a1988112f12d	Role: Department Chair; Route: chair.pbs.destroy; HTTP 200	127.0.0.1	2026-11-06 02:41:24+00
964a0d03-bb8a-4d63-90d0-966337d9305c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 422	127.0.0.1	2026-11-06 02:42:08+00
f3d4637f-33e2-4482-b412-3b9e1483be13	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 02:43:27+00
fb8421aa-86f7-4ef5-91e2-7ea111efe1a8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-06 02:50:35+00
2d121b5c-f461-4662-8d80-ab1c5898262e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:02:32+00
f3b15553-7add-462d-a341-d2ee60f195bf	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:05:19+00
100231fe-800e-476a-b2de-0d8b9e6b7adb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:06:18+00
c5ecf0c2-d245-48f3-81d1-9eca47183f12	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:07:39+00
e2b47ba9-ee23-4562-b6d1-8d7958aa1dda	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:09:42+00
5430aea7-10f3-468f-af07-5825026df266	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:14:02+00
24ff3a5d-8436-4498-9b0c-ee8169b9a50a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:16:54+00
474763f4-d90f-4ed3-8c96-d92beb42c05a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 422	127.0.0.1	2026-11-06 03:18:04+00
6b5af11f-d51e-4b3a-b57d-c2d305cd1504	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 03:18:17+00
926ebe20-426f-46be-9fb8-c8ae62592739	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-06 03:22:06+00
a8ce40a8-bd27-4353-a3fc-f2d992c210e2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 201	127.0.0.1	2026-11-06 04:04:51+00
00b1d050-dbfc-4a3e-a27d-529027ad59b2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-06 04:48:08+00
430230a5-075c-412a-b287-33dc5a33fe77	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-07 04:53:07+00
7521adc0-fe8d-4a31-976b-d4c9b57b719f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-07 08:05:58+00
ca938aea-7fbd-4cd3-9d39-6d16388db9b0	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-08 11:17:29+00
a9272344-5ea9-4d73-9180-6ff83d81725a	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-08 11:22:37+00
27af6d32-2270-4e77-ac72-fe452e5a6620	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-08 11:23:10+00
1f4ef345-645b-40f1-9cef-b65159f61262	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-08 11:31:03+00
5e5a6b93-2159-474e-b827-bb305877fbcb	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-08 11:32:22+00
8bdb575d-d695-4a21-8520-e41b3e9165de	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-08 11:32:58+00
fe1d6c90-7846-4cee-a412-ad2d7e0b6ba5	270ad7b7-7c4d-4823-b314-816b91773cb9	Created / Submitted	admin.pending-accounts.approve	f6f7626a-d7c2-4388-b085-0cf0f429d588	Role: System Admin; Route: admin.pending-accounts.approve; HTTP 302	127.0.0.1	2026-11-08 11:33:24+00
2f692145-e7e3-4f9f-91ca-14533793d7a1	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-08 11:33:31+00
831128ea-91b5-49f8-97f3-09d3d2a19d31	2005b54a-b45b-437a-bb5a-e167c8137a02	Logged in	session	\N	Successful login; role: faculty	127.0.0.1	2026-11-08 11:33:40+00
137fd219-6432-429c-9f13-6b88ea11b4ec	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-08 11:33:48+00
0384272d-f128-476e-8c77-49b97bfd040b	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-08 11:36:53+00
3df3a58b-2134-482d-81da-062511c00500	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-08 13:12:35+00
0baabf15-686e-42be-a37d-fd18d08b46b3	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-08 13:12:50+00
3909f318-e7db-41a1-af86-f3529a31e558	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-08 13:15:03+00
33accd1c-f4e2-47f4-8cbd-dd1f29196141	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-08 13:15:16+00
c6af1be4-5bc7-4032-9f0a-f9ce3c44dce6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-08 14:46:36+00
6fbfcc29-988e-4b6e-bb42-74f2e99eeb70	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-08 14:46:46+00
045290a5-5a76-432e-8b48-36e386967720	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-08 14:57:56+00
ca174345-041f-4e5f-848e-634b17dd7db1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-08 14:58:32+00
e6483b1c-dc8c-480d-ae52-b1602f5a2d22	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 15:09:45+00
1a942afc-3b2f-4b17-8577-c1effc1b6ea9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-08 15:51:57+00
163a3e7e-3d5e-45d6-86c9-a4aaec2daefb	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	328589e9-163a-442e-9d39-a1ae06f2abd7	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-08 16:01:57+00
cbac858a-db2c-49c2-97d8-c23385890a79	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	328589e9-163a-442e-9d39-a1ae06f2abd7	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-08 16:02:20+00
5487e1fb-0128-4a70-be5d-f9287eebfa99	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 16:02:58+00
2748ece0-dd2f-451b-ab41-cabc805ba876	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 16:10:30+00
394faeb3-8ea4-4659-92fe-c3e95daaf133	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 16:10:42+00
95936678-0b0e-40e0-9f6d-541fc4c6b1e1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 16:11:15+00
b5482e28-a855-449f-a5fe-714788ec28b4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 16:14:37+00
24b93d55-6a7b-466e-856a-9bfff966c7f2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-08 16:19:11+00
8190b490-dbbc-47d8-8c6f-6a33c8ec2ccf	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-08 16:19:38+00
0f5e58b0-3f92-4bf9-a0f8-9020f49cfeba	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-08 16:21:21+00
d2d6ecac-236f-47b9-82cf-df2e6379b546	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-08 16:21:35+00
9d0d6fa7-8ed0-4936-ae77-4490ebd6ce43	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-08 16:30:58+00
df7c377a-dc60-4e49-9ac3-5ee9f65d0cc5	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-08 16:31:07+00
832dd68f-f1ae-49ac-8588-cc0822c5a807	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-08 16:31:36+00
c0b170b7-1a62-47d2-b032-d8214c6d333f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-08 16:32:35+00
7dc0395f-e7b0-4b89-be3d-492f9df72d28	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 16:38:19+00
e1a1f338-12ff-4761-abf7-7356ae67fddd	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged out	session	\N	User logged out; role: department_chair	127.0.0.1	2026-11-08 16:47:31+00
e5921a2a-aad9-4416-8f45-c55e603dc393	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-08 16:47:47+00
16faeeb0-1e93-4e30-8277-73abe7754ae3	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-08 16:48:08+00
9a40d12b-419f-4ef7-902a-a32285e39596	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-08 16:48:20+00
b9455fa5-ebbf-4620-a186-35e7cf32cc34	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-08 16:50:30+00
5e40b7c3-0cf8-4596-97ce-58a61403f73b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-09 08:27:27+00
3e2cf2c6-aba8-450b-a9b9-6e9fb2fc143b	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-09 08:32:26+00
7edfdfe0-74ff-4c7d-943e-1744fe787c25	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged in	session	\N	Successful login; role: dean	127.0.0.1	2026-11-09 08:32:38+00
9f8df0c2-ecbf-437d-a8be-ef0c4f6c649d	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Logged out	session	\N	User logged out; role: dean	127.0.0.1	2026-11-09 08:34:44+00
f20fce80-a9ba-46e7-bb93-acfc7b6ab8da	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-09 08:38:10+00
2b44f26f-1b90-4e9b-a82a-5fbc7198dc88	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-09 08:40:24+00
e1417f78-22c9-4ca9-9473-31d16f1b3e30	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged in	session	\N	Successful login; role: system_admin	127.0.0.1	2026-11-09 08:59:30+00
f25ca839-bf54-48cd-8dac-9484c6327e38	270ad7b7-7c4d-4823-b314-816b91773cb9	Logged out	session	\N	User logged out; role: system_admin	127.0.0.1	2026-11-09 09:00:10+00
c2ddafb5-fe33-4c15-aa21-837502696815	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-10 04:31:28+00
de979580-afb0-401a-9bbe-b587b42b0917	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-10 04:33:20+00
3c237f52-adac-4f87-8b00-dc6bf2d5bc2a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-10 04:33:53+00
cdc12249-5808-43a0-b799-4f294be4966a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Logged in	session	\N	Successful login; role: department_chair	127.0.0.1	2026-11-10 10:55:11+00
32b20779-8596-4cb7-b10b-0aaa0f47ffd2	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	21b2f9df-fd35-4f6e-a08e-b10a6ef3b928	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-10 11:18:17+00
6ad2d79e-81bb-4648-a0a0-142276b91c30	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 11:30:25+00
4eb32af0-c9e5-4fae-a7b1-97ed0932591e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 11:31:45+00
08252b61-c031-48a3-b0e1-56c40b0f851d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 11:32:36+00
e788e461-fc94-4ebc-9af4-6d33f0a4c9db	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 11:32:50+00
0ffa088a-353f-423f-ad64-72d462bf76c0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 11:33:07+00
20f32c83-3caa-478d-b0fb-abbe51192143	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 11:34:20+00
dd05d89f-ba5a-4c92-aa90-9b4dd6607e23	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 11:35:49+00
3bf030e9-5244-4cac-9177-cfeb1aa56ddd	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 11:37:32+00
37abc56f-a0fc-4dbb-8666-b7e59d8f16c4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	fc598609-8ce3-40f0-af52-48d4e7e5803e	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-10 11:40:48+00
dc2c1f57-47d4-4198-b6e2-b6a1780e7f27	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1a08a-6633-71da-a16e-269a62efff07	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 11:49:14+00
123386d7-a70b-49e3-ace6-206b8b61d08d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1a08a-6633-71da-a16e-269a62efff07	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 11:49:20+00
2643f624-0484-4c70-8f67-52fb0348222d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1a08a-6633-71da-a16e-269a62efff07	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 11:50:19+00
1158b5a5-ef1a-4d87-928e-5a961957a61a	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1a08a-6633-71da-a16e-269a62efff07	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 11:52:19+00
9942951c-06b4-4cc0-9c79-aca4d2cd8270	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.faculty_load.special-position	01a1a08a-6633-71da-a16e-269a62efff07	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 302	127.0.0.1	2026-11-10 12:02:04+00
3adc8405-810e-49e6-946d-89c4cc5a5656	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:03:43+00
def0b816-ebdc-4c37-8bf0-2298a8bb6f20	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 12:06:27+00
2ae17e8f-004c-48ea-ba67-61e02f0d5dd6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 12:08:44+00
87545151-a354-46d5-abcc-8d7ba5f892e4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 12:09:35+00
e53e67cc-0d5a-4a10-b6c1-7f2189b14273	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:10:33+00
bed11221-25b3-4954-851f-c63c40b46eb8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:12:50+00
57e7f9c9-3a7c-4473-ad29-7abd1d02e622	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:15:51+00
453f6e61-b564-4164-a6b0-6f1c2116682d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:17:13+00
f93c6145-c4b4-4250-9cea-eb61830e98e5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:17:27+00
7e3811b6-0f77-4da2-9417-b89a6a5511d1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:18:18+00
d26b6eb0-9fe8-4cf1-b4ca-eb9cbd15f3bd	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:20:34+00
2c079acd-a709-4a02-bed6-6fd87164af0c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-11-10 12:27:26+00
6656a95b-0141-40f0-b8dc-720504f0bdf0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-11-10 12:27:40+00
e3561ef5-14b4-44c3-8839-04a5e5978b9e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 500	127.0.0.1	2026-11-10 12:28:38+00
9ebf324b-1f8b-40f7-b0fb-7fd08c7727c8	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 12:31:46+00
1fe7c32e-c087-4872-9add-88120249db1c	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 12:45:15+00
af260860-3cec-4715-8a64-cba38d8186f4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:48:24+00
946df73c-a05f-4322-84ca-d3f3643a5d91	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:51:40+00
2821f153-7954-4985-9558-e954316b5c07	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:51:58+00
5eae5942-cf58-4ec2-a833-1f607285ec88	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:52:28+00
98f59663-b1cd-4389-bb8b-a818f95843b4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 12:57:35+00
bbb8b6e2-ad70-4894-ac5c-4072c6f19972	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 12:58:40+00
39593fed-155f-47c3-be52-65e2b9817947	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 13:02:53+00
f8a52ae4-136e-4552-a7a3-4d24aef751d3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 13:14:58+00
adffaede-8286-4f07-8874-e32aa243b49b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 13:16:33+00
0573ce8f-8f17-4eca-a251-65bb9a2ebc9e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbs.store	\N	Role: Department Chair; Route: chair.pbs.store; HTTP 422	127.0.0.1	2026-11-10 13:22:35+00
32569d3d-f681-4f55-bc05-6a9231e66da3	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 13:34:52+00
6bb3b64e-ed3f-4899-aa64-24945db5a1a4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 13:35:28+00
95442016-2531-49f8-afc1-eeb136990335	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	63670ec5-d87d-4b8e-8062-cf64d8907d68	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-10 14:10:58+00
04a2d7ba-e961-4fc2-a3d4-66f7d1df73a6	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:11:43+00
1fdd5d57-5358-447b-81aa-6b976aff4daa	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:15:04+00
ffe16997-845b-4224-9895-8d5dd1a08b05	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:24:48+00
ae243326-7c1b-45f1-abe7-e38d17fb5830	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:28:39+00
70f3448c-b8b1-47e9-8474-2d62143077bf	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:47:00+00
b8ba1d29-b872-4598-915d-ace2db74f4e4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:47:51+00
755ce414-a299-408d-9c94-035561ff94a1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:52:36+00
1800a133-2532-4508-bba6-6161504fd282	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:56:24+00
387ea02d-aa3c-4cec-891b-3a51646b0a17	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 422	127.0.0.1	2026-11-10 14:59:03+00
acfc1b85-8360-48cb-b402-b2b9360c47df	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	f49cb8f7-4d32-4db9-a589-93a754eb9512	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-10 15:07:31+00
f1754c1d-04be-44b7-ac8c-441d0cea4d91	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.faculty_load.special-positions.store	\N	Role: Department Chair; Route: chair.faculty_load.special-positions.store; HTTP 302	127.0.0.1	2026-11-10 17:00:26+00
a4e04db1-77f4-4957-a678-0ac62d86bf4e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.faculty_load.special-positions.store	\N	Role: Department Chair; Route: chair.faculty_load.special-positions.store; HTTP 302	127.0.0.1	2026-11-10 17:00:28+00
7ec99e9f-5217-4924-ac20-29ce766dfdd4	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.faculty_load.special-positions.store	\N	Role: Department Chair; Route: chair.faculty_load.special-positions.store; HTTP 302	127.0.0.1	2026-11-10 17:00:30+00
c4a644ec-7959-4839-889b-965c98b543b9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.faculty_load.special-positions.store	\N	Role: Department Chair; Route: chair.faculty_load.special-positions.store; HTTP 302	127.0.0.1	2026-11-10 17:00:31+00
5653c2a2-bee6-4397-b3bd-badf29283627	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.faculty_load.special-positions.store	\N	Role: Department Chair; Route: chair.faculty_load.special-positions.store; HTTP 302	127.0.0.1	2026-11-10 17:00:33+00
1056e991-5fa1-4fcc-a409-e51d50eb1725	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.faculty_load.special-positions.store	\N	Role: Department Chair; Route: chair.faculty_load.special-positions.store; HTTP 302	127.0.0.1	2026-11-10 17:00:35+00
618cd27e-85dd-4a3e-a3f9-4f8eeba348a9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1baec-8076-7080-a841-079a4c219fe9	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 17:01:24+00
e9d87969-428a-40c6-956c-b53474af8292	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1baec-8076-7080-a841-079a4c219fe9	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 17:01:29+00
59d5120f-4f70-4f22-b366-7694304ad400	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 17:09:44+00
e457921d-83a2-42d6-bec8-abbfb5647d1e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 17:09:50+00
df9f360a-0bbe-4c6a-af3d-8df8933c1117	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 302	127.0.0.1	2026-11-10 17:12:43+00
80e96d52-39e5-4098-8663-3aab11f03ca9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 302	127.0.0.1	2026-11-10 17:13:33+00
e5fe7fb8-de82-4b01-bab1-583ddb7ced8d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 500	127.0.0.1	2026-11-10 17:14:03+00
45b62a26-5f9c-4b08-ae3a-c7ad16f26e97	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 302	127.0.0.1	2026-11-10 17:21:39+00
d0d74498-f15f-482b-ba5a-7376e0e31487	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 302	127.0.0.1	2026-11-10 17:21:54+00
41e12345-4cff-4f74-9436-a7ac7d929e8f	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 302	127.0.0.1	2026-11-10 17:23:01+00
d0c8c74f-8eee-4ac3-be1c-c3cc64ab5eb9	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.faculty_load.special-position	01a1a081-6875-705e-9bbf-aad9f2b675f8	Role: Department Chair; Route: chair.faculty_load.special-position; HTTP 302	127.0.0.1	2026-11-10 17:23:15+00
d52646c4-866f-43b4-8a05-1840e6010165	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	ef854190-cad4-4b7b-90df-cf8077f75b35	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-10 17:35:53+00
90bf7db5-6570-46ec-b7a2-020ea96a3c81	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 17:36:34+00
41831537-67da-4379-8f3b-951a9fd055e7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	d0a6a3a5-a075-47b8-9328-215c509b7bea	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-10 17:37:12+00
16a8abc1-99c6-44da-9c5e-82b9d06d5f0e	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Deleted	chair.pbt.destroy	476cbf5b-6f2e-446b-b7e5-a6ec46859d18	Role: Department Chair; Route: chair.pbt.destroy; HTTP 200	127.0.0.1	2026-11-10 17:37:40+00
d9fa829c-7da4-4b2b-a27f-9c22f981bbf7	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 17:38:35+00
97156784-c84b-49df-903e-408f88b1c371	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 17:40:41+00
60c6b8a4-e24e-4a16-b0c9-fbf3ca69210b	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Updated	chair.pbt.update	c7487a01-8869-40d1-baff-ce99ebca8994	Role: Department Chair; Route: chair.pbt.update; HTTP 200	127.0.0.1	2026-11-10 17:41:17+00
c2b39a94-b259-4f82-b2fb-f483002cc004	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 17:42:58+00
9f06e01d-2e72-44ec-ba12-9fe4b10389ca	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 17:43:21+00
24a2d651-a6f0-44f9-bd85-acf35f5de6bf	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 17:45:19+00
89a1afe9-4248-4f9d-9ce1-a2300cff2609	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 17:46:42+00
5b432683-2393-460d-91d8-093b63418389	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Failed Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 422	127.0.0.1	2026-11-10 17:46:56+00
7b6ab085-06e8-4fb4-ac62-4a40e42db562	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Created / Submitted	chair.pbt.store	\N	Role: Department Chair; Route: chair.pbt.store; HTTP 200	127.0.0.1	2026-11-10 17:47:15+00
\.


--
-- Data for Name: biometric_credential; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.biometric_credential (bio_id, bio_usr_id, bio_fingerprint_hash, bio_registered_at, bio_is_active) FROM stdin;
01b7b3ac-dc59-479d-beee-293481ddd695	270ad7b7-7c4d-4823-b314-816b91773cb9	fp_hash_admin	2026-07-15 15:05:38.115564+00	t
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: college; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.college (college_id, college_name, college_code, college_created_at) FROM stdin;
dbabab7c-f8f5-48fb-86ec-a7cb11af1a8c	College of Engineering	COE	2026-07-15 15:02:46.174993+00
01c2dac6-6375-4e5e-a2a7-1838c3a6fd1b	College of Education	COED	2026-07-15 15:02:46.174993+00
bb09daa1-3065-4718-b08f-e4569311ace3	College of Arts and Sciences	CAS	2026-07-15 15:02:46.174993+00
d2762742-5d6f-4013-9124-1cd3105c2708	College of Technology	COT	2026-07-15 15:02:46.174993+00
c5d5cb3b-eda7-4f13-99b1-f7602a749b30	College of Computer Information and Communication Technology	CCICT	2026-07-15 15:02:46.174993+00
\.


--
-- Data for Name: course; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.course (course_id, course_college_id, course_dept_id, course_code, course_name, course_lecture_hours, course_lab_hours, course_is_active, course_created_at, course_updated_at, course_year_level, course_semester, course_units, course_category) FROM stdin;
221d8268-bbc3-427d-8e18-3d6457d3c2fb	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 111	Introduction to Computing	2.0	3.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	3.00	CC
d601231f-8bf9-44c1-979d-47b0862983b2	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-MMW	Mathematics in the Modern World	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	3.00	GEC
dd991fe9-a724-441d-a9c2-a2b383f8d75c	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEE-GPSP	Gender and Society with Peace Studies	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	3.00	GEE
b4a35934-3cb8-43ff-8510-7594adb302fd	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-PC	Purposive Communication	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	3.00	GEC
ecf095e8-8be4-4fa7-9486-14f3a5ef310c	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 112	Computer Programming 1 (Lec)	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	2.00	CC
97146de0-2d72-4a5f-9122-e23a6dd73a25	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-STS	Science, Technology and Society	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	3.00	GEC
3caf369d-7064-4af3-991a-c4e9adb43741	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-US	Understanding the Self	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	3.00	GEC
23b04717-a9f5-422d-a358-9682b8290757	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 123 L	Computer Programming 2 (Lab)	0.0	9.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	3.00	CC
129c7c27-0333-482e-a1b2-adaebad32452	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 214	Data Structures and Algorithms (Lec)	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	2.00	CC
29315385-4ca1-4ec1-8650-e3638858a6b9	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 214 L	Data Structures and Algorithms (Lab)	0.0	9.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	3.00	CC
84f9ed60-bdf0-49a6-8778-57ff0178396b	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	NSTP 2	National Service Training Program 2 (CWTS 2/LTS 2/ROTC 2)	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	3.00	NSTP
88bddbe2-6c57-42d4-8a25-17208a560fbe	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-LWR	Life and Works of Rizal	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	3.00	GEC
814c196c-d04f-4c82-a43c-6d36b46dbc7c	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PATHFit4	Physical Activities Towards Health and Fitness 4: Dance/Sports/Martial Arts/Group Exercise/Outdoor and Adventure Activities	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	2.00	PE
c422befe-115d-40cb-8528-5dcac6ddfd42	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-TCW	The Contemporary World	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	3.00	GEC
75eb53eb-4458-41c5-b317-2a9623fb7d12	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 213	Professional Issues in Information Systems	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	3.00	PC
0e9dc814-f4d8-4018-b905-0793be99baf7	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-PEE	People and the Earth's Ecosystems	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	3.00	GEC
46e3227c-c233-48a0-816f-5beeff1e720c	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 225 L	Information Management (Lab)	0.0	9.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	3.00	CC
de4ef0a6-5a60-4a20-b4f1-9de55824cf47	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 226	Business Process Management	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	3.00	PC
9751862a-6d6a-417e-9834-26e920dfebba	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEE-TEM	The Entrepreneurial Mind	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	3.00	GEE
15e22461-23f8-4a88-9581-ac0d8fb2ede0	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	AP 5	Foundations of Software Engineering	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	AP
efa79181-f225-4ad9-b5ae-1b28bb2dfdb3	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 4114	Capstone Project 2	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	4	1	3.00	PC
9daf1d2c-9d9f-4c70-8e37-701efd8ae642	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 4113	IS Strategy, Management and Acquisition	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	4	1	3.00	PC
589bc794-8034-49c3-828e-ae52c10185b7	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	P Elec 1	Data Mining	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	P Elec
8bab364d-dbe5-42d1-bd60-c1b6039bc861	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-RPH	Readings in Philippine History	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	3.00	GEC
9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 112 L	Computer Programming 1 (Lab)	0.0	9.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	3.00	CC
a3d2f648-c2d3-4277-951c-8b33f6832b01	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 317	Systems Analysis and Design (Lec)	2.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 03:20:10.33823+00	3	1	2.00	PC
060fb1de-bd77-4c8b-8b3c-0ed17999ec33	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 122	Organization and Management Concepts	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	3.00	PC
0b9d7201-1ec8-4600-a970-7d99b32720f9	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 225	IT Infrastructure and Network Technologies	2.0	3.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	3.00	PC
3796036e-8010-48ac-b6e3-af139bdc7557	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-AA	Art Appreciation	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	GEC
404ecfbf-4665-4c7e-b1ef-7665aa6b4108	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	AP 1	Computer Architecture and Organization	2.0	3.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	3.00	AP
48f229a6-59fd-45e7-b534-b9db10ebeb24	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 123	Computer Programming 2 (Lec)	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	2.00	CC
6792b81a-307d-48fc-838c-0dfadea6bf32	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEE-ES	Environmental Science	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	3.00	GEE
6c0961e0-4b83-41c1-a21f-a80c677d4a8a	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 214	Financial Management	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	3.00	PC
7450f516-d06c-48a9-b19b-17e7f575e2cf	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 121	Fundamentals of Information Systems	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	3.00	PC
b020043f-6784-4cf2-9478-c5ad23714052	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 225	Information Management (Lec)	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	2.00	CC
b8533f26-4339-44ba-b2ac-becc3ee54bdb	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	NSTP 1	National Service Training Program 1 (CWTS 1/LTS 1/ROTC 1)	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	3.00	NSTP
d263bef3-06d8-425d-a5e9-5d369bc41127	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 3212	Capstone Project 1	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	PC
d14a8916-ada0-4c92-9ca1-d50b34690032	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEE-FE	Functional English	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	4	1	3.00	GEE
e032b16d-40fa-42a5-8a4d-4c5c02abfa4e	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	AP 2	Web I: Client Development	2.0	3.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	2	3.00	AP
e7e06ddc-5e1d-427c-9127-4caf6e3f8be4	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	P Elec 2	Customer Relationship Management	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	P Elec
f61b7055-01a4-4933-8c68-c8c9436c4a75	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	GEC-E	Ethics	3.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	3.00	GEC
43d5c948-d863-4c0c-a021-56233464fca1	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PATHFit3	Physical Activities Towards Health and Fitness 3: Dance/Sports/Martial Arts/Group Exercise/Outdoor and Adventure Activities	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	2	1	2.00	PE
8cd451f2-cca7-4a4e-b7d1-ef1e541b60ef	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PATHFit1	Physical Activities Towards Health and Fitness 1: Movement Competency Training	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	1	2.00	PE
d900be4c-6d99-4d5c-9131-fce21e372f88	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PATHFit2	Physical Activities Towards Health and Fitness 2: Exercise-based Fitness Activities	2.0	0.0	t	2026-10-07 02:40:56.295271+00	2026-10-07 02:40:56.295271+00	1	2	2.00	PE
889f0785-1a4f-4053-93d1-04a2a651374d	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 319	Evaluation of Business Performance	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 03:20:10.33823+00	3	1	3.00	PC
8f28b4b7-8177-45d0-bb26-48b7f2627643	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 3110	Quantitative Methods	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 03:20:10.33823+00	3	1	3.00	PC
b39c8352-4307-4883-822f-66943f54d698	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	PC 224	Networking 1	2.0	3.0	t	2026-10-28 05:53:06.268189+00	2026-10-28 05:53:06.268189+00	2	2	3.00	PC
f9c0dc38-b211-4b81-a294-7c28110d973d	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	AP 6	System Administration and Maintenance	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	AP
d987712f-57cc-495f-8a5f-e9fc386844a1	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	CC 326	Applications Development and Emerging Technologies	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	CC
b962e1fc-98cc-4657-b897-70538c312cf4	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	P Elec 4	IT Audit and Controls	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	4	1	3.00	P Elec
2c5be148-1d93-4b4d-a08d-3c49df7613e2	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	P Elec 3	Enterprise Systems	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	4	1	3.00	P Elec
56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 317 L	Systems Analysis and Design (Lab)	0.0	9.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 03:20:10.33823+00	3	1	3.00	PC
02b2203a-e29e-43fb-b3cb-81111a5a4aec	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 4215	On-the-Job Training (OJT)	0.0	729.0	t	2026-10-07 03:22:11.792601+00	2026-10-07 03:22:11.792601+00	4	2	9.00	PC
4fa74092-1469-450e-be91-a9c015cf2bcf	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	AP 4	Database Administration DBMS	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 03:20:10.33823+00	3	1	3.00	AP
15f66c86-9bf9-42ef-bca3-4b10320842dd	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 318	Enterprise Architecture	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 03:20:10.33823+00	3	1	3.00	PC
054be96b-7b81-4d29-af1c-64f8a4a1b461	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	PC 3211	IS Project Management I	3.0	0.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 02:57:28.735813+00	3	2	3.00	PC
129760c4-d92f-4695-a7e3-71e8dcac525f	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	AP 3	Web II Development	2.0	3.0	t	2026-10-07 02:57:28.735813+00	2026-10-07 03:20:10.33823+00	3	1	3.00	AP
153ff80c-ba8f-4758-b4ac-2b2fb2bc4416	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	PC 212	Quantitative Methods (Modeling & Simulation)	3.0	0.0	t	2026-10-28 05:41:39.150802+00	2026-10-28 05:41:39.150802+00	2	1	3.00	PC
2ea11643-7229-4a16-a29b-aec277663981	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	PC 121	Discrete Mathematics	3.0	0.0	t	2026-10-28 05:38:08.328973+00	2026-10-28 05:38:08.328973+00	1	2	3.00	PC
47e5a6b0-ceda-4438-a078-29469c317ce9	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	P Elec 1	Object-Oriented Programming	2.0	3.0	t	2026-10-28 05:43:16.910096+00	2026-10-28 05:43:16.910096+00	2	1	3.00	P Elec
54c20c9e-003d-4614-affc-6f52aff300bd	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	P Elect 3 	Platform Technologies	2.0	3.0	t	2026-10-28 05:54:29.571862+00	2026-10-28 05:54:29.571862+00	2	2	3.00	P Elec
5b078561-bcf4-4d3d-b35c-b2f432ce17fb	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	AP 3 	ASP.NET	2.0	3.0	t	2026-10-28 05:55:46.163682+00	2026-10-28 05:55:46.163682+00	2	2	3.00	AP
6ba85deb-a5a7-4527-87fc-624db5b11bf2	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	PC 223	Integrative Programming and Technologies 1	2.0	3.0	t	2026-10-28 05:52:11.919672+00	2026-10-28 05:52:11.919672+00	2	2	3.00	PC
748bc0ff-9511-47d1-842d-845b679e8901	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	8de117bd-ed91-45dc-9ad4-f75eede0cb38	P Elec 2	Web Systems and Technologies	2.0	3.0	t	2026-10-28 05:44:24.400207+00	2026-10-28 05:44:24.400207+00	2	1	3.00	P Elec
c55f8061-08b2-4728-9656-7216110e27c8	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	REC	Research, Extension and Consultation Time	0.0	0.0	t	2026-10-27 04:40:23.239696+00	2026-10-27 04:40:23.239696+00	\N	1	\N	
\.


--
-- Data for Name: dean; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.dean (dean_id, dean_usr_id, dean_first_name, dean_middle_name, dean_last_name, dean_suffix, dean_phone_number, dean_gmail, dean_office_address, dean_profile_image, dean_assigned_at, dean_college_id, dean_dept_id, dean_employee_id, dean_gender, dean_civil_status, dean_dob, dean_nationality, dean_profile_image_public_id, dean_bio, dean_address) FROM stdin;
e4b6c925-9647-4d2b-bfd7-e8c5d2c3aa47	270ad7b7-7c4d-4823-b314-816b91773cb9	Pedro	B.	Garcia	\N	09181234567	pedro@ctu.edu.ph	Cebu City	\N	2026-07-15 15:02:55.862627+00	dbabab7c-f8f5-48fb-86ec-a7cb11af1a8c	\N	\N	\N	\N	\N	\N	\N	\N	\N
ce02d743-22d2-49fc-ba62-803f9d566dc7	bc02c426-ac11-4fb4-93d5-eb6804b7974e	Jose  Maria	S	Garcia	II	+639974453635	\N	\N	ce02d743-22d2-49fc-ba62-803f9d566dc7_1793690858.jpg	2026-11-03 07:26:19.924313+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	5694bc6e-69e8-4dfd-b78b-35af1289ad11	03112026	Male	Married	1966-07-17	Filipino	\N	half pogi and half gwapo	Cebu City
\.


--
-- Data for Name: department; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.department (dept_id, dept_college_id, dept_name, dept_code, dept_created_at) FROM stdin;
8de117bd-ed91-45dc-9ad4-f75eede0cb38	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Bachelor of Science in Information Technology	BSIT	2026-07-15 15:02:46.174993+00
5694bc6e-69e8-4dfd-b78b-35af1289ad11	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Bachelor of Science in Information Systems	BSIS	2026-07-15 15:02:46.174993+00
9a9867c0-72f6-4c74-a8e6-5bff9ddce7b7	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Bachelor of Industrial Technology major in Computer Technology	BIT-CT	2026-07-15 15:02:46.174993+00
2b49e2f9-c446-4638-ac77-c420404f27a5	bb09daa1-3065-4718-b08f-e4569311ace3	Department of Psychology and Social Sciences	DPSS	2026-11-03 08:03:20.823985+00
2801daef-1f0e-4dda-83ab-6822f33d0ebd	bb09daa1-3065-4718-b08f-e4569311ace3	Department of Human Kinetics	DHK	2026-11-03 08:05:23.665622+00
9f11764f-3280-4285-ae76-4fae84a24e85	bb09daa1-3065-4718-b08f-e4569311ace3	Department of Communication	DCOMM	2026-11-03 08:05:53.065404+00
ebbcc28a-1629-4202-982e-118a43f5925e	bb09daa1-3065-4718-b08f-e4569311ace3	Department of Pure Sciences	DPS	2026-11-03 08:06:24.942472+00
0483afc1-fa10-4aaa-bc97-539cc539f6ef	bb09daa1-3065-4718-b08f-e4569311ace3	Department of English and Literary Studies	DELS	2026-11-03 08:07:09.02179+00
7f2aa08f-6780-45ee-b16d-5955f8347733	bb09daa1-3065-4718-b08f-e4569311ace3	Departamento ng Filipino at Ibang Mga Wika	DFIW	2026-11-03 08:07:51.688691+00
b4204316-9b9d-477a-8df5-295f903d2d94	bb09daa1-3065-4718-b08f-e4569311ace3	Department of Mathematics and Statistics	DMS	2026-11-03 08:08:28.84012+00
\.


--
-- Data for Name: department_chair; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.department_chair (dc_id, dc_usr_id, dc_college_id, dc_first_name, dc_middle_name, dc_last_name, dc_suffix, dc_phone_number, dc_gmail, dc_address, dc_profile_image, dc_assigned_at, dc_dept_id, dc_employee_id, dc_gender, dc_civil_status, dc_dob, dc_nationality, dc_bio) FROM stdin;
b8faf6e3-c9a7-4718-b974-b2c6c020eb75	335c70b6-c19a-4214-8b7b-fb771a2c9e02	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Noreen	B.	Fuentes	\N	09172560010	noreen.fuentes@ctu.edu.ph	Cebu City	b8faf6e3-c9a7-4718-b974-b2c6c020eb75_1791436898.png	2026-07-18 06:03:37.813106+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	927202602	Female	Married	1960-06-27	Filipino	Associate Professor II/ BSIS Program Chair\nCollege of Computer, Information and Communications Technology / Information Systems Department
d7b63c0d-375a-45ab-be0f-a03cd0f14504	0180a717-2d4b-4d0d-a418-153b7b24911e	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Pet Andrew	P.	Nacua	Ph.D	09170000011	pet.nacua@ctu.edu.ph	Cebu City	d7b63c0d-375a-45ab-be0f-a03cd0f14504_1793691410.jpg	2026-07-18 06:03:37.813106+00	8de117bd-ed91-45dc-9ad4-f75eede0cb38	\N	\N	\N	\N	\N	\N
01a1a51d-a083-7264-b95d-1da1fe06fa1c	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	bb09daa1-3065-4718-b08f-e4569311ace3	Raamah	C.	Rosales	\N	+639695462213	\N	Cebu City, Cebu	\N	2026-11-04 05:52:23.52338+00	ebbcc28a-1629-4202-982e-118a43f5925e	1103202601	Male	Married	1980-11-03	Filipino	\N
\.


--
-- Data for Name: fac_specialization; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fac_specialization (fspec_id, fspec_fac_id, fspec_specialization) FROM stdin;
\.


--
-- Data for Name: faculty; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.faculty (fac_id, fac_usr_id, fac_college_id, fac_first_name, fac_middle_name, fac_last_name, fac_suffix, fac_phone_number, fac_gmail, fac_address, fac_employment_type, fac_rank, fac_profile_image, fac_created_at, fac_updated_at, fac_dept_id, fac_employee_id, fac_gender, fac_civil_status, fac_dob, fac_nationality, fac_bio, fac_special_position, fac_special_position_max_hours, fac_bachelors_degree, fac_masters_degree, fac_doctorate_degree) FROM stdin;
01a1a081-6875-705e-9bbf-aad9f2b675f8	80e78fb0-6908-422c-ae9e-cc31a7a29f04	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Albert	D.	Maghanoy	\N	+639990000001	\N	Cebu City	full_time	Assistant Professor I	01a1a081-6875-705e-9bbf-aad9f2b675f8_c1005810-3d30-449f-9b46-caf23beff3af.jpg	2026-11-03 08:23:17+00	2026-11-10 17:23:14+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	03132026	Male	Married	1982-08-21	Filipino	\N	\N	\N	Bachelor of Science in Information Technology	Master of Information Technology	na
01a1a087-4c19-70da-8f2e-872c5804e5a1	0a6013f9-0b25-4383-a1f9-f81cac3ea32d	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Janet	S.	Ugang	\N	+639990000002	\N	Cebu City	full_time	Instructor III	\N	2026-11-03 08:29:43+00	2026-11-03 08:29:43+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	03142026	Female	Married	1987-09-09	Filipino	\N	\N	\N	\N	\N	\N
01a11004-1aa5-7125-9178-590a1a432970	d2468e93-6a49-4532-8eae-b5ab3f0dd090	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Gerry Ritz	R.	Densing	\N	+639123948519	\N	Skina mambaling	full_time	College/University Professor	01a11004-1aa5-7125-9178-590a1a432970_e0c15036-4cd4-47ea-9225-3a8a9248ebec.jpg	2026-10-06 07:01:06+00	2026-11-03 08:40:19+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	10062026001	Male	Single	\N	Filipino	\N	\N	\N	Bachelor of Science in Information Systems	Master of Information Technology	Doctor of Information Technology
ac6adea7-6397-426f-9fa1-b9e51749b665	0180a717-2d4b-4d0d-a418-153b7b24911e	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Pet Andrew	P.	Nacua	Ph.D	09170000011	\N	Cebu City	full_time	\N	\N	2026-10-01 09:21:41.133736+00	2026-10-01 09:21:41.133736+00	8de117bd-ed91-45dc-9ad4-f75eede0cb38	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
01a1a087-fa7a-710b-9b72-5fb699d3fad7	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	bb09daa1-3065-4718-b08f-e4569311ace3	Raamah	C.	Rosales	\N	+639695462213	\N	Cebu City, Cebu	full_time	\N	\N	2026-11-03 08:30:28+00	2026-11-04 06:00:52+00	ebbcc28a-1629-4202-982e-118a43f5925e	1103202601	Male	Married	1980-11-03	Filipino	\N	\N	\N	\N	\N	\N
01a1a048-1a3d-708c-bc17-93d208cbc837	bc02c426-ac11-4fb4-93d5-eb6804b7974e	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Jose  Maria	S	Garcia	II	+639974453635	\N	Cebu City	full_time	\N	01a1a048-1a3d-708c-bc17-93d208cbc837_c92a7899-e39c-4b75-b3a2-08dfce958d7a.jpg	2026-11-03 07:20:42+00	2026-11-03 07:26:21+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	03112026	Male	Married	1966-07-17	Filipino	half pogi and half gwapo	College Dean	9.00	Bachelor of Science in Information Technology	Master of Information Technology	Doctor of Information Technology
01a1a070-d483-73bd-a2a6-20329281ddab	e891982b-d1b2-45c8-a64f-e2c5615e433a	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	John Rey	E.	Furtos	\N	+639990000000	\N	Cebu City	full_time	Instructor I	01a1a070-d483-73bd-a2a6-20329281ddab_643dfe65-cca0-413c-8956-f852ec79d00a.jpg	2026-11-03 08:05:11+00	2026-11-05 03:21:06+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	03122026	Male	\N	1982-05-17	Filipino	\N	College Secretary	16.00	Bachelor of Science in Information Systems	Master of Business Administration	na
33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	335c70b6-c19a-4214-8b7b-fb771a2c9e02	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Noreen	B.	Fuentes	\N	09170000010	\N	Cebu City	full_time	\N	\N	2026-10-01 09:21:41.133736+00	2026-11-01 01:16:50+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	927202602	Female	Married	1960-06-27	Filipino	\N	\N	\N	BS in Computer Engg	MST-CS	Doctor of Philosophy/Doctor in Information Technology
01a1a03e-600f-72b2-be2b-199e54a81312	32ab44ac-a571-4128-8306-8d6f38c050f6	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Oddyssy	Aying	Subingsubing	\N	+639497820262	\N	Cebu City	full_time	Instructor III	01a1a03e-600f-72b2-be2b-199e54a81312_32ac8457-4f6c-43ac-8984-6475d5bc3f35.png	2026-11-03 07:10:04+00	2026-11-03 07:16:21+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	03182004	Male	Married	2004-03-18	Filipino	\N	\N	\N	Bachelor of Science in Information Systems	Master of Information Technology	Doctor of Information Technology
01a1a081-7f25-7366-88dc-ca46e4d8782f	03ffd937-4f2b-4984-bce1-22b9bf2a29a9	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Suzette	B.	Bacus	\N	+63917384625	\N	Cebu City	full_time	Instructor II	\N	2026-11-03 08:23:23+00	2026-11-03 08:30:32+00	8de117bd-ed91-45dc-9ad4-f75eede0cb38	2026-2563	Female	Married	1977-07-12	Filipino	\N	\N	\N	Bachelor of Science in Information Technology	Master of Information Technology	Ph.D.
01a1baec-8076-7080-a841-079a4c219fe9	2005b54a-b45b-437a-bb5a-e167c8137a02	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Black	N/A	Pink	\N	+639562364587	\N	Babag I	full_time	Professor I	\N	2026-11-08 11:30:23+00	2026-11-08 11:30:23+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	1108202601	Female	Single	1994-02-01	Korean	\N	\N	\N	\N	\N	\N
01a1a08a-6633-71da-a16e-269a62efff07	d126364e-680c-4fe5-879b-52a849cf8e19	c5d5cb3b-eda7-4f13-99b1-f7602a749b30	Emelie	C.	Villaceran	\N	+639990000003	\N	Cebu City	full_time	Instructor I	01a1a08a-6633-71da-a16e-269a62efff07_aa58d97d-7371-4592-b5f2-3c424f4abbef.jpg	2026-11-03 08:33:07+00	2026-11-10 12:02:03+00	5694bc6e-69e8-4dfd-b78b-35af1289ad11	03152026	Female	Married	1970-02-24	Filipino	\N	PERG Chair	16.00	Bachelor of Science in Information Technology	Master of Information Technology	Doctor of Philosophy
\.


--
-- Data for Name: faculty_account_reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.faculty_account_reviews (fvr_id, fvr_usr_id, fvr_id_photo_path, fvr_status, fvr_reviewed_by, fvr_reviewed_at, created_at, updated_at, fvr_applicant_name, fvr_applicant_email, fvr_decision_note) FROM stdin;
8ca485d0-0485-45d0-9a90-c6fc1794f224	8cf04253-216f-493e-9724-7450f19d5955	faculty-id-verification/BIcVQyCCNCN42zo5wkjB0nbRFafHBOdeKcPwxkWx.png	rejected	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-10-06 00:05:43	2026-10-05 17:12:24	2026-10-06 00:05:43	Sakura Haruno Uchiha	sakurauchiha59@ctu.edu.ph	No ID Fictional Character
0ff7a695-dc13-4cb5-a999-23e6bde2cf0b	0af0827c-cb31-477f-a44d-2c7404fbb2de	faculty-id-verification/Odf9Kx7BtTuOAMhOsqmjswSasp8pvkSjrNlpfb7Y.png	rejected	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-10-06 05:38:27	2026-10-06 05:37:35	2026-10-06 05:38:27	Donita Tanghal Tumulak	donita@ctu.edu.ph	Not my face
168f3fdd-6615-4e26-8c6e-1ef2ff17c493	625f49bd-e051-4b34-a55d-58f4028daa0a	faculty-id-verification/1EzCJsbVDXAMFCVAqN2y9tfs7VEVYa9bjwrDdCjG.jpg	rejected	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-10-27 04:07:25	2026-10-27 04:06:10	2026-10-27 04:07:25	Neil James M Obeso Jr	tristan0922@gmail.com	use another account
1d27e246-7ce6-437c-9f56-3d36a849f257	692d94e8-9428-47e3-9842-d1f331084603	faculty-id-verification/fN9YUQDvLMCQjlWdbeCll16CD0sBRxi1ntqsy4zF.png	rejected	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-01 01:32:43	2026-10-06 05:43:21	2026-11-01 01:32:43	Donita Tanghal Tumulak	donita@ctu.edu.ph	No ID
e7c7dd9f-cc3e-42eb-9e40-89980764cc19	32ab44ac-a571-4128-8306-8d6f38c050f6	faculty-id-verification/FYyZV2PQ5k08UsIYz9c68xwDCqvc7N89SOnj0u4n.png	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 07:12:04	2026-11-03 07:10:05	2026-11-03 07:12:04	\N	\N	\N
a1fa86d6-84d4-4a11-9bba-251e088f4634	bc02c426-ac11-4fb4-93d5-eb6804b7974e	faculty-id-verification/CWOFwyUqzZqDwLv063GrwYJqduzTk7ZtUrhCj2as.jpg	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 07:21:50	2026-11-03 07:20:42	2026-11-03 07:21:50	\N	\N	\N
6257f202-a6c3-45aa-b511-4296e1f50ac7	e891982b-d1b2-45c8-a64f-e2c5615e433a	faculty-id-verification/9QmNVbnflqP0zhkJlAv1dCdZuWQnC8XXmEctdW55.jpg	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 08:07:07	2026-11-03 08:05:11	2026-11-03 08:07:07	\N	\N	\N
d57ab905-7f9a-4de4-847e-4ba3c7bcfac2	f86e6a21-7652-46b1-941a-61589fe4a60d	faculty-id-verification/WInXXnp1HWCrM5TY60oYQ32Uc9WZe2bQnL4AYL2e.png	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 08:15:53	2026-11-03 08:15:37	2026-11-03 08:15:53	\N	\N	\N
e8cd8376-6a99-4344-8446-9159c05a9fe5	80e78fb0-6908-422c-ae9e-cc31a7a29f04	faculty-id-verification/bcJDx1lslYpTMQk1l9PztQFRNF2S4gxCeD3ZG3M3.jpg	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 08:25:24	2026-11-03 08:23:18	2026-11-03 08:25:24	\N	\N	\N
8f56fa7d-5767-4df7-a072-450ed0a6275c	03ffd937-4f2b-4984-bce1-22b9bf2a29a9	faculty-id-verification/H070y1gfd9gjXwlLQ675pnRmOi6thi5BSXXuVdBz.jpg	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 08:28:38	2026-11-03 08:23:23	2026-11-03 08:28:38	\N	\N	\N
1fb1299f-33d4-4e86-a994-b5ad75e7b435	0a6013f9-0b25-4383-a1f9-f81cac3ea32d	faculty-id-verification/jIJl7tZatpzXpIwIehyjMAKSx9D7a7K4XKrk8RwF.jpg	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 08:30:22	2026-11-03 08:29:44	2026-11-03 08:30:22	\N	\N	\N
f4621259-120e-465b-9ec7-567c11393625	cc1bb940-ddcc-43b4-b9f7-316954fe8da0	faculty-id-verification/XUDJVCyglymfGcCEXJ1WokGpU5XB1ZMjj1vNGymM.png	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 08:32:21	2026-11-03 08:30:29	2026-11-03 08:32:21	\N	\N	\N
fefa2366-a955-46f1-bb2b-5d243dfe86b7	d126364e-680c-4fe5-879b-52a849cf8e19	faculty-id-verification/QhP9QWI3Q1EAAepUoi75Of9qYbpvWJzg4giTsSvm.jpg	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-03 08:34:05	2026-11-03 08:33:07	2026-11-03 08:34:05	\N	\N	\N
8b3c02b2-40c2-40f8-9ff6-90a8d4ebb4f7	b73fc6f4-e667-4f9b-af80-4ec7b4e9269a	faculty-id-verification/Js2aQRh0JEmOwitexQxPGdGcjzQBYl4ctuzDMqVt.png	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-05 02:30:46	2026-11-05 02:29:25	2026-11-05 02:30:46	\N	\N	\N
f6f7626a-d7c2-4388-b085-0cf0f429d588	2005b54a-b45b-437a-bb5a-e167c8137a02	faculty-id-verification/n5HMtOA4HMk3hMkH2ydaBgR3PumokfYebGtbkOTr.png	approved	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-11-08 11:33:19	2026-11-08 11:30:24	2026-11-08 11:33:19	\N	\N	\N
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: historical_schedule; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.historical_schedule (hist_id, hist_fac_id, hist_subj_id, hist_sec_id, hist_day, hist_start_time, hist_end_time, hist_sem_id, hist_ay_id, hist_archived_at) FROM stdin;
a244234e-df61-4741-8113-8ce965e1f47a	71a00f35-6a60-4165-8c14-85c402eaada1	4a4abb03-6a8f-47b6-a380-c6f9e4c0df47	ec8600da-1c7c-4fd6-a67e-e665bc5157d1	Monday	08:00:00	11:00:00	244d6e08-d6e0-4dbb-b59c-aace0e0b6348	fbd45845-3a8b-4484-8cc5-0312408111e2	2026-07-15 15:03:40.332265+00
8660da97-17e4-411d-88a8-24dd1858e9c6	91c58e81-54ce-4ce1-8c49-b78324b9e2c8	2af0aa4c-2697-4cc5-bf2b-fa13c24ec3a8	81603ad3-7337-4ae5-ae9f-c9456f3641bb	Tuesday	01:00:00	04:00:00	4be9ba82-adf8-4625-a376-d18b4dddd352	fbd45845-3a8b-4484-8cc5-0312408111e2	2026-07-15 15:03:40.332265+00
27c3ea7c-9d61-4588-90fe-24aeca8fb9d9	56e063b6-76e5-4382-85c9-89296f4cc916	2df9b47a-70ed-4f45-833f-c4f0f8320746	06988bfc-08c2-4fc7-ae9c-7fe6c491bdfe	Wednesday	08:00:00	11:00:00	244d6e08-d6e0-4dbb-b59c-aace0e0b6348	a880a964-ef79-4b67-948b-acf76c0c0c09	2026-07-15 15:03:40.332265+00
912b163f-1623-46db-a69d-1ae72edffce3	c980c068-8108-4881-85f6-0d8df6a70870	75fb12d1-3626-45fb-b09c-6a61aea1fd03	36afdb09-814e-4bbf-b287-61e5480163b7	Thursday	09:00:00	12:00:00	4be9ba82-adf8-4625-a376-d18b4dddd352	a880a964-ef79-4b67-948b-acf76c0c0c09	2026-07-15 15:03:40.332265+00
2fb96ee6-7745-41b3-a49a-de10b645ab8e	bec360f4-9e21-4e8b-9429-0d5f2079d187	99ba581e-ac78-4217-b3ad-3669926651ec	7ad85261-33d4-4332-9801-d86f76b08cb1	Friday	08:00:00	11:00:00	244d6e08-d6e0-4dbb-b59c-aace0e0b6348	fbd45845-3a8b-4484-8cc5-0312408111e2	2026-07-15 15:03:40.332265+00
\.


--
-- Data for Name: institution; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.institution (inst_id, inst_name, inst_branch_campus, inst_college, inst_abbreviation, inst_contact_email, inst_phone, inst_created_at, inst_updated_at) FROM stdin;
14234048-d639-4c98-811e-ce04b2296c0d	Cebu Technological University	Main Campus	College of Computing, Information and Communications Technology	CCICT	information@ctu.edu.ph	(032) 402-4060	2026-09-26 05:04:11	2026-09-26 05:04:11
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
5	2026_09_25_061547_admin_profile_images	2
6	2026_09_25_072000_personal_info	3
7	2026_09_25_144040_languages_website	4
8	2026_09_26_060349_notification_preferences	5
9	2026_09_26_063945_security_settings	6
10	2026_09_26_065439_system_backup	7
11	2026_09_26_121724_remove_settings_columns	8
12	2026_09_26_143833_profile_images	9
13	2026_09_26_154339_dean_bio	10
14	2026_09_27_055415_lockouts_fields	11
15	2026_10_05_120000_allow_multiple_schedule_sessions_per_study_load	12
16	2026_10_06_000000_create_faculty_account_reviews_table	13
17	2026_10_06_120000_add_rejection_decision_to_faculty_account_reviews	14
18	2026_10_07_000000_add_schedule_snapshot_to_schedule_submissions	15
19	2026_10_07_010000_add_faculty_id_to_schedule_submissions	16
20	2026_10_09_091627_create_personal_access_tokens_table	17
21	2026_10_05_130000_add_college_assignment_to_rooms	18
22	2026_10_07_000000_add_special_position_load_to_faculty	18
23	2026_10_26_120000_add_shift_to_schedule_and_study_load	18
24	2026_10_27_051022_add_rec_subject_and_nullable_sections	19
25	2026_10_27_053630_relax_room_uniqueness_constraint	20
26	2026_10_28_000000_remove_room_location_column	21
27	2026_10_31_181025_add_degree_fields_to_faculty_table	22
28	2026_11_04_114417_create_schedule_archives_table	23
29	2026_11_05_152030_increase_workload_max_hours	24
30	2026_11_05_154640_add_is_evening_to_section_table	25
31	2026_11_09_000000_enforce_30_hour_workload_limit	26
\.


--
-- Data for Name: notification; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notification (notif_id, notif_usr_id, notif_title, notif_message, notif_type, notif_is_read, notif_created_at, notif_updated_at) FROM stdin;
a5c57ddc-3746-428f-a710-60df9a567053	270ad7b7-7c4d-4823-b314-816b91773cb9	System Backup	Daily backup completed successfully.	info	t	2026-07-15 15:05:53.620819+00	2026-09-26 11:57:33+00
01a10d17-70ed-7035-b3c9-235f52ea5c3e	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Nami N Bell submitted a registration for review.	new_user	f	2026-10-05 17:23:22+00	2026-10-05 17:23:22+00
01a10fb7-a64f-7004-901c-c66d975c8479	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Donita Tanghal Tumulak submitted a registration for review.	new_user	f	2026-10-06 05:37:36+00	2026-10-06 05:37:36+00
01a10fbc-edb7-7066-9302-e307db502d04	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Donita Tanghal Tumulak submitted a registration for review.	new_user	f	2026-10-06 05:43:22+00	2026-10-06 05:43:22+00
01a11004-1d69-7008-b496-7f2c994bd78e	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Gerry Ritz R. Densing submitted a registration for review.	new_user	f	2026-10-06 07:01:07+00	2026-10-06 07:01:07+00
01a115f3-21c5-731a-b367-aba267a64416	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Monkey D. Dragon submitted a registration for review.	new_user	f	2026-10-07 10:40:17+00	2026-10-07 10:40:17+00
d997452a-9711-4fea-ab41-db984db15894	0180a717-2d4b-4d0d-a418-153b7b24911e	schedule submission reminder	need to submit within this week for the schedules	info	f	2026-10-08 06:26:07+00	2026-10-08 06:26:07+00
01a16d3a-2ccf-72e0-bfca-c0e344d840d3	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Oddyssy Aying Subingsubing submitted a registration for review.	new_user	f	2026-10-24 09:24:51+00	2026-10-24 09:24:51+00
01a17b89-7dd2-73b6-a035-1d8d7ef73c9a	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Neil James M Obeso Jr submitted a registration for review.	new_user	f	2026-10-27 04:06:10+00	2026-10-27 04:06:10+00
01a17b8d-a495-70d5-8c6b-5cc3ddad3855	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Neil James D. Urobosa Sr submitted a registration for review.	new_user	f	2026-10-27 04:10:42+00	2026-10-27 04:10:42+00
01a191b9-bbc6-713b-91c3-e0db42a102ff	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Stephebn Subinngsubing Dignos Na submitted a registration for review.	new_user	f	2026-10-31 11:30:30+00	2026-10-31 11:30:30+00
01a194b3-0292-70f9-add4-ff072e872b1c	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Rante A. Subingsubing Na submitted a registration for review.	new_user	f	2026-11-01 01:22:02+00	2026-11-01 01:22:02+00
01a194c3-c34f-720e-abde-d44e7ab8b6a3	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Rante A. Subingsubing Na submitted a registration for review.	new_user	f	2026-11-01 01:40:19+00	2026-11-01 01:40:19+00
01a194d4-259c-7293-bb74-b3cd9a80a292	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Rante S. Subingsubing Na submitted a registration for review.	new_user	f	2026-11-01 01:58:13+00	2026-11-01 01:58:13+00
84103714-7db2-45fe-89e5-921b913ed554	335c70b6-c19a-4214-8b7b-fb771a2c9e02	schedule submission reminder	need to submit within this week for the schedules	info	t	2026-10-08 06:26:07+00	2026-11-01 05:12:00+00
01a1a033-9e0e-73b2-b43e-18c7047507f0	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Jose  Maria S Garcia II submitted a registration for review.	new_user	f	2026-11-03 06:58:19+00	2026-11-03 06:58:19+00
01a1a036-f26f-7014-8e6c-3bc5e28fe026	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Jose  Maria S Garcia II submitted a registration for review.	new_user	f	2026-11-03 07:01:57+00	2026-11-03 07:01:57+00
01a1a03a-c036-716b-a1ee-ee1e1e65a3d7	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Oddyssy Aying Subingsubing submitted a registration for review.	new_user	f	2026-11-03 07:06:07+00	2026-11-03 07:06:07+00
01a1a03e-645e-73a6-a810-a36409ffb12f	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Oddyssy Aying Subingsubing submitted a registration for review.	new_user	f	2026-11-03 07:10:05+00	2026-11-03 07:10:05+00
01a1a048-1c4d-71fd-bc9c-d9b004379635	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Jose  Maria S Garcia II submitted a registration for review.	new_user	f	2026-11-03 07:20:42+00	2026-11-03 07:20:42+00
01a1a06a-673f-71ba-bb9f-348145319524	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	John Rey E. Furtos submitted a registration for review.	new_user	f	2026-11-03 07:58:10+00	2026-11-03 07:58:10+00
01a1a070-d7ec-721d-a8a9-3ee5c6fb895d	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	John Rey E. Furtos submitted a registration for review.	new_user	f	2026-11-03 08:05:12+00	2026-11-03 08:05:12+00
01a1a07a-6b78-707e-9ad6-8bee548ace05	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Raamah C Rosales submitted a registration for review.	new_user	f	2026-11-03 08:15:39+00	2026-11-03 08:15:39+00
01a1a081-6a7f-7126-aa97-3cf98f1feba7	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Albert D. Maghanoy submitted a registration for review.	new_user	f	2026-11-03 08:23:18+00	2026-11-03 08:23:18+00
01a1a081-813c-7320-b580-4a066c901509	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Suzette B. Bacus submitted a registration for review.	new_user	f	2026-11-03 08:23:24+00	2026-11-03 08:23:24+00
01a1a087-4e26-7204-b76e-21dd6024149b	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Janet S. Ugang submitted a registration for review.	new_user	f	2026-11-03 08:29:44+00	2026-11-03 08:29:44+00
01a1a088-07c4-70b9-bdb7-ac871b2bd989	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Raamah C. Rosales submitted a registration for review.	new_user	f	2026-11-03 08:30:31+00	2026-11-03 08:30:31+00
01a1a08a-6859-7225-9123-17be3357260f	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Emelie C. Villaceran submitted a registration for review.	new_user	f	2026-11-03 08:33:07+00	2026-11-03 08:33:07+00
01a1a98a-2c5a-72b7-8196-e7bf978d6181	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Boom Bayah Esmeringhoy III submitted a registration for review.	new_user	f	2026-11-05 02:29:27+00	2026-11-05 02:29:27+00
01a1baec-8251-72ef-a4eb-580daf0783fe	270ad7b7-7c4d-4823-b314-816b91773cb9	New faculty account pending approval	Black N/A Pink submitted a registration for review.	new_user	f	2026-11-08 11:30:24+00	2026-11-08 11:30:24+00
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
14	App\\Models\\User	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Skedyul Faculty Mobile	cb9f0fc84aaf19a68100304cfe1280a9bced15da59c02ce1c5e764cb66332c6c	["faculty:read"]	2026-10-26 07:03:49	\N	2026-10-26 07:00:46	2026-10-26 07:03:49
8	App\\Models\\User	4cb977d8-e643-43f9-b1cd-5bacccaf2e90	Skedyul Faculty Mobile	e03c958a865025f82642f2e089c52f3a7e2411cd8648142155cd01c52377d6f8	["faculty:read"]	2026-10-14 05:44:42	\N	2026-10-14 05:44:36	2026-10-14 05:44:42
16	App\\Models\\User	b4bb728c-861e-4ed9-b6ef-4ededcc5bfcc	Skedyul Faculty Mobile	a2dca2d9296edde1907279f5dd4a10d5f84fddb0973a7611209868981d12ef82	["faculty:read"]	2026-10-26 13:45:58	\N	2026-10-26 13:45:56	2026-10-26 13:45:58
17	App\\Models\\User	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Skedyul Faculty Mobile	bda0c63c45b4fab8548121095cefc064fbe0b381856d673ffb1be7801efe6909	["faculty:read"]	\N	\N	2026-10-31 20:12:35	2026-10-31 20:12:35
18	App\\Models\\User	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Skedyul Faculty Mobile	2851533a58615596bc81a8140ac754cce229ccd3c24c3211f42f2308f14d1f9b	["faculty:read"]	2026-11-01 01:16:27	\N	2026-10-31 20:40:27	2026-11-01 01:16:27
19	App\\Models\\User	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Skedyul Faculty Mobile	1807c19e4f39f5023dfd96f1f8e5bdd6dc936e0fa0912db51e3479b710515430	["faculty:read"]	2026-11-07 04:58:44	\N	2026-11-07 04:58:42	2026-11-07 04:58:44
5	App\\Models\\User	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Skedyul Faculty Mobile	d53f2798fe718aba2a6b5ebaf21a35b32824e5eb59e0c01e3305ae136484143d	["faculty:read"]	2026-10-09 11:14:12	\N	2026-10-09 11:14:09	2026-10-09 11:14:12
13	App\\Models\\User	335c70b6-c19a-4214-8b7b-fb771a2c9e02	Skedyul Faculty Mobile	3030b8b074c88e96ebdf15d50a0d7356c3dcb19e9758a3a4b6204611c6509c97	["faculty:read"]	2026-10-24 10:23:45	\N	2026-10-24 10:21:30	2026-10-24 10:23:45
\.


--
-- Data for Name: room; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.room (room_id, room_name, room_building, room_type, room_capacity, room_is_available, room_created_at, room_updated_at, room_college_id) FROM stdin;
005e0bf9-90c1-4328-a11b-dd41e935247c	Room 301	Engineering Building	Lecture	45	t	2026-07-15 15:02:46.174993+00	2026-07-15 15:02:46.174993+00	\N
18751cd8-03d3-4e81-b202-9bb01141479c	Smart Classroom	Technology Building	Lecture	50	t	2026-07-15 15:02:46.174993+00	2026-07-15 15:02:46.174993+00	\N
ed195474-f14a-4f1b-8a0e-3f8b1e377d4b	Computer Laboratory 1	CCICT Building	Laboratory	40	t	2026-07-15 15:02:46.174993+00	2026-10-05 11:11:19+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
712d805b-0f9f-490d-98ec-72547ac36104	Computer Laboratory 2	CCICT Building	Laboratory	40	t	2026-07-15 15:02:46.174993+00	2026-10-05 11:11:27+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
ba0519d1-cda5-4a39-a3d6-dae792eb8366	Networking Laboratory	CCICT Building	Laboratory	35	t	2026-07-15 15:02:46.174993+00	2026-10-05 11:11:37+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bc2-0543-7291-8245-b4aa9535a39d	NICT-804	NCCICT Building	Laboratory	50	t	2026-10-27 05:07:55+00	2026-10-27 06:22:44+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bc0-df9f-71d9-9c74-d3c001aa7636	NICT-803	NCCICT Building	Laboratory	50	t	2026-10-27 05:06:40+00	2026-10-27 06:22:52+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a176e0-3cda-73eb-abd5-d05a057346cf	NICT-802	NCCICT Building	Laboratory	50	t	2026-10-26 06:22:49+00	2026-10-27 06:23:01+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bcd-ddbc-729c-84e8-c2965d70ec2c	NICT-701	NCCICT Building	Lecture	50	t	2026-10-27 05:20:51+00	2026-10-27 06:23:11+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bc2-7ffa-70e7-ad0b-1425dec9e6cf	NICT-805	NCCICT Building	Laboratory	50	t	2026-10-27 05:08:26+00	2026-10-27 06:23:31+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bc3-0846-70f7-838f-78f46a9c229f	NICT-806	NCCICT Building	Laboratory	50	t	2026-10-27 05:09:01+00	2026-10-27 06:23:42+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bc3-e393-732e-936f-8d7d53bd790e	NICT-807	NCCICT Building	Laboratory	50	t	2026-10-27 05:09:57+00	2026-10-27 06:23:57+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bcc-65bb-7120-b431-3c89f25ccded	NICT-808	NCCICT Building	Laboratory	50	t	2026-10-27 05:19:15+00	2026-10-27 06:24:43+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bcc-f742-7317-adf8-51246c2d8cdd	NICT-809	NCCICT Building	Laboratory	50	t	2026-10-27 05:19:52+00	2026-10-27 06:24:56+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bf9-6f71-7398-b483-83e9a64e5ffc	NICT-702	NCICCT Building	Lecture	50	t	2026-10-27 06:08:27+00	2026-10-27 06:25:23+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bfa-6247-73a9-b57a-90b0e8213149	NICT-703	NCICCT Building	Lecture	50	t	2026-10-27 06:09:29+00	2026-10-27 06:25:34+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bfd-3b82-7254-8b81-4481eef3759c	NICT-704	NCICCT Building	Lecture	50	t	2026-10-27 06:12:35+00	2026-10-27 06:25:43+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bfd-a2c0-717d-9ac9-7a2d9664898d	NICT-705	NCICCT Building	Lecture	50	t	2026-10-27 06:13:02+00	2026-10-27 06:25:57+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bfe-0c49-70c5-bf4a-04ed2299b141	NICT-706	NCICCT Building	Lecture	50	t	2026-10-27 06:13:29+00	2026-10-27 06:26:08+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bfe-5c34-71d1-92de-103dcb66defe	NICT-707	NCICCT Building	Lecture	50	t	2026-10-27 06:13:49+00	2026-10-27 06:26:21+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bff-0d7b-739d-b84a-467ca4fc5fa7	NICT-708	NCICCT Building	Lecture	50	t	2026-10-27 06:14:35+00	2026-10-27 06:26:34+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17bff-bf7f-7254-b603-6c7f491731ee	NICT-709	NCICCT Building	Lecture	50	t	2026-10-27 06:15:20+00	2026-10-27 06:26:43+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c01-22df-7386-9f83-908d579df931	NICT-601	NCICCT Building	Laboratory	50	t	2026-10-27 06:16:51+00	2026-10-27 06:27:07+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c01-9594-7329-9a15-d70c42621d70	NICT-602	NCICCT Building	Laboratory	50	t	2026-10-27 06:17:21+00	2026-10-27 06:27:18+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c02-3fe8-708f-a00c-04376fddf85c	NICT-603	NCICCT Building	Laboratory	50	t	2026-10-27 06:18:04+00	2026-10-27 06:27:28+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c02-bfb8-7070-bbde-b163456119e5	NICT-604	NCICCT Building	Laboratory	50	t	2026-10-27 06:18:37+00	2026-10-27 06:27:41+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c03-045a-71f0-8a7a-b235f135a727	NICT-605	NCICCT Building	Laboratory	50	t	2026-10-27 06:18:54+00	2026-10-27 06:27:51+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c03-a9f0-73a5-87be-99e46e353412	NICT-606	NCICCT Building	Laboratory	50	t	2026-10-27 06:19:37+00	2026-10-27 06:28:03+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c0b-c140-734b-8471-7a4167dadb84	NICT-608	NCICCT Building	Laboratory	50	t	2026-10-27 06:28:27+00	2026-10-27 06:28:27+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c0c-18ef-7349-b50d-8486197666c0	NICT-607	NCICCT Building	Laboratory	50	t	2026-10-27 06:28:50+00	2026-10-27 06:28:50+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
01a17c0c-75c3-7142-aa32-54dc3f652f07	NICT-609	NCICCT Building	Laboratory	50	t	2026-10-27 06:29:13+00	2026-10-27 06:29:13+00	c5d5cb3b-eda7-4f13-99b1-f7602a749b30
\.


--
-- Data for Name: schedule; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.schedule (sch_id, sch_load_id, sch_fac_id, sch_course_id, sch_sec_id, sch_room_id, sch_sem_id, sch_day, sch_start_time, sch_end_time, sch_status, sch_is_active, sch_created_by, sch_created_at, sch_updated_at, sch_mis_code, sch_shift) FROM stdin;
c1ac7966-cb6f-4faa-972d-77d5e0d0d96f	6c1389dd-1b37-4093-b935-904ca5fa64ed	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	b39f2514-7cec-427c-b482-ad9acdf7db4f	01a17bcc-65bb-7120-b431-3c89f25ccded	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	07:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 10:17:59+00	2026-11-04 10:18:39+00	\N	day
db3a2457-8ec0-4bca-89ce-8af54c52e1d3	6b5a248d-d383-4986-8dc2-3de8a788d456	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	52d55b72-a0b5-44d8-a3cf-cb61a2feedc1	01a17bcc-f742-7317-adf8-51246c2d8cdd	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	16:00:00	21:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 12:53:35+00	2026-11-04 12:53:35+00	\N	evening
4c629b1a-e26e-476d-b20a-115ec2cbc4c0	08348508-98b6-4e8d-ae26-67c110c7e747	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	6bd5e6b4-76be-4edb-90ee-f7cb6c55e2b4	01a17bcc-f742-7317-adf8-51246c2d8cdd	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	16:00:00	21:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 12:53:57+00	2026-11-04 12:53:57+00	\N	evening
322ca05e-404e-4638-9c3f-ece2f95d8996	9c235c41-2503-488d-a202-5cef1b225604	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	15f66c86-9bf9-42ef-bca3-4b10320842dd	6336b405-e9be-4787-96d3-bd3089e48da9	01a17bfe-0c49-70c5-bf4a-04ed2299b141	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	13:00:00	15:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 11:07:09+00	2026-11-04 14:28:33+00	\N	day
ba231c3b-b91d-421c-806a-ed6f3da49bcd	9c235c41-2503-488d-a202-5cef1b225604	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	15f66c86-9bf9-42ef-bca3-4b10320842dd	6336b405-e9be-4787-96d3-bd3089e48da9	01a17bfe-0c49-70c5-bf4a-04ed2299b141	462c1f14-8433-47f1-8569-9daeedc758a5	Thursday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 11:07:58+00	2026-11-04 14:30:39+00	\N	day
419ae72c-ea75-45fe-b4cd-949dfddc754f	94d00935-754f-44ed-bdba-81a46d484f74	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	c55f8061-08b2-4728-9656-7216110e27c8	\N	\N	462c1f14-8433-47f1-8569-9daeedc758a5	Friday	07:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 14:31:47+00	2026-11-04 14:31:47+00	\N	day
4058597c-6dbb-41c4-a3e4-10653b452702	94d00935-754f-44ed-bdba-81a46d484f74	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	c55f8061-08b2-4728-9656-7216110e27c8	\N	\N	462c1f14-8433-47f1-8569-9daeedc758a5	Friday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 14:32:04+00	2026-11-04 14:32:04+00	\N	day
3ec05839-466c-4a70-9eab-b3817e4866a6	042b19a9-82d9-4706-aabb-73810b43e297	01a1a070-d483-73bd-a2a6-20329281ddab	a3d2f648-c2d3-4277-951c-8b33f6832b01	6336b405-e9be-4787-96d3-bd3089e48da9	01a17c01-22df-7386-9f83-908d579df931	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	07:00:00	09:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:48:56+00	2026-11-05 02:48:56+00	\N	day
1943dabf-ae16-4689-8903-75a3c9d9be5d	1b1a2a90-efe3-46af-b812-5bd57d04503d	01a1a070-d483-73bd-a2a6-20329281ddab	221d8268-bbc3-427d-8e18-3d6457d3c2fb	d5a17999-4c46-4008-8a68-15c219866d25	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	09:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:53:50+00	2026-11-05 02:53:50+00	\N	day
60320397-72ea-4de8-a76f-86d7363c0655	3ae674ec-3fdc-4564-ae2c-559d6c3dd951	01a1a070-d483-73bd-a2a6-20329281ddab	a3d2f648-c2d3-4277-951c-8b33f6832b01	87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	16:30:00	18:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 03:24:20+00	2026-11-05 03:24:20+00	\N	evening
32fa3f22-268c-4bef-aa7f-0379913f6798	917d8648-c78e-466d-b78b-efe60afde918	01a1a048-1a3d-708c-bc17-93d208cbc837	9751862a-6d6a-417e-9834-26e920dfebba	03cfe1ca-5358-42f8-8975-b1d3b3c982ff	01a17c0c-75c3-7142-aa32-54dc3f652f07	462c1f14-8433-47f1-8569-9daeedc758a5	Saturday	09:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 02:50:34+00	2026-11-06 02:50:34+00	\N	day
1f5fea61-5a04-419e-ae83-d3f0672aa1b2	8f218c48-8125-4346-8d8a-0ec29d1f6013	01a11004-1aa5-7125-9178-590a1a432970	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c02-3fe8-708f-a00c-04376fddf85c	462c1f14-8433-47f1-8569-9daeedc758a5	Thursday	16:30:00	19:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:05:18+00	2026-11-06 03:05:18+00	\N	evening
ce6cb234-ae4f-4c30-9431-0e62cdef77b0	df447d85-a228-40fb-89d6-f3639eb0c092	01a1a03e-600f-72b2-be2b-199e54a81312	8bab364d-dbe5-42d1-bd60-c1b6039bc861	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c01-22df-7386-9f83-908d579df931	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	16:30:00	19:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:09:41+00	2026-11-06 03:09:41+00	\N	evening
cabe7e8c-8dd6-490b-a1b2-330427bd2761	9558fc9b-480f-462d-962e-5d07f0695331	01a1a048-1a3d-708c-bc17-93d208cbc837	9751862a-6d6a-417e-9834-26e920dfebba	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c0c-75c3-7142-aa32-54dc3f652f07	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	16:30:00	18:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:16:53+00	2026-11-06 03:16:53+00	\N	evening
6fb8d1a9-a145-429d-9741-68f057ec79a0	746ebb64-064f-4dca-ba32-2acdf3f4e587	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	efa79181-f225-4ad9-b5ae-1b28bb2dfdb3	6bd5e6b4-76be-4edb-90ee-f7cb6c55e2b4	01a17c0c-75c3-7142-aa32-54dc3f652f07	462c1f14-8433-47f1-8569-9daeedc758a5	Saturday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:22:05+00	2026-11-06 03:22:05+00	\N	day
1bdd0460-31c6-4148-a60d-bf9d170dc8e1	4faacb79-0e97-405f-8343-5561b6d03ff6	01a1a08a-6633-71da-a16e-269a62efff07	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	b5cbceae-f8a7-4c26-ab2b-e6d3a874ab56	01a17c03-a9f0-73a5-87be-99e46e353412	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	16:30:00	21:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:09:33+00	2026-11-10 12:09:33+00	\N	evening
328589e9-163a-442e-9d39-a1ae06f2abd7	75539ea8-2ae8-4201-bfc0-347c15e7c558	01a1a070-d483-73bd-a2a6-20329281ddab	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	01a17c0b-c140-734b-8471-7a4167dadb84	462c1f14-8433-47f1-8569-9daeedc758a5	Saturday	07:30:00	09:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-08 15:51:56+00	2026-11-08 16:02:18+00	\N	day
16af4b4f-5ecb-4715-8623-36b293279d27	f8f35617-9adc-4749-8056-f48b9b6c17cf	01a1a08a-6633-71da-a16e-269a62efff07	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	b453cf9f-6802-4b9b-b9f7-c9ae578563f6	01a17c03-a9f0-73a5-87be-99e46e353412	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	16:30:00	18:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:08:42+00	2026-11-10 12:08:42+00	\N	evening
8c633a50-22c3-438d-a8af-822cd81a8bb4	5d52c857-93f4-4225-8851-24e10c5a5d5e	01a1a08a-6633-71da-a16e-269a62efff07	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	b453cf9f-6802-4b9b-b9f7-c9ae578563f6	01a17c03-a9f0-73a5-87be-99e46e353412	462c1f14-8433-47f1-8569-9daeedc758a5	Thursday	16:30:00	21:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:31:44+00	2026-11-10 12:31:44+00	\N	evening
f49cb8f7-4d32-4db9-a589-93a754eb9512	5d52c857-93f4-4225-8851-24e10c5a5d5e	01a1a08a-6633-71da-a16e-269a62efff07	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c03-a9f0-73a5-87be-99e46e353412	462c1f14-8433-47f1-8569-9daeedc758a5	Saturday	08:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:57:33+00	2026-11-10 15:07:28+00	\N	day
c7487a01-8869-40d1-baff-ce99ebca8994	8f8e3f8c-f522-482a-ae61-55a7f2a183ba	01a1a08a-6633-71da-a16e-269a62efff07	c55f8061-08b2-4728-9656-7216110e27c8	\N	\N	462c1f14-8433-47f1-8569-9daeedc758a5	Friday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 17:40:40+00	2026-11-10 17:41:15+00	\N	day
7fc84490-540a-4441-90ca-118c5d7a6158	7f88d2fe-83b1-479d-af16-671524f07acf	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	a9326d77-b132-4a11-abc6-5889ef05ebb6	01a17bcc-65bb-7120-b431-3c89f25ccded	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	07:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 10:35:59+00	2026-11-04 10:59:49+00	\N	day
8047555a-c3a3-4d54-99cd-0c7c7253bc66	fd775898-f015-4a74-add8-7453dc77d994	01a11004-1aa5-7125-9178-590a1a432970	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c02-3fe8-708f-a00c-04376fddf85c	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	16:30:00	18:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:06:17+00	2026-11-06 03:06:17+00	\N	evening
271d54a9-fe33-4958-98d7-88fc27d132f1	51907221-56a1-457e-b56e-c5ecab92dce0	01a1a087-4c19-70da-8f2e-872c5804e5a1	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	46b38b07-eb74-4ea7-a105-51779a082ea8	01a17bcd-ddbc-729c-84e8-c2965d70ec2c	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	08:00:00	11:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 13:46:17+00	2026-11-04 13:46:17+00	\N	day
01e70c31-9690-4c59-8369-9fa9c66c0047	51907221-56a1-457e-b56e-c5ecab92dce0	01a1a087-4c19-70da-8f2e-872c5804e5a1	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	46b38b07-eb74-4ea7-a105-51779a082ea8	01a17bcd-ddbc-729c-84e8-c2965d70ec2c	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	08:00:00	11:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 13:46:27+00	2026-11-04 13:46:27+00	\N	day
648a43aa-ee5f-45ca-a470-dacd0d3f5a93	e850f153-6fac-4d6d-bf7b-badf837f1181	01a1a087-4c19-70da-8f2e-872c5804e5a1	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	00ce7ab3-99cf-4b89-a1e1-09771e599a45	01a17c01-22df-7386-9f83-908d579df931	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	09:00:00	10:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 16:18:51+00	2026-11-04 16:18:51+00	\N	day
924937b7-2c29-419a-a34d-983d7311990b	84e780bd-2926-43f0-8093-78faf4e570e7	01a1a070-d483-73bd-a2a6-20329281ddab	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	6336b405-e9be-4787-96d3-bd3089e48da9	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	07:00:00	10:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:50:55+00	2026-11-05 02:50:55+00	\N	day
b9452dae-d006-4ecd-837b-3555e1ce2de1	84e780bd-2926-43f0-8093-78faf4e570e7	01a1a070-d483-73bd-a2a6-20329281ddab	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	6336b405-e9be-4787-96d3-bd3089e48da9	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	07:00:00	10:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:50:59+00	2026-11-05 02:50:59+00	\N	day
209adf6f-6890-4c9e-bcff-13979271e967	84e780bd-2926-43f0-8093-78faf4e570e7	01a1a070-d483-73bd-a2a6-20329281ddab	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	6336b405-e9be-4787-96d3-bd3089e48da9	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Thursday	07:00:00	10:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:51:03+00	2026-11-05 02:51:03+00	\N	day
b26347fe-566f-4f92-b938-c5341cbb0fcd	1b1a2a90-efe3-46af-b812-5bd57d04503d	01a1a070-d483-73bd-a2a6-20329281ddab	221d8268-bbc3-427d-8e18-3d6457d3c2fb	d5a17999-4c46-4008-8a68-15c219866d25	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	10:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:56:29+00	2026-11-05 02:56:29+00	\N	day
cb71a731-112e-4f7c-b2a6-e14da2246834	75539ea8-2ae8-4201-bfc0-347c15e7c558	01a1a070-d483-73bd-a2a6-20329281ddab	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Thursday	16:30:00	21:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 03:56:31+00	2026-11-05 03:56:31+00	\N	evening
08e16e78-ce28-44ec-98f0-bf0938699c54	f6f4163f-4122-49db-8d8c-652e9128a183	01a1a070-d483-73bd-a2a6-20329281ddab	221d8268-bbc3-427d-8e18-3d6457d3c2fb	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c01-9594-7329-9a15-d70c42621d70	462c1f14-8433-47f1-8569-9daeedc758a5	Friday	16:30:00	21:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 02:43:26+00	2026-11-06 02:43:26+00	\N	evening
334e2290-6bdd-4d8a-9887-0999e4a108f3	4087e488-4d41-433a-ab0c-05c546341331	01a1a03e-600f-72b2-be2b-199e54a81312	8cd451f2-cca7-4a4e-b7d1-ef1e541b60ef	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c02-3fe8-708f-a00c-04376fddf85c	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	18:30:00	20:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:07:38+00	2026-11-06 03:07:38+00	\N	evening
39c3c6a0-eb3c-4f1d-866e-0293b02a54ef	9558fc9b-480f-462d-962e-5d07f0695331	01a1a048-1a3d-708c-bc17-93d208cbc837	9751862a-6d6a-417e-9834-26e920dfebba	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17c0c-75c3-7142-aa32-54dc3f652f07	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	20:30:00	21:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:14:01+00	2026-11-06 03:14:01+00	\N	evening
c5cd7e8e-dcf9-4fa7-9240-1e3c2a43a4de	aabcd22f-265c-420f-8f32-499bce0786c4	01a1a03e-600f-72b2-be2b-199e54a81312	d601231f-8bf9-44c1-979d-47b0862983b2	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	01a17bcd-ddbc-729c-84e8-c2965d70ec2c	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	18:30:00	21:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:18:16+00	2026-11-06 03:18:16+00	\N	evening
2e6b40d2-1fcb-46fa-b6f0-6532595b3971	19aaf95a-7e0c-4b59-9c0d-8f77993aaa28	ac6adea7-6397-426f-9fa1-b9e51749b665	221d8268-bbc3-427d-8e18-3d6457d3c2fb	32c670ec-2065-4d2d-b409-51456a537b50	01a17bfd-3b82-7254-8b81-4481eef3759c	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	07:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-10-30 07:03:17+00	2026-10-30 07:03:17+00	\N	evening
3dddf33a-bbd3-4a69-8059-91357b7c8591	d4d81cb7-dd67-4530-a8bc-70a7e46daff9	01a1a08a-6633-71da-a16e-269a62efff07	a3d2f648-c2d3-4277-951c-8b33f6832b01	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	01a17c01-22df-7386-9f83-908d579df931	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	13:00:00	15:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:31:43+00	2026-11-10 11:31:43+00	\N	day
45450e76-f099-4de5-9867-d3ac19e03444	0cd325fa-4660-4fba-a8d0-505780240ebc	01a1a08a-6633-71da-a16e-269a62efff07	129760c4-d92f-4695-a7e3-71e8dcac525f	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	01a17c02-3fe8-708f-a00c-04376fddf85c	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	07:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:33:05+00	2026-11-10 11:33:05+00	\N	day
ae9d881f-25ef-4fdf-82a8-b6e75991e5f7	2c8c1f75-78ee-41f2-a9ca-89f572801ab3	01a1a08a-6633-71da-a16e-269a62efff07	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	01a17c02-3fe8-708f-a00c-04376fddf85c	462c1f14-8433-47f1-8569-9daeedc758a5	Tuesday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:34:07+00	2026-11-10 11:34:07+00	\N	day
f9b58852-e37a-4cd3-8867-7ffbdcfad89f	2c8c1f75-78ee-41f2-a9ca-89f572801ab3	01a1a08a-6633-71da-a16e-269a62efff07	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	01a17c02-3fe8-708f-a00c-04376fddf85c	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:34:13+00	2026-11-10 11:34:13+00	\N	day
c795795c-b635-4883-a5a1-9768f9de30fd	2c8c1f75-78ee-41f2-a9ca-89f572801ab3	01a1a08a-6633-71da-a16e-269a62efff07	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	01a17c02-3fe8-708f-a00c-04376fddf85c	462c1f14-8433-47f1-8569-9daeedc758a5	Thursday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:34:18+00	2026-11-10 11:34:18+00	\N	day
7007b932-88da-47f4-8a8e-7089be392acf	f9fb035e-2d5d-43d6-ad6d-268e241a46f7	01a1a08a-6633-71da-a16e-269a62efff07	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	b5cbceae-f8a7-4c26-ab2b-e6d3a874ab56	01a17c03-a9f0-73a5-87be-99e46e353412	462c1f14-8433-47f1-8569-9daeedc758a5	Monday	16:30:00	18:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:06:25+00	2026-11-10 12:06:25+00	\N	evening
5940e087-b1ce-43aa-b365-dfa21f26f3d2	4faacb79-0e97-405f-8343-5561b6d03ff6	01a1a08a-6633-71da-a16e-269a62efff07	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	b5cbceae-f8a7-4c26-ab2b-e6d3a874ab56	01a17c03-a9f0-73a5-87be-99e46e353412	462c1f14-8433-47f1-8569-9daeedc758a5	Friday	16:30:00	20:30:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:45:14+00	2026-11-10 12:45:14+00	\N	evening
63670ec5-d87d-4b8e-8062-cf64d8907d68	ad1eb7d8-cc2c-4103-954b-51d11fd41743	01a1a08a-6633-71da-a16e-269a62efff07	129760c4-d92f-4695-a7e3-71e8dcac525f	87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	01a17c03-a9f0-73a5-87be-99e46e353412	462c1f14-8433-47f1-8569-9daeedc758a5	Saturday	13:00:00	18:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 13:02:51+00	2026-11-10 14:10:55+00	\N	day
b6d73e5e-0a6b-49e3-8dac-676436ea6d4d	8f8e3f8c-f522-482a-ae61-55a7f2a183ba	01a1a08a-6633-71da-a16e-269a62efff07	c55f8061-08b2-4728-9656-7216110e27c8	\N	\N	462c1f14-8433-47f1-8569-9daeedc758a5	Friday	07:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 17:38:34+00	2026-11-10 17:38:34+00	\N	day
8dd143c1-9eda-486f-a233-e271083ba6f3	d2f8e54d-c165-4705-a950-ad1a2f950baa	01a1baec-8076-7080-a841-079a4c219fe9	9751862a-6d6a-417e-9834-26e920dfebba	46b38b07-eb74-4ea7-a105-51779a082ea8	01a17bfa-6247-73a9-b57a-90b0e8213149	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	13:00:00	16:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 17:45:18+00	2026-11-10 17:45:18+00	\N	day
840d6321-2e59-4073-9e51-cf9d4294b25b	882337c6-ae8d-4abe-97aa-153c6dff2fab	01a1baec-8076-7080-a841-079a4c219fe9	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	5409f47c-a640-4593-951f-19cfc779af9a	01a17bc3-0846-70f7-838f-78f46a9c229f	462c1f14-8433-47f1-8569-9daeedc758a5	Wednesday	07:00:00	12:00:00	draft	t	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 17:47:14+00	2026-11-10 17:47:14+00	\N	day
\.


--
-- Data for Name: schedule_archives; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.schedule_archives (sar_id, sar_sch_id, sar_sem_id, sar_ay_id, sar_fac_id, sar_course_id, sar_sec_id, sar_room_id, sar_day, sar_start_time, sar_end_time, sar_shift, sar_status, sar_is_active, sar_description, sar_created_by, sar_updated_by, sar_archived_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: schedule_submission; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.schedule_submission (schsub_id, schsub_dept_id, schsub_sem_id, schsub_submitted_by, schsub_submitted_at, schsub_reviewed_by, schsub_reviewed_at, schsub_status, schsub_remarks, schsub_schedule_snapshot, schsub_fac_id) FROM stdin;
be8015ed-cbe0-4bdf-aac5-4784927801ad	5694bc6e-69e8-4dfd-b78b-35af1289ad11	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-10-07 06:07:19+00	\N	\N	pending	\N	[{"schedule_id":"6f3509ac-606e-4d32-9bc6-3df0e6d41410","faculty":"Noreen B. Fuentes","subject_code":"P Elec 3","subject_name":"Enterprise Systems","section":"BSIS 4-A","room":"Computer Laboratory 1","day":"Tuesday","start_time":"07:00:00","end_time":"12:00:00"},{"schedule_id":"941738d5-2e64-4ec7-b098-c70eaa0b7aa0","faculty":"Noreen B. Fuentes","subject_code":"P Elec 3","subject_name":"Enterprise Systems","section":"BSIS 4-B","room":"Computer Laboratory 1","day":"Wednesday","start_time":"07:00:00","end_time":"12:00:00"},{"schedule_id":"17b4ff84-96cd-4819-80d9-e3a2c4acaacb","faculty":"Noreen B. Fuentes","subject_code":"PC 318","subject_name":"Enterprise Architecture","section":"BSIS 3-A","room":"Computer Laboratory 1","day":"Thursday","start_time":"13:00:00","end_time":"15:00:00"},{"schedule_id":"3225c1e2-d642-4acf-a902-32da7ed36f86","faculty":"Noreen B. Fuentes","subject_code":"PC 318","subject_name":"Enterprise Architecture","section":"BSIS 3-A","room":"Computer Laboratory 1","day":"Friday","start_time":"13:00:00","end_time":"16:00:00"},{"schedule_id":"1c6cece4-b4ac-46f1-b960-8b9e77658d17","faculty":"Gerry Ritz R. Densing","subject_code":"CC 112 L","subject_name":"Computer Programming 1 (Lab)","section":"BSIS 1-A","room":"Computer Laboratory 2","day":"Wednesday","start_time":"13:00:00","end_time":"15:00:00"},{"schedule_id":"28e14207-c379-4ef8-9461-657de7927bbd","faculty":"Gerry Ritz R. Densing","subject_code":"CC 112 L","subject_name":"Computer Programming 1 (Lab)","section":"BSIS 1-A","room":"Computer Laboratory 2","day":"Thursday","start_time":"13:00:00","end_time":"15:00:00"},{"schedule_id":"41451ae6-c2e7-43f5-a9db-7956b4b2f1a1","faculty":"Gerry Ritz R. Densing","subject_code":"CC 112 L","subject_name":"Computer Programming 1 (Lab)","section":"BSIS 1-A","room":"Computer Laboratory 2","day":"Monday","start_time":"13:00:00","end_time":"15:00:00"},{"schedule_id":"d01a5e78-47d5-48b9-aa5c-df571d00ee4c","faculty":"Gerry Ritz R. Densing","subject_code":"CC 112 L","subject_name":"Computer Programming 1 (Lab)","section":"BSIS 1-A","room":"Computer Laboratory 2","day":"Tuesday","start_time":"13:00:00","end_time":"16:00:00"}]	\N
a36a47b5-6030-4fc0-b7eb-6dd251e84c6c	5694bc6e-69e8-4dfd-b78b-35af1289ad11	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 13:10:48+00	\N	\N	pending	\N	[{"schedule_id":"c1ac7966-cb6f-4faa-972d-77d5e0d0d96f","faculty":"Noreen B. Fuentes","subject_code":"P Elec 3","subject_name":"Enterprise Systems","section":"BSIS 4-A","room":"NICT-808","day":"Tuesday","start_time":"07:00:00","end_time":"12:00:00"},{"schedule_id":"322ca05e-404e-4638-9c3f-ece2f95d8996","faculty":"Noreen B. Fuentes","subject_code":"PC 318","subject_name":"Enterprise Architecture","section":"BSIS 3-A","room":"NICT-706","day":"Thursday","start_time":"13:00:00","end_time":"15:00:00"},{"schedule_id":"ba231c3b-b91d-421c-806a-ed6f3da49bcd","faculty":"Noreen B. Fuentes","subject_code":"PC 318","subject_name":"Enterprise Architecture","section":"BSIS 3-A","room":"NICT-706","day":"Friday","start_time":"13:00:00","end_time":"16:00:00"},{"schedule_id":"db3a2457-8ec0-4bca-89ce-8af54c52e1d3","faculty":"Noreen B. Fuentes","subject_code":"P Elec 3","subject_name":"Enterprise Systems","section":"BSIS 4-2","room":"NICT-809","day":"Tuesday","start_time":"16:00:00","end_time":"21:00:00"},{"schedule_id":"4c629b1a-e26e-476d-b20a-115ec2cbc4c0","faculty":"Noreen B. Fuentes","subject_code":"P Elec 3","subject_name":"Enterprise Systems","section":"BSIS 4-1","room":"NICT-809","day":"Wednesday","start_time":"16:00:00","end_time":"21:00:00"},{"schedule_id":"7fc84490-540a-4441-90ca-118c5d7a6158","faculty":"Noreen B. Fuentes","subject_code":"P Elec 3","subject_name":"Enterprise Systems","section":"BSIS 4-B","room":"NICT-808","day":"Wednesday","start_time":"07:00:00","end_time":"12:00:00"},{"schedule_id":"db9ccc46-6189-4630-ae6f-00912a17e828","faculty":"Noreen B. Fuentes","subject_code":"CC 111","subject_name":"Introduction to Computing","section":"BSIS 1-1","room":"NICT-809","day":"Thursday","start_time":"16:00:00","end_time":"21:00:00"}]	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d
034ef485-4ee9-4322-8bc2-e4821d6eb68d	5694bc6e-69e8-4dfd-b78b-35af1289ad11	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-10-31 13:31:45+00	\N	\N	pending	\N	[{"schedule_id":"d5d4df1d-f034-4da5-b72c-83f93cc49215","faculty":"Rizza P. Sayloon","subject_code":"CC 214 L","subject_name":"Data Structures and Algorithms (Lab)","section":"BSIS 2-A","room":"Computer Laboratory 2","day":"Thursday","start_time":"07:00:00","end_time":"12:00:00"},{"schedule_id":"5732476c-7557-4185-a926-e2232be20e7b","faculty":"Rizza P. Sayloon","subject_code":"CC 214 L","subject_name":"Data Structures and Algorithms (Lab)","section":"BSIS 2-A","room":"Computer Laboratory 2","day":"Monday","start_time":"09:00:00","end_time":"10:00:00"},{"schedule_id":"9325480d-67fc-4b9d-b3e3-8ec424a30672","faculty":"Rizza P. Sayloon","subject_code":"CC 214 L","subject_name":"Data Structures and Algorithms (Lab)","section":"BSIS 2-A","room":"Computer Laboratory 2","day":"Tuesday","start_time":"09:00:00","end_time":"10:00:00"},{"schedule_id":"16fe0338-396b-4360-8cfe-841819b4f538","faculty":"Rizza P. Sayloon","subject_code":"CC 214 L","subject_name":"Data Structures and Algorithms (Lab)","section":"BSIS 2-A","room":"Computer Laboratory 2","day":"Wednesday","start_time":"09:00:00","end_time":"10:00:00"},{"schedule_id":"92a47478-a2ca-4b41-b9b2-03dba3bf0220","faculty":"Rizza P. Sayloon","subject_code":"PC 214","subject_name":"Financial Management","section":"BSIS 2-A","room":"Smart Classroom","day":"Tuesday","start_time":"13:00:00","end_time":"16:00:00"},{"schedule_id":"4f8aa09e-f4f0-4b09-b7cd-60e1f79c4f5d","faculty":"Rizza P. Sayloon","subject_code":"CC 214 L","subject_name":"Data Structures and Algorithms (Lab)","section":"BSIS 2-A","room":"Computer Laboratory 2","day":"Friday","start_time":"09:00:00","end_time":"10:00:00"}]	\N
027fecd3-3b9b-4a9e-ade2-e5262e879d15	5694bc6e-69e8-4dfd-b78b-35af1289ad11	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-10-31 13:52:40+00	\N	\N	pending	\N	[{"schedule_id":"c1271548-de48-4177-b12e-6f0021f3dfb5","faculty":"Monkey D. Luffy","subject_code":"AP 4","subject_name":"Database Administration DBMS","section":"BSIS 3-B","room":"Computer Laboratory 1","day":"Monday","start_time":"17:30:00","end_time":"19:30:00"},{"schedule_id":"ad0d3507-b425-4d40-bf85-597700ff83c8","faculty":"Monkey D. Luffy","subject_code":"AP 4","subject_name":"Database Administration DBMS","section":"BSIS 3-B","room":"Computer Laboratory 1","day":"Thursday","start_time":"17:30:00","end_time":"19:30:00"},{"schedule_id":"978546d4-be2a-47f5-94ea-cdb4e3baad77","faculty":"Monkey D. Luffy","subject_code":"GEC-MMW","subject_name":"Mathematics in the Modern World","section":"BSIS 1-B","room":"Networking Laboratory","day":"Wednesday","start_time":"13:00:00","end_time":"15:00:00"},{"schedule_id":"c03c65dd-3f78-44f4-88b8-5314ffeca351","faculty":"Monkey D. Luffy","subject_code":"CC 111","subject_name":"Introduction to Computing","section":"BSIS 1-A","room":"Computer Laboratory 1","day":"Monday","start_time":"09:30:00","end_time":"10:30:00"}]	\N
\.


--
-- Data for Name: section; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.section (sec_id, sec_dept_id, sec_ay_id, sec_sem_id, sec_name, sec_year_level, sec_no_of_student, sec_max_capacity, sec_status, sec_created_at, sec_updated_at, sec_is_evening) FROM stdin;
bad18a12-2748-4334-982b-97c089235076	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-1A	1	25	40	active	2026-11-04 15:27:01+00	2026-11-04 15:27:03+00	f
cada9017-e27c-4762-b3eb-5e450bca500b	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-B	1	33	40	active	2026-09-30 08:22:29.298999+00	2026-09-30 08:22:29.298999+00	f
a5543bd6-54c7-4be9-8ced-11a78a23e691	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 2-A	2	32	40	active	2026-09-30 08:22:29.298999+00	2026-09-30 08:22:29.298999+00	f
077175b0-235b-4e70-933f-c53c6161188c	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 2-B	2	30	40	active	2026-09-30 08:22:29.298999+00	2026-09-30 08:22:29.298999+00	f
6336b405-e9be-4787-96d3-bd3089e48da9	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 3-A	3	29	40	active	2026-09-30 08:22:29.298999+00	2026-09-30 08:22:29.298999+00	f
cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 3-B	3	28	40	active	2026-09-30 08:22:29.298999+00	2026-09-30 08:22:29.298999+00	f
b39f2514-7cec-427c-b482-ad9acdf7db4f	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 4-A	4	27	40	active	2026-09-30 08:22:29.298999+00	2026-09-30 08:22:29.298999+00	f
a9326d77-b132-4a11-abc6-5889ef05ebb6	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 4-B	4	25	40	active	2026-09-30 08:22:29.298999+00	2026-09-30 08:22:29.298999+00	f
46b38b07-eb74-4ea7-a105-51779a082ea8	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-A	1	48	40	active	2026-09-30 08:22:29.298999+00	2026-10-01 03:24:20+00	f
d5a17999-4c46-4008-8a68-15c219866d25	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-A	1	40	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
e8e9b4c3-46a6-4134-b5d3-f5da754d1a92	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-B	1	38	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
12de1585-67d3-41dd-9d73-1f87d27715ff	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 2-A	2	35	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
573c1bc9-82da-4acb-85c1-b01b0b215803	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 2-B	2	36	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
1f91582c-9941-40dc-a99a-bd1d536c01d6	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 3-A	3	32	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
dde6347f-5b24-4e45-9b9a-9b24fc91ccb2	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 3-B	3	30	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
47cc4247-5e18-4672-afe7-6d02097c3252	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 4-A	4	28	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
5a2b8b7f-e6cc-4097-bfa7-b4544080b572	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 4-B	4	27	40	active	2026-10-01 06:13:40.308865+00	2026-10-01 06:13:40.308865+00	f
b5cbceae-f8a7-4c26-ab2b-e6d3a874ab56	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-1B	1	30	50	active	2026-10-26 05:49:18.050154+00	2026-10-26 05:49:18.050154+00	f
32c670ec-2065-4d2d-b409-51456a537b50	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-1A	1	30	50	active	2026-10-26 05:52:35.179492+00	2026-10-26 05:52:35.179492+00	f
00ce7ab3-99cf-4b89-a1e1-09771e599a45	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-2A	1	25	50	active	2026-10-26 05:53:42.265163+00	2026-10-26 05:53:42.265163+00	f
b453cf9f-6802-4b9b-b9f7-c9ae578563f6	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-2B	1	30	50	active	2026-10-26 05:51:44.384476+00	2026-10-26 05:51:44.384476+00	f
a4f150a1-97b0-4506-b2a5-c38e60c8c6ed	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-2A	1	25	40	active	2026-11-04 15:30:47+00	2026-11-04 15:30:48+00	f
21007b48-90f3-478d-9f17-863617c394d4	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 2-2	2	25	40	active	2026-11-04 15:37:08+00	2026-11-04 15:37:09+00	t
484bb3c0-4bad-4d9f-901a-cd6871570f72	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 2-2	2	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
487b76d1-9b5f-4959-aead-b6dcacbe9ea0	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 3-1	3	25	40	active	2026-11-04 15:37:46+00	2026-11-04 15:37:47+00	t
519ecca4-8cf0-4f8f-8136-d2f627d9bf03	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 2-1	2	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
52d55b72-a0b5-44d8-a3cf-cb61a2feedc1	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 4-2	4	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
5409f47c-a640-4593-951f-19cfc779af9a	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 3-1	3	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
6bd5e6b4-76be-4edb-90ee-f7cb6c55e2b4	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 4-1	4	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
6f77b080-7dd2-4a8a-ae35-90f666159b9d	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 4-1	4	25	40	active	2026-11-04 15:40:24+00	2026-11-04 15:40:25+00	t
03cfe1ca-5358-42f8-8975-b1d3b3c982ff	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-1	1	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 1-2	1	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	5694bc6e-69e8-4dfd-b78b-35af1289ad11	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIS 3-2	3	0	50	active	2026-11-04 12:51:53.982757+00	2026-11-04 12:51:53.982757+00	t
b43c2585-4f52-48d7-8e46-e05152983cb9	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-1	1	25	40	active	2026-11-04 15:21:29+00	2026-11-04 15:21:31+00	t
dc3aea16-1418-4612-97a8-cb387b54f6ca	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-2	1	25	40	active	2026-11-04 15:29:36+00	2026-11-04 15:29:37+00	t
7b1b465d-e27b-44d0-a592-43954d83ab54	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 2-1	2	25	40	active	2026-11-04 15:35:09+00	2026-11-04 15:35:11+00	t
773e1d3f-e751-4e81-8f57-ec1d43f852f5	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 3-2	3	25	40	active	2026-11-04 15:38:24+00	2026-11-04 15:38:25+00	t
a097a3e5-d720-42c0-8886-53409a140c68	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 4-2	4	25	40	active	2026-11-04 15:41:01+00	2026-11-04 15:41:03+00	t
e8317cc3-1cb0-4dab-b789-393914ef8d9f	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-1B	1	25	40	active	2026-11-04 15:28:39+00	2026-11-04 15:28:40+00	t
295ed94c-1601-4d8f-bb22-56b793f8a4cb	8de117bd-ed91-45dc-9ad4-f75eede0cb38	0e90a55c-51ba-4d90-868d-3b91051ce5a0	462c1f14-8433-47f1-8569-9daeedc758a5	BSIT 1-2B	1	25	40	active	2026-11-04 15:33:46+00	2026-11-04 15:33:47+00	t
\.


--
-- Data for Name: semester; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.semester (sem_id, sem_ay_id, sem_name, sem_start_date, sem_end_date, sem_is_active, sem_created_at) FROM stdin;
0d030558-166f-4f05-8a6d-093af4b1ffee	0e90a55c-51ba-4d90-868d-3b91051ce5a0	2nd Semester	2026-08-01	2026-12-20	f	2026-09-30 08:18:55.204565+00
462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	1st Semester	2026-08-01	2026-12-20	t	2026-09-30 08:18:55.204565+00
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: study_load; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.study_load (sl_id, sl_fac_id, sl_course_id, sl_sec_id, sl_sem_id, sl_assigned_by, sl_assigned_at, sl_status, sl_shift) FROM stdin;
51907221-56a1-457e-b56e-c5ecab92dce0	01a1a087-4c19-70da-8f2e-872c5804e5a1	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	46b38b07-eb74-4ea7-a105-51779a082ea8	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 13:46:17+00	draft	day
94d00935-754f-44ed-bdba-81a46d484f74	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	c55f8061-08b2-4728-9656-7216110e27c8	\N	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 14:31:47+00	draft	day
e850f153-6fac-4d6d-bf7b-badf837f1181	01a1a087-4c19-70da-8f2e-872c5804e5a1	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	00ce7ab3-99cf-4b89-a1e1-09771e599a45	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 16:18:50+00	draft	day
042b19a9-82d9-4706-aabb-73810b43e297	01a1a070-d483-73bd-a2a6-20329281ddab	a3d2f648-c2d3-4277-951c-8b33f6832b01	6336b405-e9be-4787-96d3-bd3089e48da9	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:48:56+00	draft	day
84e780bd-2926-43f0-8093-78faf4e570e7	01a1a070-d483-73bd-a2a6-20329281ddab	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	6336b405-e9be-4787-96d3-bd3089e48da9	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:50:55+00	draft	day
1b1a2a90-efe3-46af-b812-5bd57d04503d	01a1a070-d483-73bd-a2a6-20329281ddab	221d8268-bbc3-427d-8e18-3d6457d3c2fb	d5a17999-4c46-4008-8a68-15c219866d25	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 02:53:50+00	draft	day
3ae674ec-3fdc-4564-ae2c-559d6c3dd951	01a1a070-d483-73bd-a2a6-20329281ddab	a3d2f648-c2d3-4277-951c-8b33f6832b01	87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 03:24:20+00	draft	evening
75539ea8-2ae8-4201-bfc0-347c15e7c558	01a1a070-d483-73bd-a2a6-20329281ddab	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-05 03:56:31+00	draft	evening
f6f4163f-4122-49db-8d8c-652e9128a183	01a1a070-d483-73bd-a2a6-20329281ddab	221d8268-bbc3-427d-8e18-3d6457d3c2fb	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 02:43:26+00	draft	evening
917d8648-c78e-466d-b78b-efe60afde918	01a1a048-1a3d-708c-bc17-93d208cbc837	9751862a-6d6a-417e-9834-26e920dfebba	03cfe1ca-5358-42f8-8975-b1d3b3c982ff	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 02:50:34+00	draft	evening
19aaf95a-7e0c-4b59-9c0d-8f77993aaa28	ac6adea7-6397-426f-9fa1-b9e51749b665	221d8268-bbc3-427d-8e18-3d6457d3c2fb	32c670ec-2065-4d2d-b409-51456a537b50	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-10-30 07:03:16+00	draft	evening
6c1389dd-1b37-4093-b935-904ca5fa64ed	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	b39f2514-7cec-427c-b482-ad9acdf7db4f	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 10:17:59+00	draft	day
7f88d2fe-83b1-479d-af16-671524f07acf	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	a9326d77-b132-4a11-abc6-5889ef05ebb6	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 10:35:59+00	draft	day
9c235c41-2503-488d-a202-5cef1b225604	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	15f66c86-9bf9-42ef-bca3-4b10320842dd	6336b405-e9be-4787-96d3-bd3089e48da9	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 11:07:09+00	draft	day
6b5a248d-d383-4986-8dc2-3de8a788d456	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	52d55b72-a0b5-44d8-a3cf-cb61a2feedc1	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 12:53:34+00	draft	evening
08348508-98b6-4e8d-ae26-67c110c7e747	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	2c5be148-1d93-4b4d-a08d-3c49df7613e2	6bd5e6b4-76be-4edb-90ee-f7cb6c55e2b4	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 12:53:57+00	draft	evening
889bf404-3024-4943-99e6-dc5b0e8e12e5	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	221d8268-bbc3-427d-8e18-3d6457d3c2fb	03cfe1ca-5358-42f8-8975-b1d3b3c982ff	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-04 12:55:10+00	draft	evening
8f218c48-8125-4346-8d8a-0ec29d1f6013	01a11004-1aa5-7125-9178-590a1a432970	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:05:18+00	draft	evening
fd775898-f015-4a74-add8-7453dc77d994	01a11004-1aa5-7125-9178-590a1a432970	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:06:17+00	draft	evening
4087e488-4d41-433a-ab0c-05c546341331	01a1a03e-600f-72b2-be2b-199e54a81312	8cd451f2-cca7-4a4e-b7d1-ef1e541b60ef	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:07:38+00	draft	evening
df447d85-a228-40fb-89d6-f3639eb0c092	01a1a03e-600f-72b2-be2b-199e54a81312	8bab364d-dbe5-42d1-bd60-c1b6039bc861	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:09:41+00	draft	evening
9558fc9b-480f-462d-962e-5d07f0695331	01a1a048-1a3d-708c-bc17-93d208cbc837	9751862a-6d6a-417e-9834-26e920dfebba	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:14:01+00	draft	evening
aabcd22f-265c-420f-8f32-499bce0786c4	01a1a03e-600f-72b2-be2b-199e54a81312	d601231f-8bf9-44c1-979d-47b0862983b2	88f0fb6b-6483-4f3c-9f5b-55ad9d3c124d	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:18:16+00	draft	evening
746ebb64-064f-4dca-ba32-2acdf3f4e587	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	efa79181-f225-4ad9-b5ae-1b28bb2dfdb3	6bd5e6b4-76be-4edb-90ee-f7cb6c55e2b4	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-06 03:22:05+00	draft	evening
d4d81cb7-dd67-4530-a8bc-70a7e46daff9	01a1a08a-6633-71da-a16e-269a62efff07	a3d2f648-c2d3-4277-951c-8b33f6832b01	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:31:42+00	draft	day
0cd325fa-4660-4fba-a8d0-505780240ebc	01a1a08a-6633-71da-a16e-269a62efff07	129760c4-d92f-4695-a7e3-71e8dcac525f	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:33:05+00	draft	day
2c8c1f75-78ee-41f2-a9ca-89f572801ab3	01a1a08a-6633-71da-a16e-269a62efff07	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	cac58350-f856-41ad-bdbc-7fe2e2f8ccd1	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 11:34:06+00	draft	day
f9fb035e-2d5d-43d6-ad6d-268e241a46f7	01a1a08a-6633-71da-a16e-269a62efff07	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	b5cbceae-f8a7-4c26-ab2b-e6d3a874ab56	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:06:25+00	draft	evening
f8f35617-9adc-4749-8056-f48b9b6c17cf	01a1a08a-6633-71da-a16e-269a62efff07	ecf095e8-8be4-4fa7-9486-14f3a5ef310c	b453cf9f-6802-4b9b-b9f7-c9ae578563f6	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:08:42+00	draft	evening
4faacb79-0e97-405f-8343-5561b6d03ff6	01a1a08a-6633-71da-a16e-269a62efff07	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	b5cbceae-f8a7-4c26-ab2b-e6d3a874ab56	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:09:32+00	draft	evening
5d52c857-93f4-4225-8851-24e10c5a5d5e	01a1a08a-6633-71da-a16e-269a62efff07	9c20c4c7-2a9d-4f3e-9527-3f1adb6ce097	b453cf9f-6802-4b9b-b9f7-c9ae578563f6	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 12:31:42+00	draft	evening
ad1eb7d8-cc2c-4103-954b-51d11fd41743	01a1a08a-6633-71da-a16e-269a62efff07	129760c4-d92f-4695-a7e3-71e8dcac525f	87f891a2-e64d-4fe6-bb95-b7a8ba63d5d2	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 13:02:51+00	draft	evening
8f8e3f8c-f522-482a-ae61-55a7f2a183ba	01a1a08a-6633-71da-a16e-269a62efff07	c55f8061-08b2-4728-9656-7216110e27c8	\N	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 17:38:33+00	draft	day
d2f8e54d-c165-4705-a950-ad1a2f950baa	01a1baec-8076-7080-a841-079a4c219fe9	9751862a-6d6a-417e-9834-26e920dfebba	46b38b07-eb74-4ea7-a105-51779a082ea8	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 17:45:17+00	draft	day
882337c6-ae8d-4abe-97aa-153c6dff2fab	01a1baec-8076-7080-a841-079a4c219fe9	56ff4a31-5c3c-4a4f-9c9e-84f751f8e033	5409f47c-a640-4593-951f-19cfc779af9a	462c1f14-8433-47f1-8569-9daeedc758a5	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-11-10 17:47:13+00	draft	evening
\.


--
-- Data for Name: system_backup; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_backup (bkp_id, bkp_file_path, bkp_status, bkp_error_message, bkp_ran_at) FROM stdin;
01a0dd82-ab7c-70e0-9762-a18c4f615839	C:\\xampp\\htdocs\\Skedyul\\storage\\app/backups/backup_2026-09-26_113812.sql	success	\N	2026-09-26 11:38:39
\.


--
-- Data for Name: system_setting; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_setting (sset_id, sset_key, sset_value, sset_updated_by, sset_updated_at) FROM stdin;
a722401a-8e82-4f3f-b770-628dc3366f31	current_academic_year	2025-2026	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-07-15 15:06:05.210873+00
4156f284-6c08-46a6-a3e9-34234f9bc07a	current_semester	First Semester	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-07-15 15:06:05.210873+00
84fcd59e-e0b4-4464-938a-9f383c22c14d	max_faculty_load	30	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-07-15 15:06:05.210873+00
02d20d74-e8d3-4ce2-b729-fef54e25bc4e	allow_schedule_edit	true	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-07-15 15:06:05.210873+00
07c8f870-3d5a-4dd3-b19f-d4a6fe348f48	system_name	SKEDYUL	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-07-15 15:06:05.210873+00
01a0ddaf-22e9-7060-b827-5ee1f9ee58c5	notif_system_backups	0	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-09-26 12:27:18.450108+00
01a0ddb0-eadb-72dc-ae70-71990f3d5453	notif_login_activity	0	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-09-26 12:29:15.176931+00
01a0ddae-f4ef-7266-8330-54b7af58839c	notif_new_user_registration	1	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-09-26 12:27:06.769017+00
01a0ddaf-10b3-70a7-9b0c-e36039d6339d	notif_faculty_overload	1	270ad7b7-7c4d-4823-b314-816b91773cb9	2026-09-26 12:27:13.784826+00
01a11718-70f5-714e-808f-5646cd98dfac	chair_notif_faculty_overload	0	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-10-07 16:00:40.73044+00
01a1171e-1d5b-71ff-b9fa-4524b6b0b1dc	chair_notif_user_335c70b6-c19a-4214-8b7b-fb771a2c9e02_chair_notif_faculty_overload	1	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-10-07 16:06:52.554497+00
01a0e167-ae29-71a8-aa9b-fd8a4519d308	usr_session_timeout_minutes	15	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-09-27 05:47:44.587843+00
01a0e167-af41-7148-aa6f-d324a63f2854	usr_max_login_attempts	10	335c70b6-c19a-4214-8b7b-fb771a2c9e02	2026-09-27 05:47:44.863851+00
\.


--
-- Data for Name: token_blacklist; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.token_blacklist (tbl_id, tbl_usr_id, tbl_token, tbl_revoked_at) FROM stdin;
07547411-5972-406f-b756-e5dedf0e4189	270ad7b7-7c4d-4823-b314-816b91773cb9	token_admin_001	2026-07-15 15:05:47.220445+00
\.


--
-- Data for Name: webauthn_credentials; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.webauthn_credentials (id, usr_id, credential_id, public_key, sign_count, transports, device_name, created_at, last_used_at, credential_record) FROM stdin;
01a176f2-4224-7056-82c8-dfafe8d8ea36	335c70b6-c19a-4214-8b7b-fb771a2c9e02	ihDOogK2fHuUamFAKhR9rQ	pQECAyYgASFYIFtNIIYNqCUyIEAEyKkWV6kBoFAyBOmA111WOVUP_3cOIlggrGWwNjQP2arsNL1GWfgia6a7qvPQXCqWfovla03HrPc	0	["hybrid", "internal"]	This device	2026-10-26 06:42:30.554367+00	2026-10-26 06:43:39+00	{"type": "public-key", "aaguid": "ea9b8d66-4d01-1d21-3ce4-b6b48cb575d4", "counter": 0, "trustPath": [], "transports": ["hybrid", "internal"], "userHandle": "MzM1YzcwYjZjMTlhNDIxNDhiN2JmYjc3MWEyYzllMDI", "backupStatus": true, "uvInitialized": true, "backupEligible": true, "attestationType": "none", "credentialPublicKey": "pQECAyYgASFYIFtNIIYNqCUyIEAEyKkWV6kBoFAyBOmA111WOVUP_3cOIlggrGWwNjQP2arsNL1GWfgia6a7qvPQXCqWfovla03HrPc", "publicKeyCredentialId": "ihDOogK2fHuUamFAKhR9rQ"}
\.


--
-- Data for Name: workload; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.workload (wl_id, wl_fac_id, wl_sem_id, wl_ay_id, wl_type, wl_total_hours, wl_created_at, wl_updated_at) FROM stdin;
78952102-e989-4513-9c6f-e69f421b0322	01a1a087-4c19-70da-8f2e-872c5804e5a1	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	11.0	2026-11-04 13:46:28+00	2026-11-04 16:18:51+00
d8d76b94-6a26-44cf-b6a8-255e1a5fd1ba	01a1a08a-6633-71da-a16e-269a62efff07	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	43.0	2026-11-05 14:46:45+00	2026-11-10 17:41:16+00
e331c9f6-12bc-4b06-b171-e15307f93406	01a1baec-8076-7080-a841-079a4c219fe9	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	12.0	2026-11-10 17:45:19+00	2026-11-10 17:47:15+00
5d5e679e-f5dd-408d-bd18-ff04fb80e8dd	01a11004-1aa5-7125-9178-590a1a432970	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	11.0	2026-10-06 07:39:40+00	2026-11-06 03:06:18+00
049fe9a5-aa13-4d37-bc17-bc91a084344e	ac6adea7-6397-426f-9fa1-b9e51749b665	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	5.0	2026-10-30 07:03:19+00	2026-10-30 07:03:19+00
375c189e-9591-4cf1-87d3-fe43ed63f8d6	01a1a048-1a3d-708c-bc17-93d208cbc837	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	6.0	2026-11-06 02:50:35+00	2026-11-06 03:16:54+00
6fe59ff4-76ef-415c-8655-666210b5dbbe	01a1a03e-600f-72b2-be2b-199e54a81312	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	8.0	2026-11-04 11:14:56+00	2026-11-06 03:18:17+00
d6dbf77b-8bf5-4a7c-9f65-74f31647f01b	33a4cf3b-17f5-4d74-a70f-0ec5f0b2fd1d	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	33.0	2026-10-07 05:38:45+00	2026-11-06 03:22:06+00
bb2bea3d-293a-4af6-9134-5d636aaaf1e0	01a1a070-d483-73bd-a2a6-20329281ddab	462c1f14-8433-47f1-8569-9daeedc758a5	0e90a55c-51ba-4d90-868d-3b91051ce5a0	regular	32.0	2026-11-04 13:13:23+00	2026-11-08 16:02:19+00
\.


--
-- Data for Name: schema_migrations; Type: TABLE DATA; Schema: realtime; Owner: supabase_admin
--

COPY realtime.schema_migrations (version, inserted_at) FROM stdin;
20211116024918	2026-09-24 12:22:22
20211116045059	2026-09-24 12:22:22
20211116050929	2026-09-24 12:22:22
20211116051442	2026-09-24 12:22:22
20211116212300	2026-09-24 12:22:22
20211116213355	2026-09-24 12:22:22
20211116213934	2026-09-24 12:22:22
20211116214523	2026-09-24 12:22:22
20211122062447	2026-09-24 12:22:22
20211124070109	2026-09-24 12:22:22
20211202204204	2026-09-24 12:22:22
20211202204605	2026-09-24 12:22:22
20211210212804	2026-09-24 12:22:22
20211228014915	2026-09-24 12:22:22
20220107221237	2026-09-24 12:22:22
20220228202821	2026-09-24 12:22:22
20220312004840	2026-09-24 12:22:22
20220603231003	2026-09-24 12:22:22
20220603232444	2026-09-24 12:22:22
20220615214548	2026-09-24 12:22:22
20220712093339	2026-09-24 12:22:22
20220908172859	2026-09-24 12:22:22
20220916233421	2026-09-24 12:22:22
20230119133233	2026-09-24 12:22:22
20230128025114	2026-09-24 12:22:22
20230128025212	2026-09-24 12:22:22
20230227211149	2026-09-24 12:22:22
20230228184745	2026-09-24 12:22:22
20230308225145	2026-09-24 12:22:22
20230328144023	2026-09-24 12:22:22
20231018144023	2026-09-24 12:22:22
20231204144023	2026-09-24 12:22:22
20231204144024	2026-09-24 12:22:22
20231204144025	2026-09-24 12:22:22
20240108234812	2026-09-24 12:22:22
20240109165339	2026-09-24 12:22:22
20240227174441	2026-09-24 12:22:22
20240311171622	2026-09-24 12:22:22
20240321100241	2026-09-24 12:22:22
20240401105812	2026-09-24 12:22:22
20240418121054	2026-09-24 12:22:22
20240523004032	2026-09-24 12:22:22
20240618124746	2026-09-24 12:22:22
20240801235015	2026-09-24 12:22:22
20240805133720	2026-09-24 12:22:22
20240827160934	2026-09-24 12:22:22
20240919163303	2026-09-24 12:22:22
20240919163305	2026-09-24 12:22:22
20241019105805	2026-09-24 12:22:22
20241030150047	2026-09-24 12:22:22
20241108114728	2026-09-24 12:22:22
20241121104152	2026-09-24 12:22:22
20241130184212	2026-09-24 12:22:22
20241220035512	2026-09-24 12:22:22
20241220123912	2026-09-24 12:22:22
20241224161212	2026-09-24 12:22:22
20250107150512	2026-09-24 12:22:22
20250110162412	2026-09-24 12:22:22
20250123174212	2026-09-24 12:22:22
20250128220012	2026-09-24 12:22:22
20250506224012	2026-09-24 12:22:22
20250523164012	2026-09-24 12:22:22
20250714121412	2026-09-24 12:22:22
20250905041441	2026-09-24 12:22:22
20251103001201	2026-09-24 12:22:22
20251120212548	2026-09-24 12:22:22
20251120215549	2026-09-24 12:22:22
20260218120000	2026-09-24 12:22:22
20260326120000	2026-09-24 12:22:22
20260514120000	2026-09-24 12:22:22
20260527120000	2026-09-24 12:22:22
20260528120000	2026-09-24 12:22:22
20260603120000	2026-09-24 12:22:22
20260605120000	2026-09-24 12:22:22
20260606110000	2026-09-24 12:22:22
20260616120000	2026-09-24 12:22:22
20260624120000	2026-09-24 12:22:22
20260626120000	2026-09-24 12:22:22
20260706120000	2026-09-24 12:22:22
20260707120000	2026-09-24 12:22:22
20260709120000	2026-09-24 12:22:22
20260714120000	2026-09-24 12:22:22
20260827120000	2026-09-24 12:22:22
20260914120000	2026-09-24 12:22:22
20260916120000	2026-09-24 12:22:22
20260922120000	2026-09-24 12:22:22
20260925120000	2026-09-30 05:13:43
20260928120000	2026-10-05 07:53:57
20261002120000	2026-10-08 05:13:56
20261008120000	2026-10-22 10:36:57
20261009120000	2026-10-22 10:36:57
20261007120000	2026-11-09 08:34:21
20261022120000	2026-11-09 08:34:21
20261027120000	2026-11-09 08:34:21
20261029120000	2026-11-09 08:34:21
20261103130000	2026-11-09 08:34:21
\.


--
-- Data for Name: subscription; Type: TABLE DATA; Schema: realtime; Owner: supabase_realtime_admin
--

COPY realtime.subscription (id, subscription_id, entity, filters, claims, created_at, action_filter, selected_columns) FROM stdin;
\.


--
-- Data for Name: bucket_lifecycle_states; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.bucket_lifecycle_states (bucket_id, generation, next_run_at, claim_id, claim_until, continuation, last_started_at, last_completed_at, last_success_at, last_result, failure_count, last_error, updated_at) FROM stdin;
\.


--
-- Data for Name: buckets; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.buckets (id, name, owner, created_at, updated_at, public, avif_autodetection, file_size_limit, allowed_mime_types, owner_id, type, versioning_status, lifecycle_configuration, lifecycle_configuration_generation) FROM stdin;
\.


--
-- Data for Name: buckets_analytics; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.buckets_analytics (name, type, format, created_at, updated_at, id, deleted_at) FROM stdin;
\.


--
-- Data for Name: buckets_vectors; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.buckets_vectors (id, type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lifecycle_abandoned_artifacts; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.lifecycle_abandoned_artifacts (bucket_id, key, name, version, attempted_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.migrations (id, name, hash, executed_at) FROM stdin;
0	create-migrations-table	e18db593bcde2aca2a408c4d1100f6abba2195df	2026-09-24 12:22:22.560544
1	initialmigration	6ab16121fbaa08bbd11b712d05f358f9b555d777	2026-09-24 12:22:22.569833
3	pathtoken-column	2cb1b0004b817b29d5b0a971af16bafeede4b70d	2026-09-24 12:22:22.5935
4	add-migrations-rls	427c5b63fe1c5937495d9c635c263ee7a5905058	2026-09-24 12:22:22.611309
5	add-size-functions	79e081a1455b63666c1294a440f8ad4b1e6a7f84	2026-09-24 12:22:22.616447
6	change-column-name-in-get-size	ded78e2f1b5d7e616117897e6443a925965b30d2	2026-09-24 12:22:22.622283
7	add-rls-to-buckets	e7e7f86adbc51049f341dfe8d30256c1abca17aa	2026-09-24 12:22:22.627792
8	add-public-to-buckets	fd670db39ed65f9d08b01db09d6202503ca2bab3	2026-09-24 12:22:22.632483
9	fix-search-function	af597a1b590c70519b464a4ab3be54490712796b	2026-09-24 12:22:22.638174
10	search-files-search-function	b595f05e92f7e91211af1bbfe9c6a13bb3391e16	2026-09-24 12:22:22.643578
11	add-trigger-to-auto-update-updated_at-column	7425bdb14366d1739fa8a18c83100636d74dcaa2	2026-09-24 12:22:22.650591
12	add-automatic-avif-detection-flag	8e92e1266eb29518b6a4c5313ab8f29dd0d08df9	2026-09-24 12:22:22.656384
13	add-bucket-custom-limits	cce962054138135cd9a8c4bcd531598684b25e7d	2026-09-24 12:22:22.663563
14	use-bytes-for-max-size	941c41b346f9802b411f06f30e972ad4744dad27	2026-09-24 12:22:22.669244
15	add-can-insert-object-function	934146bc38ead475f4ef4b555c524ee5d66799e5	2026-09-24 12:22:22.693712
16	add-version	76debf38d3fd07dcfc747ca49096457d95b1221b	2026-09-24 12:22:22.700433
17	drop-owner-foreign-key	f1cbb288f1b7a4c1eb8c38504b80ae2a0153d101	2026-09-24 12:22:22.705884
18	add_owner_id_column_deprecate_owner	e7a511b379110b08e2f214be852c35414749fe66	2026-09-24 12:22:22.711095
19	alter-default-value-objects-id	02e5e22a78626187e00d173dc45f58fa66a4f043	2026-09-24 12:22:22.719156
20	list-objects-with-delimiter	cd694ae708e51ba82bf012bba00caf4f3b6393b7	2026-09-24 12:22:22.724155
21	s3-multipart-uploads	8c804d4a566c40cd1e4cc5b3725a664a9303657f	2026-09-24 12:22:22.732025
22	s3-multipart-uploads-big-ints	9737dc258d2397953c9953d9b86920b8be0cdb73	2026-09-24 12:22:22.751394
23	optimize-search-function	9d7e604cddc4b56a5422dc68c9313f4a1b6f132c	2026-09-24 12:22:22.787993
24	operation-function	8312e37c2bf9e76bbe841aa5fda889206d2bf8aa	2026-09-24 12:22:22.793787
25	custom-metadata	d974c6057c3db1c1f847afa0e291e6165693b990	2026-09-24 12:22:22.799852
26	objects-prefixes	215cabcb7f78121892a5a2037a09fedf9a1ae322	2026-09-24 12:22:22.806688
27	search-v2	859ba38092ac96eb3964d83bf53ccc0b141663a6	2026-09-24 12:22:22.811724
28	object-bucket-name-sorting	c73a2b5b5d4041e39705814fd3a1b95502d38ce4	2026-09-24 12:22:22.816575
29	create-prefixes	ad2c1207f76703d11a9f9007f821620017a66c21	2026-09-24 12:22:22.821822
30	update-object-levels	2be814ff05c8252fdfdc7cfb4b7f5c7e17f0bed6	2026-09-24 12:22:22.82643
31	objects-level-index	b40367c14c3440ec75f19bbce2d71e914ddd3da0	2026-09-24 12:22:22.846313
32	backward-compatible-index-on-objects	e0c37182b0f7aee3efd823298fb3c76f1042c0f7	2026-09-24 12:22:22.854503
33	backward-compatible-index-on-prefixes	b480e99ed951e0900f033ec4eb34b5bdcb4e3d49	2026-09-24 12:22:22.859735
34	optimize-search-function-v1	ca80a3dc7bfef894df17108785ce29a7fc8ee456	2026-09-24 12:22:22.864992
35	add-insert-trigger-prefixes	458fe0ffd07ec53f5e3ce9df51bfdf4861929ccc	2026-09-24 12:22:22.869682
36	optimise-existing-functions	6ae5fca6af5c55abe95369cd4f93985d1814ca8f	2026-09-24 12:22:22.874194
37	add-bucket-name-length-trigger	3944135b4e3e8b22d6d4cbb568fe3b0b51df15c1	2026-09-24 12:22:22.878724
38	iceberg-catalog-flag-on-buckets	02716b81ceec9705aed84aa1501657095b32e5c5	2026-09-24 12:22:22.885291
39	add-search-v2-sort-support	6706c5f2928846abee18461279799ad12b279b78	2026-09-24 12:22:22.898413
40	fix-prefix-race-conditions-optimized	7ad69982ae2d372b21f48fc4829ae9752c518f6b	2026-09-24 12:22:22.902775
41	add-object-level-update-trigger	07fcf1a22165849b7a029deed059ffcde08d1ae0	2026-09-24 12:22:22.907251
42	rollback-prefix-triggers	771479077764adc09e2ea2043eb627503c034cd4	2026-09-24 12:22:22.911763
43	fix-object-level	84b35d6caca9d937478ad8a797491f38b8c2979f	2026-09-24 12:22:22.916485
44	vector-bucket-type	99c20c0ffd52bb1ff1f32fb992f3b351e3ef8fb3	2026-09-24 12:22:22.921155
45	vector-buckets	049e27196d77a7cb76497a85afae669d8b230953	2026-09-24 12:22:22.930497
46	buckets-objects-grants	fedeb96d60fefd8e02ab3ded9fbde05632f84aed	2026-09-24 12:22:22.944588
47	iceberg-table-metadata	649df56855c24d8b36dd4cc1aeb8251aa9ad42c2	2026-09-24 12:22:22.951153
48	iceberg-catalog-ids	e0e8b460c609b9999ccd0df9ad14294613eed939	2026-09-24 12:22:22.957696
49	buckets-objects-grants-postgres	072b1195d0d5a2f888af6b2302a1938dd94b8b3d	2026-09-24 12:22:22.98118
50	search-v2-optimised	6323ac4f850aa14e7387eb32102869578b5bd478	2026-09-24 12:22:22.988509
51	index-backward-compatible-search	2ee395d433f76e38bcd3856debaf6e0e5b674011	2026-09-24 12:22:23.276644
52	drop-not-used-indexes-and-functions	5cc44c8696749ac11dd0dc37f2a3802075f3a171	2026-09-24 12:22:23.278763
53	drop-index-lower-name	d0cb18777d9e2a98ebe0bc5cc7a42e57ebe41854	2026-09-24 12:22:23.290723
54	drop-index-object-level	6289e048b1472da17c31a7eba1ded625a6457e67	2026-09-24 12:22:23.293822
55	prevent-direct-deletes	262a4798d5e0f2e7c8970232e03ce8be695d5819	2026-09-24 12:22:23.296116
56	fix-optimized-search-function	b823ed1e418101032fa01374edc9a436e54e3ed4	2026-09-24 12:22:23.303234
57	s3-multipart-uploads-metadata	f127886e00d1b374fadbc7c6b31e09336aad5287	2026-09-24 12:22:23.310921
58	operation-ergonomics	00ca5d483b3fe0d522133d9002ccc5df98365120	2026-09-24 12:22:23.315914
59	drop-unused-functions	38456f13e39691c2bbb4b5151d0d1cdbabd4a8c4	2026-09-24 12:22:23.32224
60	optimize-existing-functions-again	db35e1c91a9201e59f4fef8d972c2f277d68b157	2026-09-24 12:22:23.327933
61	mark-filename-immutable	fe0096517ae9d60aaec1d110172ba9036dc66bb7	2026-09-24 12:22:23.334979
63	fix-search-name-relative-to-prefix	c7485e417624f795ce8bb2da21927f48e088904d	2026-09-24 12:22:23.349592
64	fix-search-by-timestamp-sqli	0af424ecd388a39bb1645184b222185a12149675	2026-09-24 12:22:23.357033
65	objects-key-version-index	da319c4b89ba800ce795d1b699f3a70675138058	2026-09-24 12:22:23.370336
66	objects-current-version-index	191466c93aa2c46a00e36505577c5fcab8d7cb4b	2026-09-24 12:22:23.378596
67	objects-null-version-index	15bfe8c35b66642b6c78ba60060fa8793bd2207a	2026-09-24 12:22:23.391561
68	bucket-lifecycle-configuration	3c08f6f889922f399519722a932b51007c11bebc	2026-09-24 12:22:23.394549
69	validate-bucket-lifecycle-constraints	4febacaaaa0e61e2b783bef081fe03a287e65eb3	2026-09-24 12:22:23.408421
70	list-objects-with-versions	5c17c3777616cd8d7b18b82835525fa3205af57b	2026-09-24 12:22:23.419337
71	objects-delete-marker-index	6d14858e66c66f8d6accf8a2630aefd1527fddba	2026-09-24 12:22:23.444767
72	drop-bucketid-objname-index	302beb09e1b469d7d4db19566f2389d280b64aa3	2026-09-24 12:22:23.451403
2	storage-schema	cdc19a79b298ce37cc730487821a1a7b06b98105	2026-09-24 12:22:22.57477
73	revoke-grants-to-unused-operations	ba9244051be09f22f714fc0170a2539f8b5f0e68	2026-10-14 05:28:20.578083
62	object-versioning-core	c6567e688f8c4a76887e7c1b117b2d58f4feccba	2026-09-24 12:22:23.341952
74	unlock-object-versioning	dacb58ecb37678de855505b3e1c5aec0d5df7894	2026-11-03 01:19:02.518565
75	enforce-bucket-lifecycle-update-role	8454a556116c985db857d3b99de94a31a68ad273	2026-11-04 05:07:27.598216
76	noncurrent-lifecycle	052cbe11956fd14b85861fc19537c0303b73010e	2026-11-04 05:07:27.622268
77	noncurrent-lifecycle-archived-order-index	d68b03438a987aa1324a0fca68f7078cb2ffb849	2026-11-04 05:07:27.826271
78	noncurrent-lifecycle-archived-due-index	c82ba0e577ec5c3496b1077ff385ce4645497698	2026-11-04 05:07:27.836306
79	lifecycle-writer-coordination	3b38458424d2099e6b2e3cb4233590060f527fe1	2026-11-04 05:07:27.83845
\.


--
-- Data for Name: objects; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.objects (id, bucket_id, name, owner, created_at, updated_at, last_accessed_at, metadata, version, owner_id, user_metadata, archived_at, is_delete_marker, is_versioned) FROM stdin;
\.


--
-- Data for Name: s3_multipart_uploads; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.s3_multipart_uploads (id, in_progress_size, upload_signature, bucket_id, key, version, owner_id, created_at, user_metadata, metadata) FROM stdin;
\.


--
-- Data for Name: s3_multipart_uploads_parts; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.s3_multipart_uploads_parts (id, upload_id, size, part_number, bucket_id, key, etag, owner_id, version, created_at) FROM stdin;
\.


--
-- Data for Name: vector_indexes; Type: TABLE DATA; Schema: storage; Owner: supabase_storage_admin
--

COPY storage.vector_indexes (id, name, bucket_id, data_type, dimension, distance_metric, metadata_configuration, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: secrets; Type: TABLE DATA; Schema: vault; Owner: supabase_admin
--

COPY vault.secrets (id, name, description, secret, key_id, nonce, created_at, updated_at) FROM stdin;
\.


--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: supabase_auth_admin
--

SELECT pg_catalog.setval('auth.refresh_tokens_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 31, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 19, true);


--
-- Name: subscription_id_seq; Type: SEQUENCE SET; Schema: realtime; Owner: supabase_realtime_admin
--

SELECT pg_catalog.setval('realtime.subscription_id_seq', 1, false);


--
-- Name: mfa_amr_claims amr_id_pk; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_amr_claims
    ADD CONSTRAINT amr_id_pk PRIMARY KEY (id);


--
-- Name: audit_log_entries audit_log_entries_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.audit_log_entries
    ADD CONSTRAINT audit_log_entries_pkey PRIMARY KEY (id);


--
-- Name: custom_oauth_providers custom_oauth_providers_identifier_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.custom_oauth_providers
    ADD CONSTRAINT custom_oauth_providers_identifier_key UNIQUE (identifier);


--
-- Name: custom_oauth_providers custom_oauth_providers_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.custom_oauth_providers
    ADD CONSTRAINT custom_oauth_providers_pkey PRIMARY KEY (id);


--
-- Name: flow_state flow_state_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.flow_state
    ADD CONSTRAINT flow_state_pkey PRIMARY KEY (id);


--
-- Name: identities identities_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.identities
    ADD CONSTRAINT identities_pkey PRIMARY KEY (id);


--
-- Name: identities identities_provider_id_provider_unique; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.identities
    ADD CONSTRAINT identities_provider_id_provider_unique UNIQUE (provider_id, provider);


--
-- Name: instances instances_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.instances
    ADD CONSTRAINT instances_pkey PRIMARY KEY (id);


--
-- Name: mfa_amr_claims mfa_amr_claims_session_id_authentication_method_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_amr_claims
    ADD CONSTRAINT mfa_amr_claims_session_id_authentication_method_pkey UNIQUE (session_id, authentication_method);


--
-- Name: mfa_challenges mfa_challenges_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_challenges
    ADD CONSTRAINT mfa_challenges_pkey PRIMARY KEY (id);


--
-- Name: mfa_factors mfa_factors_last_challenged_at_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_factors
    ADD CONSTRAINT mfa_factors_last_challenged_at_key UNIQUE (last_challenged_at);


--
-- Name: mfa_factors mfa_factors_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_factors
    ADD CONSTRAINT mfa_factors_pkey PRIMARY KEY (id);


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_mfa_factor_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_mfa_factor_id_key UNIQUE (mfa_factor_id);


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_pkey PRIMARY KEY (id);


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_user_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_user_id_key UNIQUE (user_id);


--
-- Name: mfa_recovery_codes mfa_recovery_codes_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_codes
    ADD CONSTRAINT mfa_recovery_codes_pkey PRIMARY KEY (id);


--
-- Name: oauth_authorizations oauth_authorizations_authorization_code_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_authorization_code_key UNIQUE (authorization_code);


--
-- Name: oauth_authorizations oauth_authorizations_authorization_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_authorization_id_key UNIQUE (authorization_id);


--
-- Name: oauth_authorizations oauth_authorizations_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_pkey PRIMARY KEY (id);


--
-- Name: oauth_client_states oauth_client_states_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_client_states
    ADD CONSTRAINT oauth_client_states_pkey PRIMARY KEY (id);


--
-- Name: oauth_clients oauth_clients_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_clients
    ADD CONSTRAINT oauth_clients_pkey PRIMARY KEY (id);


--
-- Name: oauth_consents oauth_consents_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_pkey PRIMARY KEY (id);


--
-- Name: oauth_consents oauth_consents_user_client_unique; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_user_client_unique UNIQUE (user_id, client_id);


--
-- Name: one_time_tokens one_time_tokens_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.one_time_tokens
    ADD CONSTRAINT one_time_tokens_pkey PRIMARY KEY (id);


--
-- Name: refresh_tokens refresh_tokens_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens
    ADD CONSTRAINT refresh_tokens_pkey PRIMARY KEY (id);


--
-- Name: refresh_tokens refresh_tokens_token_unique; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens
    ADD CONSTRAINT refresh_tokens_token_unique UNIQUE (token);


--
-- Name: saml_providers saml_providers_entity_id_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_providers
    ADD CONSTRAINT saml_providers_entity_id_key UNIQUE (entity_id);


--
-- Name: saml_providers saml_providers_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_providers
    ADD CONSTRAINT saml_providers_pkey PRIMARY KEY (id);


--
-- Name: saml_relay_states saml_relay_states_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_relay_states
    ADD CONSTRAINT saml_relay_states_pkey PRIMARY KEY (id);


--
-- Name: schema_migrations schema_migrations_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.schema_migrations
    ADD CONSTRAINT schema_migrations_pkey PRIMARY KEY (version);


--
-- Name: scim_tokens scim_tokens_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_tokens
    ADD CONSTRAINT scim_tokens_pkey PRIMARY KEY (id);


--
-- Name: scim_users scim_users_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_users
    ADD CONSTRAINT scim_users_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: sso_domains sso_domains_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sso_domains
    ADD CONSTRAINT sso_domains_pkey PRIMARY KEY (id);


--
-- Name: sso_providers sso_providers_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sso_providers
    ADD CONSTRAINT sso_providers_pkey PRIMARY KEY (id);


--
-- Name: users users_phone_key; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.users
    ADD CONSTRAINT users_phone_key UNIQUE (phone);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: webauthn_challenges webauthn_challenges_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_challenges
    ADD CONSTRAINT webauthn_challenges_pkey PRIMARY KEY (id);


--
-- Name: webauthn_credentials webauthn_credentials_pkey; Type: CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_credentials
    ADD CONSTRAINT webauthn_credentials_pkey PRIMARY KEY (id);


--
-- Name: USER USER_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."USER"
    ADD CONSTRAINT "USER_pkey" PRIMARY KEY (usr_id);


--
-- Name: USER USER_usr_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."USER"
    ADD CONSTRAINT "USER_usr_email_key" UNIQUE (usr_email);


--
-- Name: academic_year academic_year_ay_academic_year_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.academic_year
    ADD CONSTRAINT academic_year_ay_academic_year_key UNIQUE (ay_academic_year);


--
-- Name: academic_year academic_year_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.academic_year
    ADD CONSTRAINT academic_year_pkey PRIMARY KEY (ay_id);


--
-- Name: audit_log audit_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_log
    ADD CONSTRAINT audit_log_pkey PRIMARY KEY (al_id);


--
-- Name: biometric_credential biometric_credential_bio_usr_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.biometric_credential
    ADD CONSTRAINT biometric_credential_bio_usr_id_key UNIQUE (bio_usr_id);


--
-- Name: biometric_credential biometric_credential_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.biometric_credential
    ADD CONSTRAINT biometric_credential_pkey PRIMARY KEY (bio_id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: course course_dept_code_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course
    ADD CONSTRAINT course_dept_code_unique UNIQUE (course_dept_id, course_code);


--
-- Name: dean dean_dean_usr_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dean
    ADD CONSTRAINT dean_dean_usr_id_key UNIQUE (dean_usr_id);


--
-- Name: dean dean_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dean
    ADD CONSTRAINT dean_pkey PRIMARY KEY (dean_id);


--
-- Name: department_chair department_chair_dc_usr_id_dc_dept_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_chair
    ADD CONSTRAINT department_chair_dc_usr_id_dc_dept_id_key UNIQUE (dc_usr_id, dc_college_id);


--
-- Name: department_chair department_chair_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_chair
    ADD CONSTRAINT department_chair_pkey PRIMARY KEY (dc_id);


--
-- Name: college department_dept_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.college
    ADD CONSTRAINT department_dept_code_key UNIQUE (college_code);


--
-- Name: college department_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.college
    ADD CONSTRAINT department_pkey PRIMARY KEY (college_id);


--
-- Name: fac_specialization fac_specialization_fspec_fac_id_fspec_specialization_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fac_specialization
    ADD CONSTRAINT fac_specialization_fspec_fac_id_fspec_specialization_key UNIQUE (fspec_fac_id, fspec_specialization);


--
-- Name: fac_specialization fac_specialization_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fac_specialization
    ADD CONSTRAINT fac_specialization_pkey PRIMARY KEY (fspec_id);


--
-- Name: faculty_account_reviews faculty_account_reviews_fvr_usr_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faculty_account_reviews
    ADD CONSTRAINT faculty_account_reviews_fvr_usr_id_unique UNIQUE (fvr_usr_id);


--
-- Name: faculty_account_reviews faculty_account_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faculty_account_reviews
    ADD CONSTRAINT faculty_account_reviews_pkey PRIMARY KEY (fvr_id);


--
-- Name: faculty faculty_fac_usr_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faculty
    ADD CONSTRAINT faculty_fac_usr_id_key UNIQUE (fac_usr_id);


--
-- Name: faculty faculty_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faculty
    ADD CONSTRAINT faculty_pkey PRIMARY KEY (fac_id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: historical_schedule historical_schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historical_schedule
    ADD CONSTRAINT historical_schedule_pkey PRIMARY KEY (hist_id);


--
-- Name: institution institution_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.institution
    ADD CONSTRAINT institution_pkey PRIMARY KEY (inst_id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notification notification_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notification
    ADD CONSTRAINT notification_pkey PRIMARY KEY (notif_id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: department program_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department
    ADD CONSTRAINT program_pkey PRIMARY KEY (dept_id);


--
-- Name: department program_prog_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department
    ADD CONSTRAINT program_prog_code_key UNIQUE (dept_code);


--
-- Name: room room_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room
    ADD CONSTRAINT room_pkey PRIMARY KEY (room_id);


--
-- Name: schedule_archives schedule_archives_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_archives
    ADD CONSTRAINT schedule_archives_pkey PRIMARY KEY (sar_id);


--
-- Name: schedule schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_pkey PRIMARY KEY (sch_id);


--
-- Name: schedule_submission schedule_submission_dept_sem_fac_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_submission
    ADD CONSTRAINT schedule_submission_dept_sem_fac_unique UNIQUE (schsub_dept_id, schsub_sem_id, schsub_fac_id);


--
-- Name: schedule_submission schedule_submission_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_submission
    ADD CONSTRAINT schedule_submission_pkey PRIMARY KEY (schsub_id);


--
-- Name: section section_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section
    ADD CONSTRAINT section_pkey PRIMARY KEY (sec_id);


--
-- Name: section section_sec_prog_id_sec_name_sec_ay_id_sec_sem_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section
    ADD CONSTRAINT section_sec_prog_id_sec_name_sec_ay_id_sec_sem_id_key UNIQUE (sec_dept_id, sec_name, sec_ay_id, sec_sem_id);


--
-- Name: semester semester_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.semester
    ADD CONSTRAINT semester_pkey PRIMARY KEY (sem_id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: study_load study_load_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.study_load
    ADD CONSTRAINT study_load_pkey PRIMARY KEY (sl_id);


--
-- Name: study_load study_load_sl_fac_id_sl_subj_id_sl_sec_id_sl_sem_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.study_load
    ADD CONSTRAINT study_load_sl_fac_id_sl_subj_id_sl_sec_id_sl_sem_id_key UNIQUE (sl_fac_id, sl_course_id, sl_sec_id, sl_sem_id);


--
-- Name: course subject_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course
    ADD CONSTRAINT subject_pkey PRIMARY KEY (course_id);


--
-- Name: system_backup system_backup_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_backup
    ADD CONSTRAINT system_backup_pkey PRIMARY KEY (bkp_id);


--
-- Name: system_setting system_setting_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_setting
    ADD CONSTRAINT system_setting_pkey PRIMARY KEY (sset_id);


--
-- Name: system_setting system_setting_sset_key_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_setting
    ADD CONSTRAINT system_setting_sset_key_key UNIQUE (sset_key);


--
-- Name: token_blacklist token_blacklist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.token_blacklist
    ADD CONSTRAINT token_blacklist_pkey PRIMARY KEY (tbl_id);


--
-- Name: token_blacklist token_blacklist_tbl_token_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.token_blacklist
    ADD CONSTRAINT token_blacklist_tbl_token_key UNIQUE (tbl_token);


--
-- Name: department_chair uq_dc_prog; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_chair
    ADD CONSTRAINT uq_dc_prog UNIQUE (dc_dept_id);


--
-- Name: dean uq_dean_dept; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dean
    ADD CONSTRAINT uq_dean_dept UNIQUE (dean_college_id);


--
-- Name: webauthn_credentials webauthn_credentials_credential_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.webauthn_credentials
    ADD CONSTRAINT webauthn_credentials_credential_id_key UNIQUE (credential_id);


--
-- Name: webauthn_credentials webauthn_credentials_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.webauthn_credentials
    ADD CONSTRAINT webauthn_credentials_pkey PRIMARY KEY (id);


--
-- Name: workload workload_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.workload
    ADD CONSTRAINT workload_pkey PRIMARY KEY (wl_id);


--
-- Name: workload workload_wl_fac_id_wl_sem_id_wl_ay_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.workload
    ADD CONSTRAINT workload_wl_fac_id_wl_sem_id_wl_ay_id_key UNIQUE (wl_fac_id, wl_sem_id, wl_ay_id);


--
-- Name: messages messages_payload_exclusive; Type: CHECK CONSTRAINT; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE realtime.messages
    ADD CONSTRAINT messages_payload_exclusive CHECK (((payload IS NULL) OR (binary_payload IS NULL))) NOT VALID;


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE ONLY realtime.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id, inserted_at);


--
-- Name: subscription pk_subscription; Type: CONSTRAINT; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE ONLY realtime.subscription
    ADD CONSTRAINT pk_subscription PRIMARY KEY (id);


--
-- Name: schema_migrations schema_migrations_pkey; Type: CONSTRAINT; Schema: realtime; Owner: supabase_admin
--

ALTER TABLE ONLY realtime.schema_migrations
    ADD CONSTRAINT schema_migrations_pkey PRIMARY KEY (version);


--
-- Name: bucket_lifecycle_states bucket_lifecycle_states_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.bucket_lifecycle_states
    ADD CONSTRAINT bucket_lifecycle_states_pkey PRIMARY KEY (bucket_id);


--
-- Name: buckets_analytics buckets_analytics_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.buckets_analytics
    ADD CONSTRAINT buckets_analytics_pkey PRIMARY KEY (id);


--
-- Name: buckets buckets_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.buckets
    ADD CONSTRAINT buckets_pkey PRIMARY KEY (id);


--
-- Name: buckets_vectors buckets_vectors_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.buckets_vectors
    ADD CONSTRAINT buckets_vectors_pkey PRIMARY KEY (id);


--
-- Name: lifecycle_abandoned_artifacts lifecycle_abandoned_artifacts_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.lifecycle_abandoned_artifacts
    ADD CONSTRAINT lifecycle_abandoned_artifacts_pkey PRIMARY KEY (bucket_id, key);


--
-- Name: migrations migrations_name_key; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.migrations
    ADD CONSTRAINT migrations_name_key UNIQUE (name);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: objects objects_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.objects
    ADD CONSTRAINT objects_pkey PRIMARY KEY (id);


--
-- Name: s3_multipart_uploads_parts s3_multipart_uploads_parts_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads_parts
    ADD CONSTRAINT s3_multipart_uploads_parts_pkey PRIMARY KEY (id);


--
-- Name: s3_multipart_uploads s3_multipart_uploads_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads
    ADD CONSTRAINT s3_multipart_uploads_pkey PRIMARY KEY (id);


--
-- Name: vector_indexes vector_indexes_pkey; Type: CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.vector_indexes
    ADD CONSTRAINT vector_indexes_pkey PRIMARY KEY (id);


--
-- Name: audit_logs_instance_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX audit_logs_instance_id_idx ON auth.audit_log_entries USING btree (instance_id);


--
-- Name: confirmation_token_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX confirmation_token_idx ON auth.users USING btree (confirmation_token) WHERE ((confirmation_token)::text !~ '^[0-9 ]*$'::text);


--
-- Name: custom_oauth_providers_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_created_at_idx ON auth.custom_oauth_providers USING btree (created_at);


--
-- Name: custom_oauth_providers_enabled_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_enabled_idx ON auth.custom_oauth_providers USING btree (enabled);


--
-- Name: custom_oauth_providers_identifier_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_identifier_idx ON auth.custom_oauth_providers USING btree (identifier);


--
-- Name: custom_oauth_providers_provider_type_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX custom_oauth_providers_provider_type_idx ON auth.custom_oauth_providers USING btree (provider_type);


--
-- Name: email_change_token_current_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX email_change_token_current_idx ON auth.users USING btree (email_change_token_current) WHERE ((email_change_token_current)::text !~ '^[0-9 ]*$'::text);


--
-- Name: email_change_token_new_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX email_change_token_new_idx ON auth.users USING btree (email_change_token_new) WHERE ((email_change_token_new)::text !~ '^[0-9 ]*$'::text);


--
-- Name: factor_id_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX factor_id_created_at_idx ON auth.mfa_factors USING btree (user_id, created_at);


--
-- Name: flow_state_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX flow_state_created_at_idx ON auth.flow_state USING btree (created_at DESC);


--
-- Name: identities_email_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX identities_email_idx ON auth.identities USING btree (email text_pattern_ops);


--
-- Name: INDEX identities_email_idx; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON INDEX auth.identities_email_idx IS 'Auth: Ensures indexed queries on the email column';


--
-- Name: identities_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX identities_user_id_idx ON auth.identities USING btree (user_id);


--
-- Name: idx_auth_code; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_auth_code ON auth.flow_state USING btree (auth_code);


--
-- Name: idx_oauth_client_states_created_at; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_oauth_client_states_created_at ON auth.oauth_client_states USING btree (created_at);


--
-- Name: idx_user_id_auth_method; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_user_id_auth_method ON auth.flow_state USING btree (user_id, authentication_method);


--
-- Name: idx_users_created_at_desc; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_created_at_desc ON auth.users USING btree (created_at DESC);


--
-- Name: idx_users_email; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_email ON auth.users USING btree (email);


--
-- Name: idx_users_last_sign_in_at_desc; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_last_sign_in_at_desc ON auth.users USING btree (last_sign_in_at DESC);


--
-- Name: idx_users_name; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX idx_users_name ON auth.users USING btree (((raw_user_meta_data ->> 'name'::text))) WHERE ((raw_user_meta_data ->> 'name'::text) IS NOT NULL);


--
-- Name: mfa_challenge_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX mfa_challenge_created_at_idx ON auth.mfa_challenges USING btree (created_at DESC);


--
-- Name: mfa_factors_user_friendly_name_unique; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX mfa_factors_user_friendly_name_unique ON auth.mfa_factors USING btree (friendly_name, user_id) WHERE (TRIM(BOTH FROM friendly_name) <> ''::text);


--
-- Name: mfa_factors_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX mfa_factors_user_id_idx ON auth.mfa_factors USING btree (user_id);


--
-- Name: mfa_recovery_codes_set_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX mfa_recovery_codes_set_id_idx ON auth.mfa_recovery_codes USING btree (mfa_recovery_code_set_id);


--
-- Name: oauth_auth_pending_exp_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_auth_pending_exp_idx ON auth.oauth_authorizations USING btree (expires_at) WHERE (status = 'pending'::auth.oauth_authorization_status);


--
-- Name: oauth_clients_deleted_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_clients_deleted_at_idx ON auth.oauth_clients USING btree (deleted_at);


--
-- Name: oauth_consents_active_client_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_consents_active_client_idx ON auth.oauth_consents USING btree (client_id) WHERE (revoked_at IS NULL);


--
-- Name: oauth_consents_active_user_client_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_consents_active_user_client_idx ON auth.oauth_consents USING btree (user_id, client_id) WHERE (revoked_at IS NULL);


--
-- Name: oauth_consents_user_order_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX oauth_consents_user_order_idx ON auth.oauth_consents USING btree (user_id, granted_at DESC);


--
-- Name: one_time_tokens_relates_to_hash_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX one_time_tokens_relates_to_hash_idx ON auth.one_time_tokens USING hash (relates_to);


--
-- Name: one_time_tokens_token_hash_hash_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX one_time_tokens_token_hash_hash_idx ON auth.one_time_tokens USING hash (token_hash);


--
-- Name: one_time_tokens_user_id_token_type_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX one_time_tokens_user_id_token_type_key ON auth.one_time_tokens USING btree (user_id, token_type);


--
-- Name: reauthentication_token_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX reauthentication_token_idx ON auth.users USING btree (reauthentication_token) WHERE ((reauthentication_token)::text !~ '^[0-9 ]*$'::text);


--
-- Name: recovery_token_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX recovery_token_idx ON auth.users USING btree (recovery_token) WHERE ((recovery_token)::text !~ '^[0-9 ]*$'::text);


--
-- Name: refresh_tokens_instance_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_instance_id_idx ON auth.refresh_tokens USING btree (instance_id);


--
-- Name: refresh_tokens_instance_id_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_instance_id_user_id_idx ON auth.refresh_tokens USING btree (instance_id, user_id);


--
-- Name: refresh_tokens_parent_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_parent_idx ON auth.refresh_tokens USING btree (parent);


--
-- Name: refresh_tokens_session_id_revoked_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_session_id_revoked_idx ON auth.refresh_tokens USING btree (session_id, revoked);


--
-- Name: refresh_tokens_updated_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX refresh_tokens_updated_at_idx ON auth.refresh_tokens USING btree (updated_at DESC);


--
-- Name: saml_providers_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_providers_sso_provider_id_idx ON auth.saml_providers USING btree (sso_provider_id);


--
-- Name: saml_relay_states_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_relay_states_created_at_idx ON auth.saml_relay_states USING btree (created_at DESC);


--
-- Name: saml_relay_states_for_email_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_relay_states_for_email_idx ON auth.saml_relay_states USING btree (for_email);


--
-- Name: saml_relay_states_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX saml_relay_states_sso_provider_id_idx ON auth.saml_relay_states USING btree (sso_provider_id);


--
-- Name: scim_tokens_expires_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_tokens_expires_at_idx ON auth.scim_tokens USING btree (expires_at);


--
-- Name: scim_tokens_revoked_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_tokens_revoked_at_idx ON auth.scim_tokens USING btree (revoked_at);


--
-- Name: scim_tokens_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_tokens_sso_provider_id_idx ON auth.scim_tokens USING btree (sso_provider_id);


--
-- Name: scim_tokens_token_hash_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX scim_tokens_token_hash_key ON auth.scim_tokens USING btree (token_hash);


--
-- Name: scim_users_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_created_at_idx ON auth.scim_users USING btree (sso_provider_id, created_at, id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_deleted_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_deleted_at_idx ON auth.scim_users USING btree (deleted_at);


--
-- Name: scim_users_external_id_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX scim_users_external_id_key ON auth.scim_users USING btree (sso_provider_id, external_id) WHERE ((external_id IS NOT NULL) AND (deleted_at IS NULL));


--
-- Name: scim_users_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_id_idx ON auth.scim_users USING btree (sso_provider_id, id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_sso_provider_id_idx ON auth.scim_users USING btree (sso_provider_id);


--
-- Name: scim_users_updated_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_updated_at_idx ON auth.scim_users USING btree (sso_provider_id, updated_at, id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_user_id_idx ON auth.scim_users USING btree (user_id);


--
-- Name: scim_users_user_name_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX scim_users_user_name_idx ON auth.scim_users USING btree (sso_provider_id, user_name COLLATE "C", id) WHERE (deleted_at IS NULL);


--
-- Name: scim_users_user_name_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX scim_users_user_name_key ON auth.scim_users USING btree (sso_provider_id, user_name) WHERE (deleted_at IS NULL);


--
-- Name: sessions_not_after_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sessions_not_after_idx ON auth.sessions USING btree (not_after DESC);


--
-- Name: sessions_oauth_client_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sessions_oauth_client_id_idx ON auth.sessions USING btree (oauth_client_id);


--
-- Name: sessions_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sessions_user_id_idx ON auth.sessions USING btree (user_id);


--
-- Name: sso_domains_domain_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX sso_domains_domain_idx ON auth.sso_domains USING btree (lower(domain));


--
-- Name: sso_domains_sso_provider_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sso_domains_sso_provider_id_idx ON auth.sso_domains USING btree (sso_provider_id);


--
-- Name: sso_providers_resource_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX sso_providers_resource_id_idx ON auth.sso_providers USING btree (lower(resource_id));


--
-- Name: sso_providers_resource_id_pattern_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX sso_providers_resource_id_pattern_idx ON auth.sso_providers USING btree (resource_id text_pattern_ops);


--
-- Name: unique_phone_factor_per_user; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX unique_phone_factor_per_user ON auth.mfa_factors USING btree (user_id, phone);


--
-- Name: user_id_created_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX user_id_created_at_idx ON auth.sessions USING btree (user_id, created_at);


--
-- Name: users_email_partial_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX users_email_partial_key ON auth.users USING btree (email) WHERE (is_sso_user = false);


--
-- Name: INDEX users_email_partial_key; Type: COMMENT; Schema: auth; Owner: supabase_auth_admin
--

COMMENT ON INDEX auth.users_email_partial_key IS 'Auth: A partial unique index that applies only when is_sso_user is false';


--
-- Name: users_instance_id_email_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX users_instance_id_email_idx ON auth.users USING btree (instance_id, lower((email)::text));


--
-- Name: users_instance_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX users_instance_id_idx ON auth.users USING btree (instance_id);


--
-- Name: users_is_anonymous_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX users_is_anonymous_idx ON auth.users USING btree (is_anonymous);


--
-- Name: webauthn_challenges_expires_at_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX webauthn_challenges_expires_at_idx ON auth.webauthn_challenges USING btree (expires_at);


--
-- Name: webauthn_challenges_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX webauthn_challenges_user_id_idx ON auth.webauthn_challenges USING btree (user_id);


--
-- Name: webauthn_credentials_credential_id_key; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE UNIQUE INDEX webauthn_credentials_credential_id_key ON auth.webauthn_credentials USING btree (credential_id);


--
-- Name: webauthn_credentials_user_id_idx; Type: INDEX; Schema: auth; Owner: supabase_auth_admin
--

CREATE INDEX webauthn_credentials_user_id_idx ON auth.webauthn_credentials USING btree (user_id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: faculty_account_reviews_fvr_status_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX faculty_account_reviews_fvr_status_index ON public.faculty_account_reviews USING btree (fvr_status);


--
-- Name: idx_al_target; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_al_target ON public.audit_log USING btree (al_target_table, al_target_id);


--
-- Name: idx_al_usr; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_al_usr ON public.audit_log USING btree (al_usr_id);


--
-- Name: idx_course_year_sem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_course_year_sem ON public.course USING btree (course_dept_id, course_year_level, course_semester) WHERE (course_is_active = true);


--
-- Name: idx_dc_dept; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dc_dept ON public.department_chair USING btree (dc_college_id);


--
-- Name: idx_dc_prog; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dc_prog ON public.department_chair USING btree (dc_dept_id);


--
-- Name: idx_dean_dept; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dean_dept ON public.dean USING btree (dean_college_id);


--
-- Name: idx_dean_prog; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dean_prog ON public.dean USING btree (dean_dept_id);


--
-- Name: idx_faculty_dept; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_faculty_dept ON public.faculty USING btree (fac_college_id);


--
-- Name: idx_faculty_prog; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_faculty_prog ON public.faculty USING btree (fac_dept_id);


--
-- Name: idx_faculty_usr; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_faculty_usr ON public.faculty USING btree (fac_usr_id);


--
-- Name: idx_fspec_fac; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fspec_fac ON public.fac_specialization USING btree (fspec_fac_id);


--
-- Name: idx_hist_fac; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_hist_fac ON public.historical_schedule USING btree (hist_fac_id);


--
-- Name: idx_notif_usr; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notif_usr ON public.notification USING btree (notif_usr_id);


--
-- Name: idx_prog_dept; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_prog_dept ON public.department USING btree (dept_college_id);


--
-- Name: idx_sch_day_time; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sch_day_time ON public.schedule USING btree (sch_day, sch_start_time);


--
-- Name: idx_sch_fac; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sch_fac ON public.schedule USING btree (sch_fac_id);


--
-- Name: idx_sch_room; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sch_room ON public.schedule USING btree (sch_room_id);


--
-- Name: idx_sch_sec; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sch_sec ON public.schedule USING btree (sch_sec_id);


--
-- Name: idx_sch_sem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sch_sem ON public.schedule USING btree (sch_sem_id);


--
-- Name: idx_schsub_dept_sem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_schsub_dept_sem ON public.schedule_submission USING btree (schsub_dept_id, schsub_sem_id);


--
-- Name: idx_section_ay; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_section_ay ON public.section USING btree (sec_ay_id);


--
-- Name: idx_section_prog; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_section_prog ON public.section USING btree (sec_dept_id);


--
-- Name: idx_section_sem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_section_sem ON public.section USING btree (sec_sem_id);


--
-- Name: idx_sem_ay; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sem_ay ON public.semester USING btree (sem_ay_id);


--
-- Name: idx_sl_fac; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sl_fac ON public.study_load USING btree (sl_fac_id);


--
-- Name: idx_sl_sec; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sl_sec ON public.study_load USING btree (sl_sec_id);


--
-- Name: idx_sl_sem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_sl_sem ON public.study_load USING btree (sl_sem_id);


--
-- Name: idx_subj_dept; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_subj_dept ON public.course USING btree (course_college_id);


--
-- Name: idx_subj_prog; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_subj_prog ON public.course USING btree (course_dept_id);


--
-- Name: idx_wl_fac; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_wl_fac ON public.workload USING btree (wl_fac_id);


--
-- Name: idx_wl_sem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_wl_sem ON public.workload USING btree (wl_sem_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: room_name_location_building_unique; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX room_name_location_building_unique ON public.room USING btree (room_name, COALESCE(room_building, ''::character varying));


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: webauthn_credentials_usr_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX webauthn_credentials_usr_id_idx ON public.webauthn_credentials USING btree (usr_id);


--
-- Name: ix_realtime_subscription_entity; Type: INDEX; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE INDEX ix_realtime_subscription_entity ON realtime.subscription USING btree (entity);


--
-- Name: messages_replay_index; Type: INDEX; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE INDEX messages_replay_index ON ONLY realtime.messages USING btree (topic, inserted_at DESC, COALESCE(expires_at, 'infinity'::timestamp without time zone)) WHERE ((extension = 'broadcast'::text) AND (private = true));


--
-- Name: subscription_subscription_id_entity_filters_hash_key; Type: INDEX; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE UNIQUE INDEX subscription_subscription_id_entity_filters_hash_key ON realtime.subscription USING btree (subscription_id, entity, realtime.filters_hash(filters), action_filter, COALESCE(selected_columns, '{}'::text[]));


--
-- Name: bname; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX bname ON storage.buckets USING btree (name);


--
-- Name: bucket_lifecycle_states_due_idx; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX bucket_lifecycle_states_due_idx ON storage.bucket_lifecycle_states USING btree (next_run_at, bucket_id) WHERE (next_run_at IS NOT NULL);


--
-- Name: buckets_analytics_unique_name_idx; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX buckets_analytics_unique_name_idx ON storage.buckets_analytics USING btree (name) WHERE (deleted_at IS NULL);


--
-- Name: idx_multipart_uploads_list; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_multipart_uploads_list ON storage.s3_multipart_uploads USING btree (bucket_id, key, created_at);


--
-- Name: idx_objects_bucket_id_name; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_objects_bucket_id_name ON storage.objects USING btree (bucket_id, name COLLATE "C");


--
-- Name: idx_objects_bucket_id_name_lower; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_objects_bucket_id_name_lower ON storage.objects USING btree (bucket_id, lower(name) COLLATE "C");


--
-- Name: idx_objects_current_version; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX idx_objects_current_version ON storage.objects USING btree (bucket_id, name COLLATE "C") WHERE (archived_at IS NULL);


--
-- Name: idx_objects_delete_markers; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX idx_objects_delete_markers ON storage.objects USING btree (bucket_id, name COLLATE "C") WHERE is_delete_marker;


--
-- Name: idx_objects_null_version; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX idx_objects_null_version ON storage.objects USING btree (bucket_id, name COLLATE "C") WHERE (NOT is_versioned);


--
-- Name: lifecycle_abandoned_artifacts_attempted_at_idx; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX lifecycle_abandoned_artifacts_attempted_at_idx ON storage.lifecycle_abandoned_artifacts USING btree (attempted_at);


--
-- Name: name_prefix_search; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX name_prefix_search ON storage.objects USING btree (name text_pattern_ops);


--
-- Name: objects_archived_due_idx; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE INDEX objects_archived_due_idx ON storage.objects USING btree (bucket_id, archived_at, name COLLATE "C") INCLUDE (id, version, is_delete_marker) WHERE (archived_at IS NOT NULL);


--
-- Name: objects_archived_order_uq; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX objects_archived_order_uq ON storage.objects USING btree (bucket_id, name COLLATE "C", archived_at) WHERE (archived_at IS NOT NULL);


--
-- Name: objects_bucket_id_name_version_key; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX objects_bucket_id_name_version_key ON storage.objects USING btree (bucket_id, name COLLATE "C", version) NULLS NOT DISTINCT;


--
-- Name: vector_indexes_name_bucket_id_idx; Type: INDEX; Schema: storage; Owner: supabase_storage_admin
--

CREATE UNIQUE INDEX vector_indexes_name_bucket_id_idx ON storage.vector_indexes USING btree (name, bucket_id);


--
-- Name: subscription tr_check_filters; Type: TRIGGER; Schema: realtime; Owner: supabase_realtime_admin
--

CREATE TRIGGER tr_check_filters BEFORE INSERT OR UPDATE ON realtime.subscription FOR EACH ROW EXECUTE FUNCTION realtime.subscription_check_filters();


--
-- Name: buckets enforce_bucket_name_length_trigger; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER enforce_bucket_name_length_trigger BEFORE INSERT OR UPDATE OF name ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.enforce_bucket_name_length();


--
-- Name: buckets enforce_versioning_status_transition; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER enforce_versioning_status_transition BEFORE UPDATE OF versioning_status ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.enforce_versioning_status_transition();


--
-- Name: buckets protect_active_lifecycle_states; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_active_lifecycle_states BEFORE DELETE ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.protect_active_lifecycle_states();


--
-- Name: buckets protect_bucket_control_insert; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_bucket_control_insert BEFORE INSERT ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.protect_bucket_control_columns('service_role');


--
-- Name: buckets protect_bucket_control_update; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_bucket_control_update BEFORE UPDATE OF lifecycle_configuration, lifecycle_configuration_generation ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.protect_bucket_control_columns();


--
-- Name: buckets protect_bucket_control_update_role; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_bucket_control_update_role AFTER UPDATE OF lifecycle_configuration, lifecycle_configuration_generation ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.enforce_bucket_lifecycle_service_role('service_role');


--
-- Name: buckets protect_buckets_delete; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_buckets_delete BEFORE DELETE ON storage.buckets FOR EACH STATEMENT EXECUTE FUNCTION storage.protect_delete();


--
-- Name: objects protect_objects_delete; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER protect_objects_delete BEFORE DELETE ON storage.objects FOR EACH STATEMENT EXECUTE FUNCTION storage.protect_delete();


--
-- Name: objects update_objects_updated_at; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER update_objects_updated_at BEFORE UPDATE ON storage.objects FOR EACH ROW EXECUTE FUNCTION storage.update_updated_at_column();


--
-- Name: buckets wake_lifecycle_on_policy_write; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER wake_lifecycle_on_policy_write AFTER INSERT OR UPDATE OF lifecycle_configuration_generation ON storage.buckets FOR EACH ROW EXECUTE FUNCTION storage.wake_lifecycle_state();


--
-- Name: buckets wake_lifecycle_on_versioning_enable; Type: TRIGGER; Schema: storage; Owner: supabase_storage_admin
--

CREATE TRIGGER wake_lifecycle_on_versioning_enable AFTER UPDATE OF versioning_status ON storage.buckets FOR EACH ROW WHEN ((new.lifecycle_configuration IS NOT NULL)) EXECUTE FUNCTION storage.wake_lifecycle_state();


--
-- Name: identities identities_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.identities
    ADD CONSTRAINT identities_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: mfa_amr_claims mfa_amr_claims_session_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_amr_claims
    ADD CONSTRAINT mfa_amr_claims_session_id_fkey FOREIGN KEY (session_id) REFERENCES auth.sessions(id) ON DELETE CASCADE;


--
-- Name: mfa_challenges mfa_challenges_auth_factor_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_challenges
    ADD CONSTRAINT mfa_challenges_auth_factor_id_fkey FOREIGN KEY (factor_id) REFERENCES auth.mfa_factors(id) ON DELETE CASCADE;


--
-- Name: mfa_factors mfa_factors_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_factors
    ADD CONSTRAINT mfa_factors_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_mfa_factor_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_mfa_factor_id_fkey FOREIGN KEY (mfa_factor_id) REFERENCES auth.mfa_factors(id) ON DELETE CASCADE;


--
-- Name: mfa_recovery_code_sets mfa_recovery_code_sets_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_code_sets
    ADD CONSTRAINT mfa_recovery_code_sets_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: mfa_recovery_codes mfa_recovery_codes_mfa_recovery_code_set_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.mfa_recovery_codes
    ADD CONSTRAINT mfa_recovery_codes_mfa_recovery_code_set_id_fkey FOREIGN KEY (mfa_recovery_code_set_id) REFERENCES auth.mfa_recovery_code_sets(id) ON DELETE CASCADE;


--
-- Name: oauth_authorizations oauth_authorizations_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_client_id_fkey FOREIGN KEY (client_id) REFERENCES auth.oauth_clients(id) ON DELETE CASCADE;


--
-- Name: oauth_authorizations oauth_authorizations_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_authorizations
    ADD CONSTRAINT oauth_authorizations_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: oauth_consents oauth_consents_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_client_id_fkey FOREIGN KEY (client_id) REFERENCES auth.oauth_clients(id) ON DELETE CASCADE;


--
-- Name: oauth_consents oauth_consents_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.oauth_consents
    ADD CONSTRAINT oauth_consents_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: one_time_tokens one_time_tokens_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.one_time_tokens
    ADD CONSTRAINT one_time_tokens_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: refresh_tokens refresh_tokens_session_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.refresh_tokens
    ADD CONSTRAINT refresh_tokens_session_id_fkey FOREIGN KEY (session_id) REFERENCES auth.sessions(id) ON DELETE CASCADE;


--
-- Name: saml_providers saml_providers_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_providers
    ADD CONSTRAINT saml_providers_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: saml_relay_states saml_relay_states_flow_state_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_relay_states
    ADD CONSTRAINT saml_relay_states_flow_state_id_fkey FOREIGN KEY (flow_state_id) REFERENCES auth.flow_state(id) ON DELETE CASCADE;


--
-- Name: saml_relay_states saml_relay_states_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.saml_relay_states
    ADD CONSTRAINT saml_relay_states_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: scim_tokens scim_tokens_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_tokens
    ADD CONSTRAINT scim_tokens_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: scim_users scim_users_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_users
    ADD CONSTRAINT scim_users_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: scim_users scim_users_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.scim_users
    ADD CONSTRAINT scim_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE SET NULL;


--
-- Name: sessions sessions_oauth_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sessions
    ADD CONSTRAINT sessions_oauth_client_id_fkey FOREIGN KEY (oauth_client_id) REFERENCES auth.oauth_clients(id) ON DELETE CASCADE;


--
-- Name: sessions sessions_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sessions
    ADD CONSTRAINT sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: sso_domains sso_domains_sso_provider_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.sso_domains
    ADD CONSTRAINT sso_domains_sso_provider_id_fkey FOREIGN KEY (sso_provider_id) REFERENCES auth.sso_providers(id) ON DELETE CASCADE;


--
-- Name: webauthn_challenges webauthn_challenges_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_challenges
    ADD CONSTRAINT webauthn_challenges_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: webauthn_credentials webauthn_credentials_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE ONLY auth.webauthn_credentials
    ADD CONSTRAINT webauthn_credentials_user_id_fkey FOREIGN KEY (user_id) REFERENCES auth.users(id) ON DELETE CASCADE;


--
-- Name: audit_log audit_log_al_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_log
    ADD CONSTRAINT audit_log_al_usr_id_fkey FOREIGN KEY (al_usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: biometric_credential biometric_credential_bio_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.biometric_credential
    ADD CONSTRAINT biometric_credential_bio_usr_id_fkey FOREIGN KEY (bio_usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: dean dean_dean_dept_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dean
    ADD CONSTRAINT dean_dean_dept_id_fkey FOREIGN KEY (dean_college_id) REFERENCES public.college(college_id) ON DELETE CASCADE;


--
-- Name: dean dean_dean_prog_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dean
    ADD CONSTRAINT dean_dean_prog_id_fkey FOREIGN KEY (dean_dept_id) REFERENCES public.department(dept_id) ON DELETE CASCADE;


--
-- Name: dean dean_dean_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dean
    ADD CONSTRAINT dean_dean_usr_id_fkey FOREIGN KEY (dean_usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: department_chair department_chair_dc_dept_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_chair
    ADD CONSTRAINT department_chair_dc_dept_id_fkey FOREIGN KEY (dc_college_id) REFERENCES public.college(college_id) ON DELETE CASCADE;


--
-- Name: department_chair department_chair_dc_prog_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_chair
    ADD CONSTRAINT department_chair_dc_prog_id_fkey FOREIGN KEY (dc_dept_id) REFERENCES public.department(dept_id) ON DELETE CASCADE;


--
-- Name: department_chair department_chair_dc_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department_chair
    ADD CONSTRAINT department_chair_dc_usr_id_fkey FOREIGN KEY (dc_usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: fac_specialization fac_specialization_fspec_fac_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fac_specialization
    ADD CONSTRAINT fac_specialization_fspec_fac_id_fkey FOREIGN KEY (fspec_fac_id) REFERENCES public.faculty(fac_id) ON DELETE CASCADE;


--
-- Name: faculty faculty_fac_dept_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faculty
    ADD CONSTRAINT faculty_fac_dept_id_fkey FOREIGN KEY (fac_college_id) REFERENCES public.college(college_id) ON DELETE CASCADE;


--
-- Name: faculty faculty_fac_prog_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faculty
    ADD CONSTRAINT faculty_fac_prog_id_fkey FOREIGN KEY (fac_dept_id) REFERENCES public.department(dept_id) ON DELETE CASCADE;


--
-- Name: faculty faculty_fac_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faculty
    ADD CONSTRAINT faculty_fac_usr_id_fkey FOREIGN KEY (fac_usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: notification notification_notif_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notification
    ADD CONSTRAINT notification_notif_usr_id_fkey FOREIGN KEY (notif_usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: department program_prog_dept_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.department
    ADD CONSTRAINT program_prog_dept_id_fkey FOREIGN KEY (dept_college_id) REFERENCES public.college(college_id) ON DELETE CASCADE;


--
-- Name: schedule schedule_sch_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_sch_created_by_fkey FOREIGN KEY (sch_created_by) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: schedule schedule_sch_fac_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_sch_fac_id_fkey FOREIGN KEY (sch_fac_id) REFERENCES public.faculty(fac_id) ON DELETE CASCADE;


--
-- Name: schedule schedule_sch_load_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_sch_load_id_fkey FOREIGN KEY (sch_load_id) REFERENCES public.study_load(sl_id) ON DELETE CASCADE;


--
-- Name: schedule schedule_sch_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_sch_room_id_fkey FOREIGN KEY (sch_room_id) REFERENCES public.room(room_id) ON DELETE CASCADE;


--
-- Name: schedule schedule_sch_sec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_sch_sec_id_fkey FOREIGN KEY (sch_sec_id) REFERENCES public.section(sec_id) ON DELETE CASCADE;


--
-- Name: schedule schedule_sch_sem_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_sch_sem_id_fkey FOREIGN KEY (sch_sem_id) REFERENCES public.semester(sem_id) ON DELETE CASCADE;


--
-- Name: schedule schedule_sch_subj_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule
    ADD CONSTRAINT schedule_sch_subj_id_fkey FOREIGN KEY (sch_course_id) REFERENCES public.course(course_id) ON DELETE CASCADE;


--
-- Name: schedule_submission schedule_submission_schsub_dept_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_submission
    ADD CONSTRAINT schedule_submission_schsub_dept_id_fkey FOREIGN KEY (schsub_dept_id) REFERENCES public.department(dept_id) ON DELETE CASCADE;


--
-- Name: schedule_submission schedule_submission_schsub_fac_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_submission
    ADD CONSTRAINT schedule_submission_schsub_fac_id_foreign FOREIGN KEY (schsub_fac_id) REFERENCES public.faculty(fac_id) ON DELETE SET NULL;


--
-- Name: schedule_submission schedule_submission_schsub_reviewed_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_submission
    ADD CONSTRAINT schedule_submission_schsub_reviewed_by_fkey FOREIGN KEY (schsub_reviewed_by) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: schedule_submission schedule_submission_schsub_sem_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_submission
    ADD CONSTRAINT schedule_submission_schsub_sem_id_fkey FOREIGN KEY (schsub_sem_id) REFERENCES public.semester(sem_id) ON DELETE CASCADE;


--
-- Name: schedule_submission schedule_submission_schsub_submitted_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedule_submission
    ADD CONSTRAINT schedule_submission_schsub_submitted_by_fkey FOREIGN KEY (schsub_submitted_by) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: section section_sec_ay_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section
    ADD CONSTRAINT section_sec_ay_id_fkey FOREIGN KEY (sec_ay_id) REFERENCES public.academic_year(ay_id) ON DELETE CASCADE;


--
-- Name: section section_sec_prog_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section
    ADD CONSTRAINT section_sec_prog_id_fkey FOREIGN KEY (sec_dept_id) REFERENCES public.department(dept_id) ON DELETE CASCADE;


--
-- Name: section section_sec_sem_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section
    ADD CONSTRAINT section_sec_sem_id_fkey FOREIGN KEY (sec_sem_id) REFERENCES public.semester(sem_id) ON DELETE CASCADE;


--
-- Name: semester semester_sem_ay_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.semester
    ADD CONSTRAINT semester_sem_ay_id_fkey FOREIGN KEY (sem_ay_id) REFERENCES public.academic_year(ay_id) ON DELETE CASCADE;


--
-- Name: study_load study_load_sl_assigned_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.study_load
    ADD CONSTRAINT study_load_sl_assigned_by_fkey FOREIGN KEY (sl_assigned_by) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: study_load study_load_sl_fac_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.study_load
    ADD CONSTRAINT study_load_sl_fac_id_fkey FOREIGN KEY (sl_fac_id) REFERENCES public.faculty(fac_id) ON DELETE CASCADE;


--
-- Name: study_load study_load_sl_sec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.study_load
    ADD CONSTRAINT study_load_sl_sec_id_fkey FOREIGN KEY (sl_sec_id) REFERENCES public.section(sec_id) ON DELETE CASCADE;


--
-- Name: study_load study_load_sl_sem_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.study_load
    ADD CONSTRAINT study_load_sl_sem_id_fkey FOREIGN KEY (sl_sem_id) REFERENCES public.semester(sem_id) ON DELETE CASCADE;


--
-- Name: study_load study_load_sl_subj_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.study_load
    ADD CONSTRAINT study_load_sl_subj_id_fkey FOREIGN KEY (sl_course_id) REFERENCES public.course(course_id) ON DELETE CASCADE;


--
-- Name: course subject_subj_dept_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course
    ADD CONSTRAINT subject_subj_dept_id_fkey FOREIGN KEY (course_college_id) REFERENCES public.college(college_id) ON DELETE CASCADE;


--
-- Name: course subject_subj_prog_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course
    ADD CONSTRAINT subject_subj_prog_id_fkey FOREIGN KEY (course_dept_id) REFERENCES public.department(dept_id) ON DELETE CASCADE;


--
-- Name: system_setting system_setting_sset_updated_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_setting
    ADD CONSTRAINT system_setting_sset_updated_by_fkey FOREIGN KEY (sset_updated_by) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: token_blacklist token_blacklist_tbl_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.token_blacklist
    ADD CONSTRAINT token_blacklist_tbl_usr_id_fkey FOREIGN KEY (tbl_usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: webauthn_credentials webauthn_credentials_usr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.webauthn_credentials
    ADD CONSTRAINT webauthn_credentials_usr_id_fkey FOREIGN KEY (usr_id) REFERENCES public."USER"(usr_id) ON DELETE CASCADE;


--
-- Name: workload workload_wl_ay_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.workload
    ADD CONSTRAINT workload_wl_ay_id_fkey FOREIGN KEY (wl_ay_id) REFERENCES public.academic_year(ay_id) ON DELETE CASCADE;


--
-- Name: workload workload_wl_fac_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.workload
    ADD CONSTRAINT workload_wl_fac_id_fkey FOREIGN KEY (wl_fac_id) REFERENCES public.faculty(fac_id) ON DELETE CASCADE;


--
-- Name: workload workload_wl_sem_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.workload
    ADD CONSTRAINT workload_wl_sem_id_fkey FOREIGN KEY (wl_sem_id) REFERENCES public.semester(sem_id) ON DELETE CASCADE;


--
-- Name: bucket_lifecycle_states bucket_lifecycle_states_bucket_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.bucket_lifecycle_states
    ADD CONSTRAINT bucket_lifecycle_states_bucket_id_fkey FOREIGN KEY (bucket_id) REFERENCES storage.buckets(id) ON DELETE CASCADE;


--
-- Name: objects objects_bucketId_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.objects
    ADD CONSTRAINT "objects_bucketId_fkey" FOREIGN KEY (bucket_id) REFERENCES storage.buckets(id);


--
-- Name: s3_multipart_uploads s3_multipart_uploads_bucket_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads
    ADD CONSTRAINT s3_multipart_uploads_bucket_id_fkey FOREIGN KEY (bucket_id) REFERENCES storage.buckets(id);


--
-- Name: s3_multipart_uploads_parts s3_multipart_uploads_parts_bucket_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads_parts
    ADD CONSTRAINT s3_multipart_uploads_parts_bucket_id_fkey FOREIGN KEY (bucket_id) REFERENCES storage.buckets(id);


--
-- Name: s3_multipart_uploads_parts s3_multipart_uploads_parts_upload_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.s3_multipart_uploads_parts
    ADD CONSTRAINT s3_multipart_uploads_parts_upload_id_fkey FOREIGN KEY (upload_id) REFERENCES storage.s3_multipart_uploads(id) ON DELETE CASCADE;


--
-- Name: vector_indexes vector_indexes_bucket_id_fkey; Type: FK CONSTRAINT; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE ONLY storage.vector_indexes
    ADD CONSTRAINT vector_indexes_bucket_id_fkey FOREIGN KEY (bucket_id) REFERENCES storage.buckets_vectors(id);


--
-- Name: audit_log_entries; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.audit_log_entries ENABLE ROW LEVEL SECURITY;

--
-- Name: flow_state; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.flow_state ENABLE ROW LEVEL SECURITY;

--
-- Name: identities; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.identities ENABLE ROW LEVEL SECURITY;

--
-- Name: instances; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.instances ENABLE ROW LEVEL SECURITY;

--
-- Name: mfa_amr_claims; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.mfa_amr_claims ENABLE ROW LEVEL SECURITY;

--
-- Name: mfa_challenges; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.mfa_challenges ENABLE ROW LEVEL SECURITY;

--
-- Name: mfa_factors; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.mfa_factors ENABLE ROW LEVEL SECURITY;

--
-- Name: one_time_tokens; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.one_time_tokens ENABLE ROW LEVEL SECURITY;

--
-- Name: refresh_tokens; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.refresh_tokens ENABLE ROW LEVEL SECURITY;

--
-- Name: saml_providers; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.saml_providers ENABLE ROW LEVEL SECURITY;

--
-- Name: saml_relay_states; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.saml_relay_states ENABLE ROW LEVEL SECURITY;

--
-- Name: schema_migrations; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.schema_migrations ENABLE ROW LEVEL SECURITY;

--
-- Name: sessions; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.sessions ENABLE ROW LEVEL SECURITY;

--
-- Name: sso_domains; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.sso_domains ENABLE ROW LEVEL SECURITY;

--
-- Name: sso_providers; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.sso_providers ENABLE ROW LEVEL SECURITY;

--
-- Name: users; Type: ROW SECURITY; Schema: auth; Owner: supabase_auth_admin
--

ALTER TABLE auth.users ENABLE ROW LEVEL SECURITY;

--
-- Name: USER; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public."USER" ENABLE ROW LEVEL SECURITY;

--
-- Name: academic_year; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.academic_year ENABLE ROW LEVEL SECURITY;

--
-- Name: audit_log; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.audit_log ENABLE ROW LEVEL SECURITY;

--
-- Name: biometric_credential; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.biometric_credential ENABLE ROW LEVEL SECURITY;

--
-- Name: cache; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.cache ENABLE ROW LEVEL SECURITY;

--
-- Name: cache_locks; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.cache_locks ENABLE ROW LEVEL SECURITY;

--
-- Name: college; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.college ENABLE ROW LEVEL SECURITY;

--
-- Name: course; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.course ENABLE ROW LEVEL SECURITY;

--
-- Name: dean; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.dean ENABLE ROW LEVEL SECURITY;

--
-- Name: department; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.department ENABLE ROW LEVEL SECURITY;

--
-- Name: department_chair; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.department_chair ENABLE ROW LEVEL SECURITY;

--
-- Name: fac_specialization; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.fac_specialization ENABLE ROW LEVEL SECURITY;

--
-- Name: faculty; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.faculty ENABLE ROW LEVEL SECURITY;

--
-- Name: faculty_account_reviews; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.faculty_account_reviews ENABLE ROW LEVEL SECURITY;

--
-- Name: failed_jobs; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.failed_jobs ENABLE ROW LEVEL SECURITY;

--
-- Name: historical_schedule; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.historical_schedule ENABLE ROW LEVEL SECURITY;

--
-- Name: institution; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.institution ENABLE ROW LEVEL SECURITY;

--
-- Name: job_batches; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.job_batches ENABLE ROW LEVEL SECURITY;

--
-- Name: jobs; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.jobs ENABLE ROW LEVEL SECURITY;

--
-- Name: migrations; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.migrations ENABLE ROW LEVEL SECURITY;

--
-- Name: notification; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.notification ENABLE ROW LEVEL SECURITY;

--
-- Name: password_reset_tokens; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.password_reset_tokens ENABLE ROW LEVEL SECURITY;

--
-- Name: personal_access_tokens; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.personal_access_tokens ENABLE ROW LEVEL SECURITY;

--
-- Name: room; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.room ENABLE ROW LEVEL SECURITY;

--
-- Name: schedule; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.schedule ENABLE ROW LEVEL SECURITY;

--
-- Name: schedule_archives; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.schedule_archives ENABLE ROW LEVEL SECURITY;

--
-- Name: schedule_submission; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.schedule_submission ENABLE ROW LEVEL SECURITY;

--
-- Name: section; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.section ENABLE ROW LEVEL SECURITY;

--
-- Name: semester; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.semester ENABLE ROW LEVEL SECURITY;

--
-- Name: sessions; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.sessions ENABLE ROW LEVEL SECURITY;

--
-- Name: study_load; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.study_load ENABLE ROW LEVEL SECURITY;

--
-- Name: system_backup; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.system_backup ENABLE ROW LEVEL SECURITY;

--
-- Name: system_setting; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.system_setting ENABLE ROW LEVEL SECURITY;

--
-- Name: token_blacklist; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.token_blacklist ENABLE ROW LEVEL SECURITY;

--
-- Name: webauthn_credentials; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.webauthn_credentials ENABLE ROW LEVEL SECURITY;

--
-- Name: workload; Type: ROW SECURITY; Schema: public; Owner: postgres
--

ALTER TABLE public.workload ENABLE ROW LEVEL SECURITY;

--
-- Name: messages; Type: ROW SECURITY; Schema: realtime; Owner: supabase_realtime_admin
--

ALTER TABLE realtime.messages ENABLE ROW LEVEL SECURITY;

--
-- Name: bucket_lifecycle_states; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.bucket_lifecycle_states ENABLE ROW LEVEL SECURITY;

--
-- Name: buckets; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.buckets ENABLE ROW LEVEL SECURITY;

--
-- Name: buckets_analytics; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.buckets_analytics ENABLE ROW LEVEL SECURITY;

--
-- Name: buckets_vectors; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.buckets_vectors ENABLE ROW LEVEL SECURITY;

--
-- Name: lifecycle_abandoned_artifacts; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.lifecycle_abandoned_artifacts ENABLE ROW LEVEL SECURITY;

--
-- Name: migrations; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.migrations ENABLE ROW LEVEL SECURITY;

--
-- Name: objects; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.objects ENABLE ROW LEVEL SECURITY;

--
-- Name: s3_multipart_uploads; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.s3_multipart_uploads ENABLE ROW LEVEL SECURITY;

--
-- Name: s3_multipart_uploads_parts; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.s3_multipart_uploads_parts ENABLE ROW LEVEL SECURITY;

--
-- Name: vector_indexes; Type: ROW SECURITY; Schema: storage; Owner: supabase_storage_admin
--

ALTER TABLE storage.vector_indexes ENABLE ROW LEVEL SECURITY;

--
-- Name: supabase_realtime; Type: PUBLICATION; Schema: -; Owner: postgres
--

CREATE PUBLICATION supabase_realtime WITH (publish = 'insert, update, delete, truncate');


ALTER PUBLICATION supabase_realtime OWNER TO postgres;

--
-- Name: SCHEMA auth; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA auth TO anon;
GRANT USAGE ON SCHEMA auth TO authenticated;
GRANT USAGE ON SCHEMA auth TO service_role;
GRANT ALL ON SCHEMA auth TO supabase_auth_admin;
GRANT ALL ON SCHEMA auth TO dashboard_user;
GRANT USAGE ON SCHEMA auth TO postgres;


--
-- Name: SCHEMA extensions; Type: ACL; Schema: -; Owner: postgres
--

GRANT USAGE ON SCHEMA extensions TO anon;
GRANT USAGE ON SCHEMA extensions TO authenticated;
GRANT USAGE ON SCHEMA extensions TO service_role;
GRANT ALL ON SCHEMA extensions TO dashboard_user;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

GRANT USAGE ON SCHEMA public TO postgres;
GRANT USAGE ON SCHEMA public TO anon;
GRANT USAGE ON SCHEMA public TO authenticated;
GRANT USAGE ON SCHEMA public TO service_role;


--
-- Name: SCHEMA realtime; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA realtime TO postgres WITH GRANT OPTION;
GRANT USAGE ON SCHEMA realtime TO anon;
GRANT USAGE ON SCHEMA realtime TO service_role;
GRANT ALL ON SCHEMA realtime TO supabase_realtime_admin;
GRANT USAGE ON SCHEMA realtime TO authenticated;


--
-- Name: SCHEMA storage; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA storage TO postgres WITH GRANT OPTION;
GRANT USAGE ON SCHEMA storage TO anon;
GRANT USAGE ON SCHEMA storage TO authenticated;
GRANT USAGE ON SCHEMA storage TO service_role;
GRANT ALL ON SCHEMA storage TO supabase_storage_admin WITH GRANT OPTION;
GRANT ALL ON SCHEMA storage TO dashboard_user;


--
-- Name: SCHEMA vault; Type: ACL; Schema: -; Owner: supabase_admin
--

GRANT USAGE ON SCHEMA vault TO postgres WITH GRANT OPTION;
GRANT USAGE ON SCHEMA vault TO service_role;


--
-- Name: FUNCTION gbtreekey16_in(cstring); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey16_in(cstring) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey16_in(cstring) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey16_in(cstring) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey16_in(cstring) TO service_role;


--
-- Name: FUNCTION gbtreekey16_out(public.gbtreekey16); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey16_out(public.gbtreekey16) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey16_out(public.gbtreekey16) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey16_out(public.gbtreekey16) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey16_out(public.gbtreekey16) TO service_role;


--
-- Name: FUNCTION gbtreekey2_in(cstring); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey2_in(cstring) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey2_in(cstring) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey2_in(cstring) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey2_in(cstring) TO service_role;


--
-- Name: FUNCTION gbtreekey2_out(public.gbtreekey2); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey2_out(public.gbtreekey2) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey2_out(public.gbtreekey2) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey2_out(public.gbtreekey2) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey2_out(public.gbtreekey2) TO service_role;


--
-- Name: FUNCTION gbtreekey32_in(cstring); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey32_in(cstring) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey32_in(cstring) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey32_in(cstring) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey32_in(cstring) TO service_role;


--
-- Name: FUNCTION gbtreekey32_out(public.gbtreekey32); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey32_out(public.gbtreekey32) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey32_out(public.gbtreekey32) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey32_out(public.gbtreekey32) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey32_out(public.gbtreekey32) TO service_role;


--
-- Name: FUNCTION gbtreekey4_in(cstring); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey4_in(cstring) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey4_in(cstring) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey4_in(cstring) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey4_in(cstring) TO service_role;


--
-- Name: FUNCTION gbtreekey4_out(public.gbtreekey4); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey4_out(public.gbtreekey4) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey4_out(public.gbtreekey4) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey4_out(public.gbtreekey4) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey4_out(public.gbtreekey4) TO service_role;


--
-- Name: FUNCTION gbtreekey8_in(cstring); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey8_in(cstring) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey8_in(cstring) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey8_in(cstring) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey8_in(cstring) TO service_role;


--
-- Name: FUNCTION gbtreekey8_out(public.gbtreekey8); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey8_out(public.gbtreekey8) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey8_out(public.gbtreekey8) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey8_out(public.gbtreekey8) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey8_out(public.gbtreekey8) TO service_role;


--
-- Name: FUNCTION gbtreekey_var_in(cstring); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey_var_in(cstring) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey_var_in(cstring) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey_var_in(cstring) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey_var_in(cstring) TO service_role;


--
-- Name: FUNCTION gbtreekey_var_out(public.gbtreekey_var); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbtreekey_var_out(public.gbtreekey_var) TO postgres;
GRANT ALL ON FUNCTION public.gbtreekey_var_out(public.gbtreekey_var) TO anon;
GRANT ALL ON FUNCTION public.gbtreekey_var_out(public.gbtreekey_var) TO authenticated;
GRANT ALL ON FUNCTION public.gbtreekey_var_out(public.gbtreekey_var) TO service_role;


--
-- Name: FUNCTION email(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.email() TO dashboard_user;


--
-- Name: FUNCTION jwt(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.jwt() TO postgres;
GRANT ALL ON FUNCTION auth.jwt() TO dashboard_user;


--
-- Name: FUNCTION role(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.role() TO dashboard_user;


--
-- Name: FUNCTION uid(); Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON FUNCTION auth.uid() TO dashboard_user;


--
-- Name: FUNCTION armor(bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.armor(bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.armor(bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.armor(bytea) TO dashboard_user;


--
-- Name: FUNCTION armor(bytea, text[], text[]); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.armor(bytea, text[], text[]) FROM postgres;
GRANT ALL ON FUNCTION extensions.armor(bytea, text[], text[]) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.armor(bytea, text[], text[]) TO dashboard_user;


--
-- Name: FUNCTION crypt(text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.crypt(text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.crypt(text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.crypt(text, text) TO dashboard_user;


--
-- Name: FUNCTION dearmor(text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.dearmor(text) FROM postgres;
GRANT ALL ON FUNCTION extensions.dearmor(text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.dearmor(text) TO dashboard_user;


--
-- Name: FUNCTION decrypt(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.decrypt(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.decrypt(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.decrypt(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION decrypt_iv(bytea, bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.decrypt_iv(bytea, bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.decrypt_iv(bytea, bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.decrypt_iv(bytea, bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION digest(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.digest(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.digest(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.digest(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION digest(text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.digest(text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.digest(text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.digest(text, text) TO dashboard_user;


--
-- Name: FUNCTION encrypt(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.encrypt(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.encrypt(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.encrypt(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION encrypt_iv(bytea, bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.encrypt_iv(bytea, bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.encrypt_iv(bytea, bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.encrypt_iv(bytea, bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION gen_random_bytes(integer); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_random_bytes(integer) FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_random_bytes(integer) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_random_bytes(integer) TO dashboard_user;


--
-- Name: FUNCTION gen_random_uuid(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_random_uuid() FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_random_uuid() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_random_uuid() TO dashboard_user;


--
-- Name: FUNCTION gen_salt(text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_salt(text) FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_salt(text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_salt(text) TO dashboard_user;


--
-- Name: FUNCTION gen_salt(text, integer); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.gen_salt(text, integer) FROM postgres;
GRANT ALL ON FUNCTION extensions.gen_salt(text, integer) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.gen_salt(text, integer) TO dashboard_user;


--
-- Name: FUNCTION grant_pg_cron_access(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

REVOKE ALL ON FUNCTION extensions.grant_pg_cron_access() FROM supabase_admin;
GRANT ALL ON FUNCTION extensions.grant_pg_cron_access() TO supabase_admin WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.grant_pg_cron_access() TO dashboard_user;


--
-- Name: FUNCTION grant_pg_graphql_access(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.grant_pg_graphql_access() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION grant_pg_net_access(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

REVOKE ALL ON FUNCTION extensions.grant_pg_net_access() FROM supabase_admin;
GRANT ALL ON FUNCTION extensions.grant_pg_net_access() TO supabase_admin WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.grant_pg_net_access() TO dashboard_user;


--
-- Name: FUNCTION hmac(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.hmac(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.hmac(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.hmac(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION hmac(text, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.hmac(text, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.hmac(text, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.hmac(text, text, text) TO dashboard_user;


--
-- Name: FUNCTION pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone) FROM postgres;
GRANT ALL ON FUNCTION extensions.pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pg_stat_statements(showtext boolean, OUT userid oid, OUT dbid oid, OUT toplevel boolean, OUT queryid bigint, OUT query text, OUT plans bigint, OUT total_plan_time double precision, OUT min_plan_time double precision, OUT max_plan_time double precision, OUT mean_plan_time double precision, OUT stddev_plan_time double precision, OUT calls bigint, OUT total_exec_time double precision, OUT min_exec_time double precision, OUT max_exec_time double precision, OUT mean_exec_time double precision, OUT stddev_exec_time double precision, OUT rows bigint, OUT shared_blks_hit bigint, OUT shared_blks_read bigint, OUT shared_blks_dirtied bigint, OUT shared_blks_written bigint, OUT local_blks_hit bigint, OUT local_blks_read bigint, OUT local_blks_dirtied bigint, OUT local_blks_written bigint, OUT temp_blks_read bigint, OUT temp_blks_written bigint, OUT shared_blk_read_time double precision, OUT shared_blk_write_time double precision, OUT local_blk_read_time double precision, OUT local_blk_write_time double precision, OUT temp_blk_read_time double precision, OUT temp_blk_write_time double precision, OUT wal_records bigint, OUT wal_fpi bigint, OUT wal_bytes numeric, OUT jit_functions bigint, OUT jit_generation_time double precision, OUT jit_inlining_count bigint, OUT jit_inlining_time double precision, OUT jit_optimization_count bigint, OUT jit_optimization_time double precision, OUT jit_emission_count bigint, OUT jit_emission_time double precision, OUT jit_deform_count bigint, OUT jit_deform_time double precision, OUT stats_since timestamp with time zone, OUT minmax_stats_since timestamp with time zone) TO dashboard_user;


--
-- Name: FUNCTION pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone) FROM postgres;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_info(OUT dealloc bigint, OUT stats_reset timestamp with time zone) TO dashboard_user;


--
-- Name: FUNCTION pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean) FROM postgres;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pg_stat_statements_reset(userid oid, dbid oid, queryid bigint, minmax_only boolean) TO dashboard_user;


--
-- Name: FUNCTION pgp_armor_headers(text, OUT key text, OUT value text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_armor_headers(text, OUT key text, OUT value text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_armor_headers(text, OUT key text, OUT value text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_armor_headers(text, OUT key text, OUT value text) TO dashboard_user;


--
-- Name: FUNCTION pgp_key_id(bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_key_id(bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_key_id(bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_key_id(bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt(bytea, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt(bytea, bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt(bytea, bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt_bytea(bytea, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt_bytea(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_decrypt_bytea(bytea, bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_decrypt_bytea(bytea, bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt(text, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt(text, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt(text, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt_bytea(bytea, bytea); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea) TO dashboard_user;


--
-- Name: FUNCTION pgp_pub_encrypt_bytea(bytea, bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_pub_encrypt_bytea(bytea, bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt(bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt(bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt_bytea(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_decrypt_bytea(bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_decrypt_bytea(bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt(text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt(text, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt(text, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt_bytea(bytea, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text) TO dashboard_user;


--
-- Name: FUNCTION pgp_sym_encrypt_bytea(bytea, text, text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text, text) FROM postgres;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text, text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.pgp_sym_encrypt_bytea(bytea, text, text) TO dashboard_user;


--
-- Name: FUNCTION pgrst_ddl_watch(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.pgrst_ddl_watch() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION pgrst_drop_watch(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.pgrst_drop_watch() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION set_graphql_placeholder(); Type: ACL; Schema: extensions; Owner: supabase_admin
--

GRANT ALL ON FUNCTION extensions.set_graphql_placeholder() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION uuid_generate_v1(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v1() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1() TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v1mc(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v1mc() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1mc() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v1mc() TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v3(namespace uuid, name text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v3(namespace uuid, name text) FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v3(namespace uuid, name text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v3(namespace uuid, name text) TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v4(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v4() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v4() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v4() TO dashboard_user;


--
-- Name: FUNCTION uuid_generate_v5(namespace uuid, name text); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_generate_v5(namespace uuid, name text) FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_generate_v5(namespace uuid, name text) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_generate_v5(namespace uuid, name text) TO dashboard_user;


--
-- Name: FUNCTION uuid_nil(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_nil() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_nil() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_nil() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_dns(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_dns() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_dns() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_dns() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_oid(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_oid() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_oid() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_oid() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_url(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_url() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_url() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_url() TO dashboard_user;


--
-- Name: FUNCTION uuid_ns_x500(); Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON FUNCTION extensions.uuid_ns_x500() FROM postgres;
GRANT ALL ON FUNCTION extensions.uuid_ns_x500() TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION extensions.uuid_ns_x500() TO dashboard_user;


--
-- Name: FUNCTION graphql("operationName" text, query text, variables jsonb, extensions jsonb); Type: ACL; Schema: graphql_public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO postgres;
GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO anon;
GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO authenticated;
GRANT ALL ON FUNCTION graphql_public.graphql("operationName" text, query text, variables jsonb, extensions jsonb) TO service_role;


--
-- Name: FUNCTION pg_reload_conf(); Type: ACL; Schema: pg_catalog; Owner: supabase_admin
--

GRANT ALL ON FUNCTION pg_catalog.pg_reload_conf() TO postgres WITH GRANT OPTION;


--
-- Name: FUNCTION get_auth(p_usename text); Type: ACL; Schema: pgbouncer; Owner: supabase_admin
--

REVOKE ALL ON FUNCTION pgbouncer.get_auth(p_usename text) FROM PUBLIC;
GRANT ALL ON FUNCTION pgbouncer.get_auth(p_usename text) TO pgbouncer;


--
-- Name: FUNCTION cash_dist(money, money); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.cash_dist(money, money) TO postgres;
GRANT ALL ON FUNCTION public.cash_dist(money, money) TO anon;
GRANT ALL ON FUNCTION public.cash_dist(money, money) TO authenticated;
GRANT ALL ON FUNCTION public.cash_dist(money, money) TO service_role;


--
-- Name: FUNCTION date_dist(date, date); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.date_dist(date, date) TO postgres;
GRANT ALL ON FUNCTION public.date_dist(date, date) TO anon;
GRANT ALL ON FUNCTION public.date_dist(date, date) TO authenticated;
GRANT ALL ON FUNCTION public.date_dist(date, date) TO service_role;


--
-- Name: FUNCTION float4_dist(real, real); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.float4_dist(real, real) TO postgres;
GRANT ALL ON FUNCTION public.float4_dist(real, real) TO anon;
GRANT ALL ON FUNCTION public.float4_dist(real, real) TO authenticated;
GRANT ALL ON FUNCTION public.float4_dist(real, real) TO service_role;


--
-- Name: FUNCTION float8_dist(double precision, double precision); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.float8_dist(double precision, double precision) TO postgres;
GRANT ALL ON FUNCTION public.float8_dist(double precision, double precision) TO anon;
GRANT ALL ON FUNCTION public.float8_dist(double precision, double precision) TO authenticated;
GRANT ALL ON FUNCTION public.float8_dist(double precision, double precision) TO service_role;


--
-- Name: FUNCTION gbt_bit_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bit_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bit_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bit_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bit_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_bit_consistent(internal, bit, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bit_consistent(internal, bit, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bit_consistent(internal, bit, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bit_consistent(internal, bit, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bit_consistent(internal, bit, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_bit_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bit_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bit_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bit_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bit_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bit_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bit_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bit_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bit_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bit_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bit_same(public.gbtreekey_var, public.gbtreekey_var, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bit_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bit_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bit_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bit_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO service_role;


--
-- Name: FUNCTION gbt_bit_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bit_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bit_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bit_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bit_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bool_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bool_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bool_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bool_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bool_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_bool_consistent(internal, boolean, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bool_consistent(internal, boolean, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bool_consistent(internal, boolean, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bool_consistent(internal, boolean, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bool_consistent(internal, boolean, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_bool_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bool_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bool_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bool_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bool_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_bool_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bool_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bool_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bool_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bool_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bool_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bool_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bool_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bool_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bool_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bool_same(public.gbtreekey2, public.gbtreekey2, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bool_same(public.gbtreekey2, public.gbtreekey2, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bool_same(public.gbtreekey2, public.gbtreekey2, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bool_same(public.gbtreekey2, public.gbtreekey2, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bool_same(public.gbtreekey2, public.gbtreekey2, internal) TO service_role;


--
-- Name: FUNCTION gbt_bool_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bool_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bool_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bool_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bool_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bpchar_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bpchar_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bpchar_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bpchar_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bpchar_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_bpchar_consistent(internal, character, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bpchar_consistent(internal, character, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bpchar_consistent(internal, character, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bpchar_consistent(internal, character, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bpchar_consistent(internal, character, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_bytea_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bytea_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bytea_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bytea_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bytea_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_bytea_consistent(internal, bytea, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bytea_consistent(internal, bytea, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bytea_consistent(internal, bytea, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bytea_consistent(internal, bytea, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bytea_consistent(internal, bytea, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_bytea_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bytea_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bytea_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bytea_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bytea_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bytea_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bytea_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bytea_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bytea_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bytea_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_bytea_same(public.gbtreekey_var, public.gbtreekey_var, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bytea_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bytea_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bytea_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bytea_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO service_role;


--
-- Name: FUNCTION gbt_bytea_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_bytea_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_bytea_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_bytea_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_bytea_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_consistent(internal, money, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_consistent(internal, money, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_consistent(internal, money, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_consistent(internal, money, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_consistent(internal, money, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_distance(internal, money, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_distance(internal, money, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_distance(internal, money, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_distance(internal, money, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_distance(internal, money, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_cash_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_cash_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_cash_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_cash_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_cash_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_date_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_date_consistent(internal, date, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_consistent(internal, date, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_consistent(internal, date, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_consistent(internal, date, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_consistent(internal, date, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_date_distance(internal, date, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_distance(internal, date, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_distance(internal, date, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_distance(internal, date, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_distance(internal, date, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_date_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_date_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_date_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_date_same(public.gbtreekey8, public.gbtreekey8, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_same(public.gbtreekey8, public.gbtreekey8, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_same(public.gbtreekey8, public.gbtreekey8, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_same(public.gbtreekey8, public.gbtreekey8, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_same(public.gbtreekey8, public.gbtreekey8, internal) TO service_role;


--
-- Name: FUNCTION gbt_date_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_date_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_date_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_date_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_date_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_decompress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_decompress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_decompress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_decompress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_decompress(internal) TO service_role;


--
-- Name: FUNCTION gbt_enum_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_enum_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_enum_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_enum_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_enum_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_enum_consistent(internal, anyenum, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_enum_consistent(internal, anyenum, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_enum_consistent(internal, anyenum, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_enum_consistent(internal, anyenum, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_enum_consistent(internal, anyenum, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_enum_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_enum_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_enum_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_enum_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_enum_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_enum_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_enum_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_enum_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_enum_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_enum_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_enum_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_enum_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_enum_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_enum_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_enum_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_enum_same(public.gbtreekey8, public.gbtreekey8, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_enum_same(public.gbtreekey8, public.gbtreekey8, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_enum_same(public.gbtreekey8, public.gbtreekey8, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_enum_same(public.gbtreekey8, public.gbtreekey8, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_enum_same(public.gbtreekey8, public.gbtreekey8, internal) TO service_role;


--
-- Name: FUNCTION gbt_enum_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_enum_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_enum_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_enum_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_enum_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_consistent(internal, real, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_consistent(internal, real, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_consistent(internal, real, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_consistent(internal, real, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_consistent(internal, real, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_distance(internal, real, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_distance(internal, real, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_distance(internal, real, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_distance(internal, real, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_distance(internal, real, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_same(public.gbtreekey8, public.gbtreekey8, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_same(public.gbtreekey8, public.gbtreekey8, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_same(public.gbtreekey8, public.gbtreekey8, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_same(public.gbtreekey8, public.gbtreekey8, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_same(public.gbtreekey8, public.gbtreekey8, internal) TO service_role;


--
-- Name: FUNCTION gbt_float4_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float4_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float4_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float4_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float4_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_consistent(internal, double precision, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_consistent(internal, double precision, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_consistent(internal, double precision, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_consistent(internal, double precision, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_consistent(internal, double precision, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_distance(internal, double precision, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_distance(internal, double precision, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_distance(internal, double precision, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_distance(internal, double precision, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_distance(internal, double precision, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_float8_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_float8_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_float8_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_float8_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_float8_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_inet_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_inet_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_inet_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_inet_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_inet_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_inet_consistent(internal, inet, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_inet_consistent(internal, inet, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_inet_consistent(internal, inet, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_inet_consistent(internal, inet, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_inet_consistent(internal, inet, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_inet_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_inet_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_inet_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_inet_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_inet_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_inet_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_inet_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_inet_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_inet_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_inet_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_inet_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_inet_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_inet_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_inet_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_inet_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_inet_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_inet_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_inet_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_inet_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_inet_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_consistent(internal, smallint, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_consistent(internal, smallint, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_consistent(internal, smallint, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_consistent(internal, smallint, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_consistent(internal, smallint, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_distance(internal, smallint, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_distance(internal, smallint, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_distance(internal, smallint, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_distance(internal, smallint, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_distance(internal, smallint, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_same(public.gbtreekey4, public.gbtreekey4, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_same(public.gbtreekey4, public.gbtreekey4, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_same(public.gbtreekey4, public.gbtreekey4, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_same(public.gbtreekey4, public.gbtreekey4, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_same(public.gbtreekey4, public.gbtreekey4, internal) TO service_role;


--
-- Name: FUNCTION gbt_int2_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int2_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int2_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int2_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int2_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_consistent(internal, integer, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_consistent(internal, integer, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_consistent(internal, integer, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_consistent(internal, integer, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_consistent(internal, integer, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_distance(internal, integer, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_distance(internal, integer, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_distance(internal, integer, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_distance(internal, integer, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_distance(internal, integer, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_same(public.gbtreekey8, public.gbtreekey8, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_same(public.gbtreekey8, public.gbtreekey8, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_same(public.gbtreekey8, public.gbtreekey8, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_same(public.gbtreekey8, public.gbtreekey8, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_same(public.gbtreekey8, public.gbtreekey8, internal) TO service_role;


--
-- Name: FUNCTION gbt_int4_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int4_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int4_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int4_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int4_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_consistent(internal, bigint, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_consistent(internal, bigint, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_consistent(internal, bigint, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_consistent(internal, bigint, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_consistent(internal, bigint, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_distance(internal, bigint, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_distance(internal, bigint, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_distance(internal, bigint, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_distance(internal, bigint, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_distance(internal, bigint, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_int8_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_int8_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_int8_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_int8_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_int8_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_consistent(internal, interval, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_consistent(internal, interval, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_consistent(internal, interval, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_consistent(internal, interval, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_consistent(internal, interval, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_decompress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_decompress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_decompress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_decompress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_decompress(internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_distance(internal, interval, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_distance(internal, interval, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_distance(internal, interval, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_distance(internal, interval, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_distance(internal, interval, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_same(public.gbtreekey32, public.gbtreekey32, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_same(public.gbtreekey32, public.gbtreekey32, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_same(public.gbtreekey32, public.gbtreekey32, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_same(public.gbtreekey32, public.gbtreekey32, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_same(public.gbtreekey32, public.gbtreekey32, internal) TO service_role;


--
-- Name: FUNCTION gbt_intv_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_intv_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_intv_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_intv_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_intv_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad8_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad8_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad8_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad8_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad8_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_macad8_consistent(internal, macaddr8, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad8_consistent(internal, macaddr8, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad8_consistent(internal, macaddr8, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad8_consistent(internal, macaddr8, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad8_consistent(internal, macaddr8, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad8_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad8_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad8_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad8_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad8_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_macad8_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad8_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad8_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad8_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad8_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad8_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad8_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad8_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad8_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad8_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad8_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad8_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad8_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad8_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad8_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad8_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad8_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad8_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad8_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad8_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_macad_consistent(internal, macaddr, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad_consistent(internal, macaddr, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad_consistent(internal, macaddr, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad_consistent(internal, macaddr, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad_consistent(internal, macaddr, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_macad_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_macad_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_macad_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_macad_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_macad_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_macad_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_numeric_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_numeric_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_numeric_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_numeric_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_numeric_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_numeric_consistent(internal, numeric, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_numeric_consistent(internal, numeric, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_numeric_consistent(internal, numeric, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_numeric_consistent(internal, numeric, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_numeric_consistent(internal, numeric, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_numeric_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_numeric_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_numeric_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_numeric_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_numeric_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_numeric_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_numeric_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_numeric_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_numeric_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_numeric_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_numeric_same(public.gbtreekey_var, public.gbtreekey_var, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_numeric_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_numeric_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_numeric_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_numeric_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO service_role;


--
-- Name: FUNCTION gbt_numeric_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_numeric_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_numeric_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_numeric_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_numeric_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_consistent(internal, oid, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_consistent(internal, oid, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_consistent(internal, oid, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_consistent(internal, oid, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_consistent(internal, oid, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_distance(internal, oid, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_distance(internal, oid, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_distance(internal, oid, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_distance(internal, oid, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_distance(internal, oid, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_same(public.gbtreekey8, public.gbtreekey8, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_same(public.gbtreekey8, public.gbtreekey8, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_same(public.gbtreekey8, public.gbtreekey8, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_same(public.gbtreekey8, public.gbtreekey8, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_same(public.gbtreekey8, public.gbtreekey8, internal) TO service_role;


--
-- Name: FUNCTION gbt_oid_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_oid_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_oid_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_oid_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_oid_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_text_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_text_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_text_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_text_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_text_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_text_consistent(internal, text, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_text_consistent(internal, text, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_text_consistent(internal, text, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_text_consistent(internal, text, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_text_consistent(internal, text, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_text_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_text_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_text_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_text_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_text_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_text_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_text_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_text_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_text_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_text_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_text_same(public.gbtreekey_var, public.gbtreekey_var, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_text_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_text_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_text_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_text_same(public.gbtreekey_var, public.gbtreekey_var, internal) TO service_role;


--
-- Name: FUNCTION gbt_text_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_text_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_text_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_text_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_text_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_time_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_time_consistent(internal, time without time zone, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_consistent(internal, time without time zone, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_consistent(internal, time without time zone, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_consistent(internal, time without time zone, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_consistent(internal, time without time zone, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_time_distance(internal, time without time zone, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_distance(internal, time without time zone, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_distance(internal, time without time zone, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_distance(internal, time without time zone, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_distance(internal, time without time zone, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_time_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_time_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_time_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_time_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_time_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_time_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_time_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_time_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_time_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_timetz_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_timetz_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_timetz_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_timetz_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_timetz_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_timetz_consistent(internal, time with time zone, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_timetz_consistent(internal, time with time zone, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_timetz_consistent(internal, time with time zone, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_timetz_consistent(internal, time with time zone, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_timetz_consistent(internal, time with time zone, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_consistent(internal, timestamp without time zone, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_consistent(internal, timestamp without time zone, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_consistent(internal, timestamp without time zone, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_consistent(internal, timestamp without time zone, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_consistent(internal, timestamp without time zone, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_distance(internal, timestamp without time zone, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_distance(internal, timestamp without time zone, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_distance(internal, timestamp without time zone, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_distance(internal, timestamp without time zone, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_distance(internal, timestamp without time zone, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_same(public.gbtreekey16, public.gbtreekey16, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_same(public.gbtreekey16, public.gbtreekey16, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_same(public.gbtreekey16, public.gbtreekey16, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_same(public.gbtreekey16, public.gbtreekey16, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_same(public.gbtreekey16, public.gbtreekey16, internal) TO service_role;


--
-- Name: FUNCTION gbt_ts_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_ts_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_ts_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_ts_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_ts_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_tstz_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_tstz_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_tstz_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_tstz_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_tstz_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_tstz_consistent(internal, timestamp with time zone, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_tstz_consistent(internal, timestamp with time zone, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_tstz_consistent(internal, timestamp with time zone, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_tstz_consistent(internal, timestamp with time zone, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_tstz_consistent(internal, timestamp with time zone, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_tstz_distance(internal, timestamp with time zone, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_tstz_distance(internal, timestamp with time zone, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_tstz_distance(internal, timestamp with time zone, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_tstz_distance(internal, timestamp with time zone, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_tstz_distance(internal, timestamp with time zone, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_uuid_compress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_uuid_compress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_uuid_compress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_uuid_compress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_uuid_compress(internal) TO service_role;


--
-- Name: FUNCTION gbt_uuid_consistent(internal, uuid, smallint, oid, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_uuid_consistent(internal, uuid, smallint, oid, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_uuid_consistent(internal, uuid, smallint, oid, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_uuid_consistent(internal, uuid, smallint, oid, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_uuid_consistent(internal, uuid, smallint, oid, internal) TO service_role;


--
-- Name: FUNCTION gbt_uuid_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_uuid_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_uuid_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_uuid_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_uuid_fetch(internal) TO service_role;


--
-- Name: FUNCTION gbt_uuid_penalty(internal, internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_uuid_penalty(internal, internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_uuid_penalty(internal, internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_uuid_penalty(internal, internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_uuid_penalty(internal, internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_uuid_picksplit(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_uuid_picksplit(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_uuid_picksplit(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_uuid_picksplit(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_uuid_picksplit(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_uuid_same(public.gbtreekey32, public.gbtreekey32, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_uuid_same(public.gbtreekey32, public.gbtreekey32, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_uuid_same(public.gbtreekey32, public.gbtreekey32, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_uuid_same(public.gbtreekey32, public.gbtreekey32, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_uuid_same(public.gbtreekey32, public.gbtreekey32, internal) TO service_role;


--
-- Name: FUNCTION gbt_uuid_union(internal, internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_uuid_union(internal, internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_uuid_union(internal, internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_uuid_union(internal, internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_uuid_union(internal, internal) TO service_role;


--
-- Name: FUNCTION gbt_var_decompress(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_var_decompress(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_var_decompress(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_var_decompress(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_var_decompress(internal) TO service_role;


--
-- Name: FUNCTION gbt_var_fetch(internal); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.gbt_var_fetch(internal) TO postgres;
GRANT ALL ON FUNCTION public.gbt_var_fetch(internal) TO anon;
GRANT ALL ON FUNCTION public.gbt_var_fetch(internal) TO authenticated;
GRANT ALL ON FUNCTION public.gbt_var_fetch(internal) TO service_role;


--
-- Name: FUNCTION int2_dist(smallint, smallint); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.int2_dist(smallint, smallint) TO postgres;
GRANT ALL ON FUNCTION public.int2_dist(smallint, smallint) TO anon;
GRANT ALL ON FUNCTION public.int2_dist(smallint, smallint) TO authenticated;
GRANT ALL ON FUNCTION public.int2_dist(smallint, smallint) TO service_role;


--
-- Name: FUNCTION int4_dist(integer, integer); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.int4_dist(integer, integer) TO postgres;
GRANT ALL ON FUNCTION public.int4_dist(integer, integer) TO anon;
GRANT ALL ON FUNCTION public.int4_dist(integer, integer) TO authenticated;
GRANT ALL ON FUNCTION public.int4_dist(integer, integer) TO service_role;


--
-- Name: FUNCTION int8_dist(bigint, bigint); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.int8_dist(bigint, bigint) TO postgres;
GRANT ALL ON FUNCTION public.int8_dist(bigint, bigint) TO anon;
GRANT ALL ON FUNCTION public.int8_dist(bigint, bigint) TO authenticated;
GRANT ALL ON FUNCTION public.int8_dist(bigint, bigint) TO service_role;


--
-- Name: FUNCTION interval_dist(interval, interval); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.interval_dist(interval, interval) TO postgres;
GRANT ALL ON FUNCTION public.interval_dist(interval, interval) TO anon;
GRANT ALL ON FUNCTION public.interval_dist(interval, interval) TO authenticated;
GRANT ALL ON FUNCTION public.interval_dist(interval, interval) TO service_role;


--
-- Name: FUNCTION oid_dist(oid, oid); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.oid_dist(oid, oid) TO postgres;
GRANT ALL ON FUNCTION public.oid_dist(oid, oid) TO anon;
GRANT ALL ON FUNCTION public.oid_dist(oid, oid) TO authenticated;
GRANT ALL ON FUNCTION public.oid_dist(oid, oid) TO service_role;


--
-- Name: FUNCTION rls_auto_enable(); Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON FUNCTION public.rls_auto_enable() TO anon;
GRANT ALL ON FUNCTION public.rls_auto_enable() TO authenticated;
GRANT ALL ON FUNCTION public.rls_auto_enable() TO service_role;


--
-- Name: FUNCTION time_dist(time without time zone, time without time zone); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.time_dist(time without time zone, time without time zone) TO postgres;
GRANT ALL ON FUNCTION public.time_dist(time without time zone, time without time zone) TO anon;
GRANT ALL ON FUNCTION public.time_dist(time without time zone, time without time zone) TO authenticated;
GRANT ALL ON FUNCTION public.time_dist(time without time zone, time without time zone) TO service_role;


--
-- Name: FUNCTION ts_dist(timestamp without time zone, timestamp without time zone); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.ts_dist(timestamp without time zone, timestamp without time zone) TO postgres;
GRANT ALL ON FUNCTION public.ts_dist(timestamp without time zone, timestamp without time zone) TO anon;
GRANT ALL ON FUNCTION public.ts_dist(timestamp without time zone, timestamp without time zone) TO authenticated;
GRANT ALL ON FUNCTION public.ts_dist(timestamp without time zone, timestamp without time zone) TO service_role;


--
-- Name: FUNCTION tstz_dist(timestamp with time zone, timestamp with time zone); Type: ACL; Schema: public; Owner: supabase_admin
--

GRANT ALL ON FUNCTION public.tstz_dist(timestamp with time zone, timestamp with time zone) TO postgres;
GRANT ALL ON FUNCTION public.tstz_dist(timestamp with time zone, timestamp with time zone) TO anon;
GRANT ALL ON FUNCTION public.tstz_dist(timestamp with time zone, timestamp with time zone) TO authenticated;
GRANT ALL ON FUNCTION public.tstz_dist(timestamp with time zone, timestamp with time zone) TO service_role;


--
-- Name: FUNCTION apply_rls(wal jsonb, max_record_bytes integer); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO postgres;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO anon;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO authenticated;
GRANT ALL ON FUNCTION realtime.apply_rls(wal jsonb, max_record_bytes integer) TO service_role;


--
-- Name: FUNCTION authorize(role_name text, topic_name text, claims text, sub text, headers text, read_extensions text[], write_extensions text[], OUT read_allowed boolean[], OUT write_allowed boolean[]); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.authorize(role_name text, topic_name text, claims text, sub text, headers text, read_extensions text[], write_extensions text[], OUT read_allowed boolean[], OUT write_allowed boolean[]) TO postgres;
GRANT ALL ON FUNCTION realtime.authorize(role_name text, topic_name text, claims text, sub text, headers text, read_extensions text[], write_extensions text[], OUT read_allowed boolean[], OUT write_allowed boolean[]) TO dashboard_user;


--
-- Name: FUNCTION broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text) TO postgres;
GRANT ALL ON FUNCTION realtime.broadcast_changes(topic_name text, event_name text, operation text, table_name text, table_schema text, new record, old record, level text) TO dashboard_user;


--
-- Name: FUNCTION build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO postgres;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO anon;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO authenticated;
GRANT ALL ON FUNCTION realtime.build_prepared_statement_sql(prepared_statement_name text, entity regclass, columns realtime.wal_column[]) TO service_role;


--
-- Name: FUNCTION "cast"(val text, type_ regtype); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO postgres;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO dashboard_user;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO anon;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO authenticated;
GRANT ALL ON FUNCTION realtime."cast"(val text, type_ regtype) TO service_role;


--
-- Name: FUNCTION check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO postgres;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO anon;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO authenticated;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text) TO service_role;


--
-- Name: FUNCTION check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO postgres;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO anon;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO authenticated;
GRANT ALL ON FUNCTION realtime.check_equality_op(op realtime.equality_op, type_ regtype, val_1 text, val_2 text, negate boolean) TO service_role;


--
-- Name: FUNCTION filters_hash(filters realtime.user_defined_filter[]); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.filters_hash(filters realtime.user_defined_filter[]) TO postgres;
GRANT ALL ON FUNCTION realtime.filters_hash(filters realtime.user_defined_filter[]) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.filters_hash(filters realtime.user_defined_filter[]) TO anon;
GRANT ALL ON FUNCTION realtime.filters_hash(filters realtime.user_defined_filter[]) TO authenticated;
GRANT ALL ON FUNCTION realtime.filters_hash(filters realtime.user_defined_filter[]) TO service_role;


--
-- Name: FUNCTION is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO postgres;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO anon;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO authenticated;
GRANT ALL ON FUNCTION realtime.is_visible_through_filters(columns realtime.wal_column[], filters realtime.user_defined_filter[]) TO service_role;


--
-- Name: FUNCTION list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) TO postgres;
GRANT ALL ON FUNCTION realtime.list_changes(publication name, slot_name name, max_changes integer, max_record_bytes integer) TO dashboard_user;


--
-- Name: FUNCTION list_changes_sync(publication name, slot_name name, max_changes integer, max_record_bytes integer); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.list_changes_sync(publication name, slot_name name, max_changes integer, max_record_bytes integer) TO postgres;
GRANT ALL ON FUNCTION realtime.list_changes_sync(publication name, slot_name name, max_changes integer, max_record_bytes integer) TO dashboard_user;


--
-- Name: FUNCTION quote_wal2json(entity regclass); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO postgres;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO anon;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO authenticated;
GRANT ALL ON FUNCTION realtime.quote_wal2json(entity regclass) TO service_role;


--
-- Name: FUNCTION send(payload jsonb, event text, topic text, private boolean); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean) TO postgres;
GRANT ALL ON FUNCTION realtime.send(payload jsonb, event text, topic text, private boolean) TO dashboard_user;


--
-- Name: FUNCTION send_binary(payload bytea, event text, topic text, private boolean); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean) TO postgres;
GRANT ALL ON FUNCTION realtime.send_binary(payload bytea, event text, topic text, private boolean) TO dashboard_user;


--
-- Name: FUNCTION settled_changes(slot_name name, max_changes integer, VARIADIC opts text[]); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.settled_changes(slot_name name, max_changes integer, VARIADIC opts text[]) TO postgres;
GRANT ALL ON FUNCTION realtime.settled_changes(slot_name name, max_changes integer, VARIADIC opts text[]) TO dashboard_user;


--
-- Name: FUNCTION subscription_check_filters(); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO postgres;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO dashboard_user;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO anon;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO authenticated;
GRANT ALL ON FUNCTION realtime.subscription_check_filters() TO service_role;


--
-- Name: FUNCTION to_regrole(role_name text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO postgres;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO dashboard_user;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO anon;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO authenticated;
GRANT ALL ON FUNCTION realtime.to_regrole(role_name text) TO service_role;


--
-- Name: FUNCTION topic(); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.topic() TO postgres;
GRANT ALL ON FUNCTION realtime.topic() TO dashboard_user;


--
-- Name: FUNCTION wal2json_escape_identifier(name text); Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON FUNCTION realtime.wal2json_escape_identifier(name text) TO postgres;
GRANT ALL ON FUNCTION realtime.wal2json_escape_identifier(name text) TO dashboard_user;


--
-- Name: FUNCTION delete_lifecycle_objects(object_ids uuid[]); Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON FUNCTION storage.delete_lifecycle_objects(object_ids uuid[]) FROM PUBLIC;
GRANT ALL ON FUNCTION storage.delete_lifecycle_objects(object_ids uuid[]) TO postgres;
GRANT ALL ON FUNCTION storage.delete_lifecycle_objects(object_ids uuid[]) TO service_role;


--
-- Name: FUNCTION protect_active_lifecycle_states(); Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON FUNCTION storage.protect_active_lifecycle_states() FROM PUBLIC;


--
-- Name: FUNCTION reconcile_lifecycle_state(target_bucket_id text, policy_changed boolean); Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON FUNCTION storage.reconcile_lifecycle_state(target_bucket_id text, policy_changed boolean) FROM PUBLIC;
GRANT ALL ON FUNCTION storage.reconcile_lifecycle_state(target_bucket_id text, policy_changed boolean) TO postgres;
GRANT ALL ON FUNCTION storage.reconcile_lifecycle_state(target_bucket_id text, policy_changed boolean) TO service_role;


--
-- Name: FUNCTION wake_lifecycle_state(); Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON FUNCTION storage.wake_lifecycle_state() FROM PUBLIC;


--
-- Name: FUNCTION _crypto_aead_det_decrypt(message bytea, additional bytea, key_id bigint, context bytea, nonce bytea); Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT ALL ON FUNCTION vault._crypto_aead_det_decrypt(message bytea, additional bytea, key_id bigint, context bytea, nonce bytea) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION vault._crypto_aead_det_decrypt(message bytea, additional bytea, key_id bigint, context bytea, nonce bytea) TO service_role;


--
-- Name: FUNCTION create_secret(new_secret text, new_name text, new_description text, new_key_id uuid); Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT ALL ON FUNCTION vault.create_secret(new_secret text, new_name text, new_description text, new_key_id uuid) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION vault.create_secret(new_secret text, new_name text, new_description text, new_key_id uuid) TO service_role;


--
-- Name: FUNCTION update_secret(secret_id uuid, new_secret text, new_name text, new_description text, new_key_id uuid); Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT ALL ON FUNCTION vault.update_secret(secret_id uuid, new_secret text, new_name text, new_description text, new_key_id uuid) TO postgres WITH GRANT OPTION;
GRANT ALL ON FUNCTION vault.update_secret(secret_id uuid, new_secret text, new_name text, new_description text, new_key_id uuid) TO service_role;


--
-- Name: TABLE audit_log_entries; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.audit_log_entries TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.audit_log_entries TO postgres;
GRANT SELECT ON TABLE auth.audit_log_entries TO postgres WITH GRANT OPTION;


--
-- Name: TABLE custom_oauth_providers; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.custom_oauth_providers TO postgres;
GRANT ALL ON TABLE auth.custom_oauth_providers TO dashboard_user;


--
-- Name: TABLE flow_state; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.flow_state TO postgres;
GRANT SELECT ON TABLE auth.flow_state TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.flow_state TO dashboard_user;


--
-- Name: TABLE identities; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.identities TO postgres;
GRANT SELECT ON TABLE auth.identities TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.identities TO dashboard_user;


--
-- Name: TABLE instances; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.instances TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.instances TO postgres;
GRANT SELECT ON TABLE auth.instances TO postgres WITH GRANT OPTION;


--
-- Name: TABLE mfa_amr_claims; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.mfa_amr_claims TO postgres;
GRANT SELECT ON TABLE auth.mfa_amr_claims TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.mfa_amr_claims TO dashboard_user;


--
-- Name: TABLE mfa_challenges; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.mfa_challenges TO postgres;
GRANT SELECT ON TABLE auth.mfa_challenges TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.mfa_challenges TO dashboard_user;


--
-- Name: TABLE mfa_factors; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.mfa_factors TO postgres;
GRANT SELECT ON TABLE auth.mfa_factors TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.mfa_factors TO dashboard_user;


--
-- Name: TABLE mfa_recovery_code_sets; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.mfa_recovery_code_sets TO postgres;
GRANT ALL ON TABLE auth.mfa_recovery_code_sets TO dashboard_user;


--
-- Name: TABLE mfa_recovery_codes; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.mfa_recovery_codes TO postgres;
GRANT ALL ON TABLE auth.mfa_recovery_codes TO dashboard_user;


--
-- Name: TABLE oauth_authorizations; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_authorizations TO postgres;
GRANT ALL ON TABLE auth.oauth_authorizations TO dashboard_user;


--
-- Name: TABLE oauth_client_states; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_client_states TO postgres;
GRANT ALL ON TABLE auth.oauth_client_states TO dashboard_user;


--
-- Name: TABLE oauth_clients; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_clients TO postgres;
GRANT ALL ON TABLE auth.oauth_clients TO dashboard_user;


--
-- Name: TABLE oauth_consents; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.oauth_consents TO postgres;
GRANT ALL ON TABLE auth.oauth_consents TO dashboard_user;


--
-- Name: TABLE one_time_tokens; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.one_time_tokens TO postgres;
GRANT SELECT ON TABLE auth.one_time_tokens TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.one_time_tokens TO dashboard_user;


--
-- Name: TABLE refresh_tokens; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.refresh_tokens TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.refresh_tokens TO postgres;
GRANT SELECT ON TABLE auth.refresh_tokens TO postgres WITH GRANT OPTION;


--
-- Name: SEQUENCE refresh_tokens_id_seq; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON SEQUENCE auth.refresh_tokens_id_seq TO dashboard_user;
GRANT ALL ON SEQUENCE auth.refresh_tokens_id_seq TO postgres;


--
-- Name: TABLE saml_providers; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.saml_providers TO postgres;
GRANT SELECT ON TABLE auth.saml_providers TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.saml_providers TO dashboard_user;


--
-- Name: TABLE saml_relay_states; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.saml_relay_states TO postgres;
GRANT SELECT ON TABLE auth.saml_relay_states TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.saml_relay_states TO dashboard_user;


--
-- Name: TABLE schema_migrations; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT SELECT ON TABLE auth.schema_migrations TO postgres WITH GRANT OPTION;


--
-- Name: TABLE scim_tokens; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.scim_tokens TO postgres;
GRANT ALL ON TABLE auth.scim_tokens TO dashboard_user;


--
-- Name: TABLE scim_users; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.scim_users TO postgres;
GRANT ALL ON TABLE auth.scim_users TO dashboard_user;


--
-- Name: TABLE sessions; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.sessions TO postgres;
GRANT SELECT ON TABLE auth.sessions TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.sessions TO dashboard_user;


--
-- Name: TABLE sso_domains; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.sso_domains TO postgres;
GRANT SELECT ON TABLE auth.sso_domains TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.sso_domains TO dashboard_user;


--
-- Name: TABLE sso_providers; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.sso_providers TO postgres;
GRANT SELECT ON TABLE auth.sso_providers TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE auth.sso_providers TO dashboard_user;


--
-- Name: TABLE users; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.users TO dashboard_user;
GRANT INSERT,REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE auth.users TO postgres;
GRANT SELECT ON TABLE auth.users TO postgres WITH GRANT OPTION;


--
-- Name: TABLE webauthn_challenges; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.webauthn_challenges TO postgres;
GRANT ALL ON TABLE auth.webauthn_challenges TO dashboard_user;


--
-- Name: TABLE webauthn_credentials; Type: ACL; Schema: auth; Owner: supabase_auth_admin
--

GRANT ALL ON TABLE auth.webauthn_credentials TO postgres;
GRANT ALL ON TABLE auth.webauthn_credentials TO dashboard_user;


--
-- Name: TABLE pg_stat_statements; Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON TABLE extensions.pg_stat_statements FROM postgres;
GRANT ALL ON TABLE extensions.pg_stat_statements TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE extensions.pg_stat_statements TO dashboard_user;


--
-- Name: TABLE pg_stat_statements_info; Type: ACL; Schema: extensions; Owner: postgres
--

REVOKE ALL ON TABLE extensions.pg_stat_statements_info FROM postgres;
GRANT ALL ON TABLE extensions.pg_stat_statements_info TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE extensions.pg_stat_statements_info TO dashboard_user;


--
-- Name: TABLE "USER"; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public."USER" TO anon;
GRANT ALL ON TABLE public."USER" TO authenticated;
GRANT ALL ON TABLE public."USER" TO service_role;


--
-- Name: TABLE academic_year; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.academic_year TO anon;
GRANT ALL ON TABLE public.academic_year TO authenticated;
GRANT ALL ON TABLE public.academic_year TO service_role;


--
-- Name: TABLE audit_log; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.audit_log TO anon;
GRANT ALL ON TABLE public.audit_log TO authenticated;
GRANT ALL ON TABLE public.audit_log TO service_role;


--
-- Name: TABLE biometric_credential; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.biometric_credential TO anon;
GRANT ALL ON TABLE public.biometric_credential TO authenticated;
GRANT ALL ON TABLE public.biometric_credential TO service_role;


--
-- Name: TABLE cache; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.cache TO anon;
GRANT ALL ON TABLE public.cache TO authenticated;
GRANT ALL ON TABLE public.cache TO service_role;


--
-- Name: TABLE cache_locks; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.cache_locks TO anon;
GRANT ALL ON TABLE public.cache_locks TO authenticated;
GRANT ALL ON TABLE public.cache_locks TO service_role;


--
-- Name: TABLE college; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.college TO anon;
GRANT ALL ON TABLE public.college TO authenticated;
GRANT ALL ON TABLE public.college TO service_role;


--
-- Name: TABLE course; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.course TO anon;
GRANT ALL ON TABLE public.course TO authenticated;
GRANT ALL ON TABLE public.course TO service_role;


--
-- Name: TABLE dean; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.dean TO anon;
GRANT ALL ON TABLE public.dean TO authenticated;
GRANT ALL ON TABLE public.dean TO service_role;


--
-- Name: TABLE department; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.department TO anon;
GRANT ALL ON TABLE public.department TO authenticated;
GRANT ALL ON TABLE public.department TO service_role;


--
-- Name: TABLE department_chair; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.department_chair TO anon;
GRANT ALL ON TABLE public.department_chair TO authenticated;
GRANT ALL ON TABLE public.department_chair TO service_role;


--
-- Name: TABLE fac_specialization; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.fac_specialization TO anon;
GRANT ALL ON TABLE public.fac_specialization TO authenticated;
GRANT ALL ON TABLE public.fac_specialization TO service_role;


--
-- Name: TABLE faculty; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.faculty TO anon;
GRANT ALL ON TABLE public.faculty TO authenticated;
GRANT ALL ON TABLE public.faculty TO service_role;


--
-- Name: TABLE faculty_account_reviews; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.faculty_account_reviews TO anon;
GRANT ALL ON TABLE public.faculty_account_reviews TO authenticated;
GRANT ALL ON TABLE public.faculty_account_reviews TO service_role;


--
-- Name: TABLE failed_jobs; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.failed_jobs TO anon;
GRANT ALL ON TABLE public.failed_jobs TO authenticated;
GRANT ALL ON TABLE public.failed_jobs TO service_role;


--
-- Name: SEQUENCE failed_jobs_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.failed_jobs_id_seq TO anon;
GRANT ALL ON SEQUENCE public.failed_jobs_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.failed_jobs_id_seq TO service_role;


--
-- Name: TABLE historical_schedule; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.historical_schedule TO anon;
GRANT ALL ON TABLE public.historical_schedule TO authenticated;
GRANT ALL ON TABLE public.historical_schedule TO service_role;


--
-- Name: TABLE institution; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.institution TO anon;
GRANT ALL ON TABLE public.institution TO authenticated;
GRANT ALL ON TABLE public.institution TO service_role;


--
-- Name: TABLE job_batches; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.job_batches TO anon;
GRANT ALL ON TABLE public.job_batches TO authenticated;
GRANT ALL ON TABLE public.job_batches TO service_role;


--
-- Name: TABLE jobs; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.jobs TO anon;
GRANT ALL ON TABLE public.jobs TO authenticated;
GRANT ALL ON TABLE public.jobs TO service_role;


--
-- Name: SEQUENCE jobs_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.jobs_id_seq TO anon;
GRANT ALL ON SEQUENCE public.jobs_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.jobs_id_seq TO service_role;


--
-- Name: TABLE migrations; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.migrations TO anon;
GRANT ALL ON TABLE public.migrations TO authenticated;
GRANT ALL ON TABLE public.migrations TO service_role;


--
-- Name: SEQUENCE migrations_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.migrations_id_seq TO anon;
GRANT ALL ON SEQUENCE public.migrations_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.migrations_id_seq TO service_role;


--
-- Name: TABLE notification; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.notification TO anon;
GRANT ALL ON TABLE public.notification TO authenticated;
GRANT ALL ON TABLE public.notification TO service_role;


--
-- Name: TABLE password_reset_tokens; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.password_reset_tokens TO anon;
GRANT ALL ON TABLE public.password_reset_tokens TO authenticated;
GRANT ALL ON TABLE public.password_reset_tokens TO service_role;


--
-- Name: TABLE personal_access_tokens; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.personal_access_tokens TO anon;
GRANT ALL ON TABLE public.personal_access_tokens TO authenticated;
GRANT ALL ON TABLE public.personal_access_tokens TO service_role;


--
-- Name: SEQUENCE personal_access_tokens_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.personal_access_tokens_id_seq TO anon;
GRANT ALL ON SEQUENCE public.personal_access_tokens_id_seq TO authenticated;
GRANT ALL ON SEQUENCE public.personal_access_tokens_id_seq TO service_role;


--
-- Name: TABLE room; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.room TO anon;
GRANT ALL ON TABLE public.room TO authenticated;
GRANT ALL ON TABLE public.room TO service_role;


--
-- Name: TABLE schedule; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.schedule TO anon;
GRANT ALL ON TABLE public.schedule TO authenticated;
GRANT ALL ON TABLE public.schedule TO service_role;


--
-- Name: TABLE schedule_archives; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.schedule_archives TO anon;
GRANT ALL ON TABLE public.schedule_archives TO authenticated;
GRANT ALL ON TABLE public.schedule_archives TO service_role;


--
-- Name: TABLE schedule_submission; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.schedule_submission TO anon;
GRANT ALL ON TABLE public.schedule_submission TO authenticated;
GRANT ALL ON TABLE public.schedule_submission TO service_role;


--
-- Name: TABLE section; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.section TO anon;
GRANT ALL ON TABLE public.section TO authenticated;
GRANT ALL ON TABLE public.section TO service_role;


--
-- Name: TABLE semester; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.semester TO anon;
GRANT ALL ON TABLE public.semester TO authenticated;
GRANT ALL ON TABLE public.semester TO service_role;


--
-- Name: TABLE sessions; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.sessions TO anon;
GRANT ALL ON TABLE public.sessions TO authenticated;
GRANT ALL ON TABLE public.sessions TO service_role;


--
-- Name: TABLE study_load; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.study_load TO anon;
GRANT ALL ON TABLE public.study_load TO authenticated;
GRANT ALL ON TABLE public.study_load TO service_role;


--
-- Name: TABLE system_backup; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.system_backup TO anon;
GRANT ALL ON TABLE public.system_backup TO authenticated;
GRANT ALL ON TABLE public.system_backup TO service_role;


--
-- Name: TABLE system_setting; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.system_setting TO anon;
GRANT ALL ON TABLE public.system_setting TO authenticated;
GRANT ALL ON TABLE public.system_setting TO service_role;


--
-- Name: TABLE token_blacklist; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.token_blacklist TO anon;
GRANT ALL ON TABLE public.token_blacklist TO authenticated;
GRANT ALL ON TABLE public.token_blacklist TO service_role;


--
-- Name: TABLE webauthn_credentials; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.webauthn_credentials TO anon;
GRANT ALL ON TABLE public.webauthn_credentials TO authenticated;
GRANT ALL ON TABLE public.webauthn_credentials TO service_role;


--
-- Name: TABLE workload; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.workload TO anon;
GRANT ALL ON TABLE public.workload TO authenticated;
GRANT ALL ON TABLE public.workload TO service_role;


--
-- Name: TABLE messages; Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLE realtime.messages TO postgres;
GRANT SELECT,INSERT ON TABLE realtime.messages TO postgres WITH GRANT OPTION;
GRANT ALL ON TABLE realtime.messages TO dashboard_user;
GRANT SELECT,INSERT,UPDATE ON TABLE realtime.messages TO anon;
GRANT SELECT,INSERT,UPDATE ON TABLE realtime.messages TO authenticated;
GRANT SELECT,INSERT,UPDATE ON TABLE realtime.messages TO service_role;


--
-- Name: TABLE subscription; Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON TABLE realtime.subscription TO postgres;
GRANT ALL ON TABLE realtime.subscription TO dashboard_user;
GRANT SELECT ON TABLE realtime.subscription TO anon;
GRANT SELECT ON TABLE realtime.subscription TO authenticated;
GRANT SELECT ON TABLE realtime.subscription TO service_role;


--
-- Name: SEQUENCE subscription_id_seq; Type: ACL; Schema: realtime; Owner: supabase_realtime_admin
--

GRANT ALL ON SEQUENCE realtime.subscription_id_seq TO postgres;
GRANT ALL ON SEQUENCE realtime.subscription_id_seq TO dashboard_user;
GRANT USAGE ON SEQUENCE realtime.subscription_id_seq TO anon;
GRANT USAGE ON SEQUENCE realtime.subscription_id_seq TO authenticated;
GRANT USAGE ON SEQUENCE realtime.subscription_id_seq TO service_role;


--
-- Name: TABLE bucket_lifecycle_states; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.bucket_lifecycle_states TO postgres;
GRANT ALL ON TABLE storage.bucket_lifecycle_states TO service_role;


--
-- Name: TABLE buckets; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON TABLE storage.buckets FROM supabase_storage_admin;
GRANT ALL ON TABLE storage.buckets TO supabase_storage_admin WITH GRANT OPTION;
GRANT ALL ON TABLE storage.buckets TO service_role;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE storage.buckets TO authenticated;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE storage.buckets TO anon;
GRANT ALL ON TABLE storage.buckets TO postgres WITH GRANT OPTION;


--
-- Name: TABLE buckets_analytics; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.buckets_analytics TO service_role;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE storage.buckets_analytics TO authenticated;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE storage.buckets_analytics TO anon;


--
-- Name: TABLE buckets_vectors; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT SELECT ON TABLE storage.buckets_vectors TO service_role;
GRANT SELECT ON TABLE storage.buckets_vectors TO anon;
GRANT SELECT ON TABLE storage.buckets_vectors TO authenticated;


--
-- Name: TABLE lifecycle_abandoned_artifacts; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.lifecycle_abandoned_artifacts TO postgres;
GRANT ALL ON TABLE storage.lifecycle_abandoned_artifacts TO service_role;


--
-- Name: TABLE objects; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

REVOKE ALL ON TABLE storage.objects FROM supabase_storage_admin;
GRANT ALL ON TABLE storage.objects TO supabase_storage_admin WITH GRANT OPTION;
GRANT ALL ON TABLE storage.objects TO service_role;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE storage.objects TO authenticated;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE storage.objects TO anon;
GRANT ALL ON TABLE storage.objects TO postgres WITH GRANT OPTION;


--
-- Name: TABLE s3_multipart_uploads; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.s3_multipart_uploads TO service_role;
GRANT SELECT ON TABLE storage.s3_multipart_uploads TO authenticated;
GRANT SELECT ON TABLE storage.s3_multipart_uploads TO anon;


--
-- Name: TABLE s3_multipart_uploads_parts; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT ALL ON TABLE storage.s3_multipart_uploads_parts TO service_role;
GRANT SELECT ON TABLE storage.s3_multipart_uploads_parts TO authenticated;
GRANT SELECT ON TABLE storage.s3_multipart_uploads_parts TO anon;


--
-- Name: TABLE vector_indexes; Type: ACL; Schema: storage; Owner: supabase_storage_admin
--

GRANT SELECT ON TABLE storage.vector_indexes TO service_role;
GRANT SELECT ON TABLE storage.vector_indexes TO anon;
GRANT SELECT ON TABLE storage.vector_indexes TO authenticated;


--
-- Name: TABLE secrets; Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT SELECT,REFERENCES,DELETE,TRUNCATE ON TABLE vault.secrets TO postgres WITH GRANT OPTION;
GRANT SELECT,DELETE ON TABLE vault.secrets TO service_role;


--
-- Name: TABLE decrypted_secrets; Type: ACL; Schema: vault; Owner: supabase_admin
--

GRANT SELECT,REFERENCES,DELETE,TRUNCATE ON TABLE vault.decrypted_secrets TO postgres WITH GRANT OPTION;
GRANT SELECT,DELETE ON TABLE vault.decrypted_secrets TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: auth; Owner: supabase_auth_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON SEQUENCES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: auth; Owner: supabase_auth_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON FUNCTIONS TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: auth; Owner: supabase_auth_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_auth_admin IN SCHEMA auth GRANT ALL ON TABLES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: extensions; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA extensions GRANT ALL ON SEQUENCES TO postgres WITH GRANT OPTION;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: extensions; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA extensions GRANT ALL ON FUNCTIONS TO postgres WITH GRANT OPTION;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: extensions; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA extensions GRANT ALL ON TABLES TO postgres WITH GRANT OPTION;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: graphql; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: graphql; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: graphql; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: graphql_public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: graphql_public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: graphql_public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA graphql_public GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA public GRANT ALL ON TABLES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: realtime; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON SEQUENCES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: realtime; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON FUNCTIONS TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: realtime; Owner: supabase_admin
--

ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT REFERENCES,DELETE,TRIGGER,TRUNCATE,MAINTAIN,UPDATE ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT SELECT,INSERT ON TABLES TO postgres WITH GRANT OPTION;
ALTER DEFAULT PRIVILEGES FOR ROLE supabase_admin IN SCHEMA realtime GRANT ALL ON TABLES TO dashboard_user;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: storage; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON SEQUENCES TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: storage; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON FUNCTIONS TO service_role;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: storage; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO anon;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA storage GRANT ALL ON TABLES TO service_role;


--
-- Name: ensure_rls; Type: EVENT TRIGGER; Schema: -; Owner: postgres
--

CREATE EVENT TRIGGER ensure_rls ON ddl_command_end
         WHEN TAG IN ('CREATE TABLE', 'CREATE TABLE AS', 'SELECT INTO')
   EXECUTE FUNCTION public.rls_auto_enable();


ALTER EVENT TRIGGER ensure_rls OWNER TO postgres;

--
-- Name: issue_graphql_placeholder; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_graphql_placeholder ON sql_drop
         WHEN TAG IN ('DROP EXTENSION')
   EXECUTE FUNCTION extensions.set_graphql_placeholder();


ALTER EVENT TRIGGER issue_graphql_placeholder OWNER TO supabase_admin;

--
-- Name: issue_pg_cron_access; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_pg_cron_access ON ddl_command_end
         WHEN TAG IN ('CREATE EXTENSION')
   EXECUTE FUNCTION extensions.grant_pg_cron_access();


ALTER EVENT TRIGGER issue_pg_cron_access OWNER TO supabase_admin;

--
-- Name: issue_pg_graphql_access; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_pg_graphql_access ON ddl_command_end
         WHEN TAG IN ('CREATE EXTENSION')
   EXECUTE FUNCTION extensions.grant_pg_graphql_access();


ALTER EVENT TRIGGER issue_pg_graphql_access OWNER TO supabase_admin;

--
-- Name: issue_pg_net_access; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER issue_pg_net_access ON ddl_command_end
         WHEN TAG IN ('CREATE EXTENSION')
   EXECUTE FUNCTION extensions.grant_pg_net_access();


ALTER EVENT TRIGGER issue_pg_net_access OWNER TO supabase_admin;

--
-- Name: pgrst_ddl_watch; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER pgrst_ddl_watch ON ddl_command_end
   EXECUTE FUNCTION extensions.pgrst_ddl_watch();


ALTER EVENT TRIGGER pgrst_ddl_watch OWNER TO supabase_admin;

--
-- Name: pgrst_drop_watch; Type: EVENT TRIGGER; Schema: -; Owner: supabase_admin
--

CREATE EVENT TRIGGER pgrst_drop_watch ON sql_drop
   EXECUTE FUNCTION extensions.pgrst_drop_watch();


ALTER EVENT TRIGGER pgrst_drop_watch OWNER TO supabase_admin;

--
-- PostgreSQL database dump complete
--

\unrestrict UseyCe6xbVQn9mXIwjtWaiMlgUTxZIg1vkhBOfNgbjZELjbaHIQD7f6L4tbTCT0

