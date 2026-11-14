<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class EnsureFacultyEducationComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $faculty = $user?->faculty;

        $missingDegreeAbbreviation = $faculty && (
            (filled($faculty->fac_bachelors_degree) && blank($faculty->fac_bachelors_abbr))
            || (filled($faculty->fac_masters_degree) && blank($faculty->fac_masters_abbr))
            || (filled($faculty->fac_doctorate_degree) && blank($faculty->fac_doctorate_abbr))
        );
        $missingRequiredMaster = $faculty
            && $faculty->fac_employment_type === 'full_time'
            && blank($faculty->fac_masters_degree);

        if ($faculty && (blank($faculty->fac_bachelors_degree) || blank($faculty->fac_bachelors_abbr)
            || $missingDegreeAbbreviation || $missingRequiredMaster)) {
            return redirect()->to(URL::temporarySignedRoute(
                'faculty.education.edit',
                now()->addDays(7),
                ['facultyId' => $faculty->fac_id]
            ))->with('warning', 'Complete your degree details and acronyms before accessing your dashboard.');
        }

        return $next($request);
    }
}
