<?php

namespace App\Console\Commands;

use App\Models\SystemBackup;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Backs up the Supabase Postgres database using pg_dump';

    public function handle()
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "backup_{$timestamp}.sql";
        $path = storage_path("app/backups/{$filename}");

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');

        $process = new Process([
            'pg_dump',
            '-h', $host,
            '-p', $port,
            '-U', $username,
            '-d', $database,
            '-f', $path,
            '--no-password',
        ]);

        $process->setEnv(['PGPASSWORD' => $password]);
        $process->setTimeout(300);
        $process->run();

        if ($process->isSuccessful()) {
            SystemBackup::create([
                'bkp_file_path' => $path,
                'bkp_status' => 'success',
                'bkp_ran_at' => now(),
            ]);
            $this->info('Backup completed successfully.');
        } else {
            SystemBackup::create([
                'bkp_file_path' => $path,
                'bkp_status' => 'failed',
                'bkp_error_message' => $process->getErrorOutput(),
                'bkp_ran_at' => now(),
            ]);
            $this->error('Backup failed: ' . $process->getErrorOutput());
        }
    }
}