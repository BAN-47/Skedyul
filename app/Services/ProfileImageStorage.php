<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileImageStorage
{
    public static function url(?string $storedValue, string $directory): ?string
    {
        if (!$storedValue) {
            return null;
        }

        if (filter_var($storedValue, FILTER_VALIDATE_URL)) {
            return $storedValue;
        }

        $filename = basename($storedValue);
        $key = trim($directory, '/').'/'.$filename;
        $diskName = (string) config('filesystems.profile_images_disk', 'public');

        // Continue displaying images uploaded before the switch to managed storage.
        $legacyPath = public_path('images/'.trim($directory, '/').'/'.$filename);
        if (config('filesystems.disks.'.$diskName.'.driver') === 'local' && is_file($legacyPath)) {
            return asset('images/'.trim($directory, '/').'/'.$filename);
        }

        return Storage::disk($diskName)->url($key);
    }

    public static function store(UploadedFile $file, string $directory, string $filename): bool
    {
        return Storage::disk((string) config('filesystems.profile_images_disk', 'public'))
            // Laravel Cloud Object Storage sets visibility per bucket and rejects
            // per-object ACL options. The configured avatar bucket must be public.
            ->putFileAs(trim($directory, '/'), $file, $filename);
    }

    public static function delete(?string $storedValue, string $directory): void
    {
        if (!$storedValue) {
            return;
        }

        $filename = basename($storedValue);
        $key = trim($directory, '/').'/'.$filename;
        Storage::disk((string) config('filesystems.profile_images_disk', 'public'))->delete($key);

        $legacyPath = public_path('images/'.trim($directory, '/').'/'.$filename);
        if (is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }
}
