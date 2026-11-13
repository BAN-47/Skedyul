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

        if ($faculty && blank($faculty->fac_bachelors_degree)) {
            return redirect()->to(URL::temporarySignedRoute(
                'faculty.education.edit',
                now()->addDays(7),
                ['facultyId' => $faculty->fac_id]
            ))->with('warning', 'Complete your education information before accessing your dashboard.');
        }

        return $next($request);
    }
}
