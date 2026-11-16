# Laravel Cloud deployment checklist

This application currently uses the existing Supabase PostgreSQL database. Treat the first Cloud deployment as a staging deployment and do not run migrations against production until the schema and migration history have been compared.

## 1. Prepare the repository

1. Push the project to a Git provider supported by Laravel Cloud.
2. Confirm `.env` is not committed. Configure secrets in Laravel Cloud, not in Git.
   The repository currently tracks `skedyul_backup.sql`; inspect it for real records or credentials before pushing/deploying. A database dump with personal data should not be shipped as application source.
3. Keep the included `.env.example` as a key list only; never copy local credentials into it.
4. Keep export templates together under the `templates/` prefix in the private object-storage bucket.
5. Install dependencies with `composer install` and build assets with `npm run build` before deploying.

Before using Laravel Cloud Object Storage, add its required adapter locally and commit the resulting `composer.json` and `composer.lock`:

```sh
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

This dependency could not be downloaded in the current environment because Packagist timed out, so the object-storage code is not deploy-ready until that command succeeds and the tests/build pass.

## 2. Create a staging Cloud environment

1. Create a Laravel Cloud application from the repository and select PHP 8.2 or newer.
2. Create a staging environment and connect a staging database if available. Do not point it at production for initial verification.
3. If using the existing Supabase project, configure its PostgreSQL host, port, database, username, password, and SSL requirement as Cloud secrets/environment variables. Prefer a staging Supabase project or a restored database copy.
4. Attach a Valkey/Redis cache resource, or choose another shared cache/session backend. Set session and cache drivers to the matching Cloud resource.
5. Attach two Laravel Object Storage buckets:
   - Public bucket with disk name `skedyul_public` for profile images.
   - Private bucket with disk name `skedyul_private` for faculty ID photos and export templates.
   Upload the existing files before enabling these disks, preserving the current folder names. Put templates under `templates/`.
6. Configure `PROFILE_IMAGES_DISK=skedyul_public`, `PRIVATE_UPLOADS_DISK=skedyul_private`, `EXPORT_TEMPLATES_DISK=skedyul_private`, and `EXPORT_TEMPLATES_PREFIX=templates`.
7. Set production `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, WebAuthn RP ID and HTTPS origin, mail credentials, and CAPTCHA keys if enabled. Store passwords, API secrets, and keys as Cloud secrets.

Laravel Cloud Object Storage uses bucket-level visibility. The public avatar bucket must be public; the ID-photo/template bucket must be private. Do not set per-object visibility ACLs.

## 3. Configure build and deploy commands

- Build: `composer install --no-dev --optimize-autoloader && npm run build`
- First staging deploy command: leave migrations disabled until schema checks are complete. Laravel Cloud’s default `php artisan migrate --force` would run migrations against the configured database.
- After a safe migration plan is approved and verified on a database copy, configure the deploy command to run the reviewed migrations.

The Composer setup script intentionally no longer runs database migrations automatically.

## 4. Verify staging before production

Run the full test suite and production build, then exercise sign-in and role boundaries, schedule plotting/edit/delete/clear rules (PBS and PBT separately), cross-department filters and exports, MIS export, profile-image upload, private ID-photo review, password reset/mail, WebAuthn registration/login on the deployed HTTPS domain, semester switching, and scheduled tasks. Confirm exported files open and include the expected schedule data. Check Cloud deployment logs and Laravel logs for errors.

Only after those checks pass should you switch the production domain/database, upload final object-storage contents, and enable the reviewed deploy migration command.
