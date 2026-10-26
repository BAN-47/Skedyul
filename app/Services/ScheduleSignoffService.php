<?php

namespace App\Services;

use App\Models\College;
use App\Models\Dean;
use App\Models\Dept_Chair;
use Illuminate\Support\Facades\Auth;

class ScheduleSignoffService
{
    public function names(): array
    {
        $user = Auth::user();
        $chair = $user
            ? Dept_Chair::where('dc_usr_id', $user->usr_id)->first()
            : null;

        $preparedParts = array_filter([
            $chair?->dc_first_name ?? $user?->usr_first_name,
            $chair?->dc_middle_name ?? $user?->usr_middle_name,
            $chair?->dc_last_name ?? $user?->usr_last_name,
            $chair?->dc_suffix ?? $user?->usr_suffix,
        ]);

        $ccictCollegeId = College::query()
            ->whereRaw('UPPER(TRIM(college_code)) = ?', ['CCICT'])
            ->value('college_id');

        $dean = $ccictCollegeId
            ? Dean::where('dean_college_id', $ccictCollegeId)->first()
            : null;

        $deanParts = array_filter([
            $dean?->dean_first_name,
            $dean?->dean_middle_name,
            $dean?->dean_last_name,
            $dean?->dean_suffix,
        ]);

        return [
            'prepared_by' => implode(' ', $preparedParts) ?: 'Chair name unavailable',
            'reviewed_by' => implode(' ', $deanParts) ?: 'CCICT Dean not assigned',
        ];
    }
}
