<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ExportTemplateStorage
{
    /** Return a local path for spreadsheet and Word libraries, copying remote files to temp storage when needed. */
    public function localPath(string $filename): ?string
    {
        $filename = basename($filename);
        $diskName = (string) config('filesystems.export_templates_disk', 'templates');
        $prefix = trim((string) config('filesystems.export_templates_prefix', ''), '/');
        $key = ($prefix !== '' ? $prefix.'/' : '').$filename;
        $disk = Storage::disk($diskName);

        if (!$disk->exists($key)) {
            return null;
        }

        if (config('filesystems.disks.'.$diskName.'.driver') === 'local') {
            $path = $disk->path($key);

            return is_file($path) ? $path : null;
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'skedyul-template-');
        if ($temporaryPath === false || file_put_contents($temporaryPath, $disk->get($key)) === false) {
            if (is_string($temporaryPath) && is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }

            return null;
        }

        register_shutdown_function(static function () use ($temporaryPath): void {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        });

        return $temporaryPath;
    }
}
