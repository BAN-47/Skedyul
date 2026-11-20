<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AdminIssueAttachmentStorage
{
    private const DIRECTORY = 'admin-issue-reports';

    public function store(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $filename = (string) Str::uuid() . '.' . $extension;
        $stored = Storage::disk($this->diskName())->putFileAs(self::DIRECTORY, $file, $filename);

        if (!$stored) {
            throw new RuntimeException('The screenshot could not be saved. Please try again.');
        }

        return $filename;
    }

    public function delete(?string $filename): void
    {
        if ($filename && preg_match('/^[0-9a-f-]{36}\.(jpg|jpeg|png|webp)$/i', $filename)) {
            Storage::disk($this->diskName())->delete(self::DIRECTORY . '/' . $filename);
        }
    }

    public function response(string $filename)
    {
        abort_unless(preg_match('/^[0-9a-f-]{36}\.(jpg|jpeg|png|webp)$/i', $filename), 404);

        $path = self::DIRECTORY . '/' . $filename;
        $disk = Storage::disk($this->diskName());
        abort_unless($disk->exists($path), 404);

        $mime = match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };

        return response($disk->get($path), 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="report-screenshot.' . pathinfo($filename, PATHINFO_EXTENSION) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    private function diskName(): string
    {
        return (string) config('filesystems.private_uploads_disk', 'local');
    }
}
