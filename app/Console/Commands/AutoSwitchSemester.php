<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\AcademicYearController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoSwitchSemester extends Command
{
    protected $signature = 'schedule:auto-switch-semester';
    protected $description = 'Auto-switch semester (1st→2nd, 2nd→archive) based on end dates';

    public function handle(): int
    {
        try {
            $controller = app(AcademicYearController::class);
            $msg = $controller->autoSwitchSemester();

            if ($msg) {
                $this->info($msg);
                Log::info('AutoSwitchSemester: ' . $msg);
            } else {
                $this->comment('No semester switch needed.');
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('AutoSwitchSemester failed: ' . $e->getMessage());
            Log::error('AutoSwitchSemester failed', ['exception' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}