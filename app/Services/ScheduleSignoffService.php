<?php

namespace App\Services;

use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class ScheduleSignoffService
{
    public function names(): array
    {
        $user = Auth::user();
        $chair = $user
            ? Dept_Chair::with(['user.faculty', 'program'])->where('dc_usr_id', $user->usr_id)->first()
            : null;

        $formatter = app(ScheduleSignatoryFormatter::class);
        $preparedBy = $formatter->formatRoleName($chair, 'dc_');
        if ($preparedBy === '' && $user) {
            $preparedBy = trim(implode(' ', array_filter([
                $user->usr_first_name,
                $user->usr_middle_name,
                $user->usr_last_name,
                $user->usr_suffix,
            ])));
        }

        $collegeId = $chair?->dc_college_id ?? $chair?->program?->dept_college_id;

        $dean = $collegeId
            ? Dean::with('user.faculty')->where('dean_college_id', $collegeId)->first()
            : null;
        $collegeCode = $dean?->department?->college_code ?? $chair?->program?->department?->college_code;

        return [
            'prepared_by' => $preparedBy ?: 'Chair name unavailable',
            'reviewed_by' => $formatter->formatRoleName($dean, 'dean_') ?: 'Dean not assigned',
            'reviewed_title' => 'Director / Dean' . ($collegeCode ? ', ' . $collegeCode : ''),
            'campus_director' => trim((string) SystemSetting::get('campus_director_name', '')),
        ];
    }
}
