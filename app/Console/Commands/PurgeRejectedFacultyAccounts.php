<?php

namespace App\Console\Commands;

use App\Models\FacultyAccountReview;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeRejectedFacultyAccounts extends Command
{
    protected $signature = 'app:purge-rejected-faculty-accounts';

    protected $description = 'Delete rejected faculty review history and ID photos older than 30 days';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);
        $deleted = 0;

        FacultyAccountReview::query()
            ->where('fvr_status', 'rejected')
            ->whereNotNull('fvr_reviewed_at')
            ->where('fvr_reviewed_at', '<', $cutoff)
            ->chunkById(100, function ($reviews) use (&$deleted) {
                foreach ($reviews as $review) {
                    if ($review->fvr_id_photo_path) {
                        Storage::disk('local')->delete($review->fvr_id_photo_path);
                    }
                    $review->delete();
                    $deleted++;
                }
            }, 'fvr_id');

        $this->info("Deleted {$deleted} rejected faculty review record(s).");

        return self::SUCCESS;
    }
}
