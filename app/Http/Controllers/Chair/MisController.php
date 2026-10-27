<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Departments;
use App\Models\College;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Carbon;

/**
 * MIS — Class Program for MIS
 *
 * Rows come from schedules already plotted in PBS/PBT.
 * MIS code is typed manually and saved to schedule.sch_mis_code.
 */
class MisController extends Controller
{
    private function chairRecord(): ?Dept_Chair
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return Dept_Chair::where('dc_usr_id', $user->usr_id)->first();
    }

    private function chairDeptId(): ?string
    {
        return $this->chairRecord()?->dc_dept_id;
    }

    private function activeSemester(): ?object
    {
        $sem = DB::table('semester')->where('sem_is_active', true)->first();
        if (!$sem) {
            return null;
        }

        $ay = DB::table('academic_year')->where('ay_is_active', true)->first();
        $year = $ay->ay_academic_year ?? $ay->ay_year_label ?? '';
        $yearDisp = $year !== '' ? str_replace('-', ' - ', $year) : '';
        $sem->label = trim(($sem->sem_name ?? '') . ($yearDisp !== '' ? ', AY ' . $yearDisp : ''));
        $sem->ay_academic_year = $year;

        return $sem;
    }

    /** Build display name: "First M. Last, Suffix" */
    private function personName(?object $row, string $prefix): string
    {
        if (!$row) {
            return '';
        }
        $first  = trim((string) ($row->{"{$prefix}first_name"} ?? ''));
        $middle = trim((string) ($row->{"{$prefix}middle_name"} ?? ''));
        $last   = trim((string) ($row->{"{$prefix}last_name"} ?? ''));
        $suffix = trim((string) ($row->{"{$prefix}suffix"} ?? ''));

        $middlePart = $middle !== '' ? (mb_strtoupper(mb_substr($middle, 0, 1)) . '.') : '';
        $parts = array_filter([$first, $middlePart, $last]);
        $name = implode(' ', $parts);
        if ($suffix !== '') {
            $name .= ', ' . $suffix;
        }

        return $name;
    }

    public function index(Request $request)
    {
        $chair = $this->chairRecord();
        $deptId = $chair?->dc_dept_id;
        $activeSem = $this->activeSemester();
        $shift = $request->query('shift', 'day') === 'night' ? 'night' : 'day';
        $sectionId = $request->query('section');

        // Signatories
        $chairName = $this->personName($chair, 'dc_');
        if ($chairName === '' && Auth::user()) {
            $chairName = Auth::user()->usr_name ?? '';
        }

        $dean = null;
        $deanName = '';
        $collegeId = $chair?->dc_college_id;
        if (!$collegeId && $deptId) {
            $collegeId = Departments::where('dept_id', $deptId)->value('dept_college_id');
        }
        if ($collegeId) {
            $dean = Dean::where('dean_college_id', $collegeId)->first();
            $deanName = $this->personName($dean, 'dean_');
        }

        $campusDirector = SystemSetting::get('campus_director_name', '');

        if (!$deptId) {
            return view('chair.mis', [
                'sections'        => collect(),
                'groupedRows'     => collect(),
                'activeSemester'  => $activeSem,
                'chairDepartment' => null,
                'filters'         => ['section' => null, 'shift' => $shift],
                'signatories'     => [
                    'chair_name'       => $chairName,
                    'chair_title'      => 'Dept. Chair',
                    'dean_name'        => $deanName,
                    'dean_title'       => 'Dean',
                    'campus_director'  => $campusDirector,
                ],
                'error'           => 'Your account is not linked to a department. Contact the administrator.',
            ]);
        }

        $chairDepartment = Departments::where('dept_id', $deptId)->first();

        $sections = Section::query()
            ->where('sec_dept_id', $deptId)
            ->orderBy('sec_name')
            ->get();

        $schedules = collect();
        if ($activeSem?->sem_id) {
            $query = Schedule::query()
                ->where('sch_sem_id', $activeSem->sem_id)
                ->where('sch_is_active', true)
                ->whereHas('section', fn($q) => $q->where('sec_dept_id', $deptId))
                ->with(['subject', 'course', 'faculty', 'section', 'room'])
                ->orderBy('sch_day')
                ->orderBy('sch_start_time');

            if ($sectionId) {
                $owns = $sections->contains('sec_id', $sectionId);
                if ($owns) {
                    $query->where('sch_sec_id', $sectionId);
                }
            }

            $schedules = $query->get();

            $schedules = $schedules->filter(function ($s) use ($shift) {
                try {
                    $start = Carbon::parse($s->sch_start_time);
                    $hour = (int) $start->format('G');
                } catch (\Throwable $e) {
                    return true;
                }
                if ($shift === 'night') {
                    return $hour >= 16;
                }
                return $hour < 16;
            })->values();
        }

        $groupedRows = $schedules->groupBy(fn($s) => optional($s->section)->sec_name ?? 'Unassigned');

        return view('chair.mis', [
            'sections'        => $sections,
            'groupedRows'     => $groupedRows,
            'activeSemester'  => $activeSem,
            'chairDepartment' => $chairDepartment,
            'filters'         => [
                'section' => $sectionId,
                'shift'   => $shift,
            ],
            'signatories'     => [
                'chair_name'      => $chairName,
                'chair_title'     => 'Chair, ' . ($chairDepartment->dept_code ?? 'Department'),
                'dean_name'       => $deanName,
                'dean_title'      => 'Dean',
                'campus_director' => $campusDirector,
            ],
            'error'           => null,
        ]);
    }

    /** Download the currently filtered class program as a Word document. */
    public function exportWord(Request $request)
    {
        // Reuse the MIS page query so section and Day/Evening filters stay in sync.
        $page = $this->index($request);
        $data = $page->getData();

        $word = new PhpWord();
        $word->setDefaultFontName('Arial');
        $word->setDefaultFontSize(9);
        $section = $word->addSection([
            'orientation' => 'landscape',
            'paperSize' => 'A4',
            'marginLeft' => 600,
            'marginRight' => 600,
            'marginTop' => 650,
            'marginBottom' => 650,
        ]);

        $department = $data['chairDepartment'] ?? null;
        $semester = $data['activeSemester'] ?? null;
        $filters = $data['filters'] ?? ['shift' => 'day', 'section' => null];
        $sections = $data['sections'] ?? collect();
        $groupedRows = $data['groupedRows'] ?? collect();
        $signatories = $data['signatories'] ?? [];
        $isEvening = ($filters['shift'] ?? 'day') === 'night';
        $selectedSection = $sections->firstWhere('sec_id', $filters['section'] ?? null);

        $section->addText('REPUBLIC OF THE PHILIPPINES', ['size' => 9], ['alignment' => 'center']);
        $section->addText('CEBU TECHNOLOGICAL UNIVERSITY', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addText('MAIN CAMPUS', ['bold' => true, 'size' => 9], ['alignment' => 'center']);
        $section->addText('COLLEGE OF COMPUTER, INFORMATION AND COMMUNICATIONS TECHNOLOGY', ['bold' => true, 'size' => 9], ['alignment' => 'center', 'spaceAfter' => 180]);
        $section->addText('CLASS PROGRAM FOR MIS', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $meta = trim(($isEvening ? 'Evening Program' : 'Day Program') . ' · ' . ($semester->label ?? 'No active semester') . ' · ' . ($selectedSection->sec_name ?? 'All Sections'));
        $section->addText($meta, ['size' => 9], ['alignment' => 'center', 'spaceAfter' => 260]);

        $headers = ['MIS CODE', 'COURSE NO.', 'DESCRIPTIVE TITLE', 'TIME', 'DAY', 'LEC', 'LAB', 'UNIT', 'ROOM', 'INSTRUCTOR'];
        $widths = [950, 1150, 3900, 1800, 1250, 550, 550, 650, 1500, 1900];
        $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 65, 'alignment' => 'center'];
        $headerStyle = ['bold' => true, 'size' => 8];
        $cellStyle = ['size' => 8];
        $paragraph = ['spaceAfter' => 0, 'spaceBefore' => 0];
        $cell = ['valign' => 'center'];

        foreach ($groupedRows as $sectionName => $rows) {
            $section->addText((string) $sectionName, ['bold' => true, 'size' => 11], ['spaceBefore' => 140, 'spaceAfter' => 80]);
            $table = $section->addTable($tableStyle);
            $table->addRow(null, ['tblHeader' => true]);
            foreach ($headers as $i => $label) {
                $table->addCell($widths[$i], array_merge($cell, ['bgColor' => 'D9EAF7']))->addText($label, $headerStyle, $paragraph);
            }
            foreach ($rows as $schedule) {
                $course = $schedule->course ?? $schedule->subject;
                $courseCode = $course->course_code ?? $course->subj_code ?? '—';
                $title = $course->course_name ?? $course->subj_name ?? '—';
                $lec = $course->course_lecture_hours ?? $course->subj_lecture_hours ?? 0;
                $lab = $course->course_lab_hours ?? $course->subj_lab_hours ?? 0;
                $unit = $course->course_units ?? 0;
                try {
                    $time = Carbon::parse($schedule->sch_start_time)->format('g:i A') . ' – ' . Carbon::parse($schedule->sch_end_time)->format('g:i A');
                } catch (\Throwable $e) {
                    $time = trim(($schedule->sch_start_time ?? '') . ' – ' . ($schedule->sch_end_time ?? ''));
                }
                $instructor = $schedule->faculty
                    ? trim(($schedule->faculty->fac_first_name ?? '') . ' ' . ($schedule->faculty->fac_last_name ?? ''))
                    : '—';
                $values = [
                    $schedule->sch_mis_code ?: '',
                    $courseCode,
                    $title,
                    $time,
                    strtoupper((string) ($schedule->sch_day ?? '—')),
                    (string) $lec,
                    (string) $lab,
                    (string) $unit,
                    $schedule->room->room_name ?? '—',
                    $instructor,
                ];
                $table->addRow();
                foreach ($values as $i => $value) {
                    $table->addCell($widths[$i], $cell)->addText((string) $value, $cellStyle, $paragraph);
                }
            }
            $section->addText('', [], ['spaceAfter' => 100]);
        }

        if ($groupedRows->isEmpty()) {
            $section->addText('No schedules found for this selection.', ['italic' => true, 'color' => '666666'], ['alignment' => 'center', 'spaceAfter' => 250]);
        }

        $section->addText('', [], ['spaceBefore' => 260]);
        $signatures = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
        $signatures->addRow();
        $signatures->addCell(3600)->addText('Prepared by:', ['bold' => true, 'size' => 8]);
        $signatures->addCell(7200, ['gridSpan' => 2])->addText('Reviewed, Certified True and Correct:', ['bold' => true, 'size' => 8], ['alignment' => 'center']);
        $signatures->addCell(3600)->addText('Approved by:', ['bold' => true, 'size' => 8]);
        $signatures->addRow();
        $signatureNames = [
            [$signatories['chair_name'] ?? '—', $signatories['chair_title'] ?? 'Chair, Department'],
            [$signatories['dean_name'] ?? '—', $signatories['dean_title'] ?? 'Dean, CCICT'],
            [$signatories['cas_dean_name'] ?? '—', 'Dean, CAS'],
            [$signatories['campus_director'] ?? '—', 'Campus Director, CTU-Main Campus'],
        ];
        foreach ($signatureNames as [$name, $title]) {
            $signatures->addCell(3600)->addText((string) $name, ['bold' => true, 'size' => 8], ['alignment' => 'center', 'borderBottomSize' => 4, 'borderBottomColor' => '777777']);
        }
        $signatures->addRow();
        foreach ($signatureNames as [, $title]) {
            $signatures->addCell(3600)->addText((string) $title, ['size' => 8], ['alignment' => 'center']);
        }

        $sectionId = $filters['section'] ?? null;
        $sectionLabel = $sectionId
            ? str($selectedSection->sec_name ?? 'section')->slug()->toString()
            : 'all-sections';
        $filename = 'MIS-Class-Program-' . $sectionLabel . '-' . ($isEvening ? 'evening' : 'day') . '.docx';
        $tempPath = tempnam(sys_get_temp_dir(), 'mis-word-');
        IOFactory::createWriter($word, 'Word2007')->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function updateMisCode(Request $request, string $id)
    {
        $data = $request->validate([
            'sch_mis_code' => 'nullable|string|max:50',
        ]);

        $deptId = $this->chairDeptId();
        $schedule = Schedule::with('section')->find($id);

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Schedule not found.'], 404);
        }

        if ($deptId && optional($schedule->section)->sec_dept_id !== $deptId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit MIS codes under your department.',
            ], 403);
        }

        $schedule->sch_mis_code = $data['sch_mis_code'] !== '' ? trim($data['sch_mis_code']) : null;
        $schedule->save();

        return response()->json([
            'success' => true,
            'message' => 'MIS code saved.',
            'sch_mis_code' => $schedule->sch_mis_code,
        ]);
    }

    /** Save campus director name (manual). Stored in system_setting. */
    public function updateCampusDirector(Request $request)
    {
        $data = $request->validate([
            'campus_director' => 'nullable|string|max:150',
        ]);

        $name = trim($data['campus_director'] ?? '');
        SystemSetting::set(
            'campus_director_name',
            $name,
            Auth::user()?->usr_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Campus Director saved.',
            'campus_director' => $name,
        ]);
    }
}
