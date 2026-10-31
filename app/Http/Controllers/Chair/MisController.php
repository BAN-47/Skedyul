<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Departments;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DOMDocument;
use DOMNode;
use DOMXPath;
use ZipArchive;

class MisController extends Controller
{
    private function chairRecord(): ?Dept_Chair
    {
        $user = Auth::user();

        return $user
            ? Dept_Chair::where('dc_usr_id', $user->usr_id)->first()
            : null;
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

        $sem->label = trim(
            ($sem->sem_name ?? '')
                . ($yearDisp !== '' ? ', AY ' . $yearDisp : '')
        );
        $sem->ay_academic_year = $year;

        return $sem;
    }

    /** Build display name: First M. Last, Suffix */
    private function personName(?object $row, string $prefix): string
    {
        if (!$row) {
            return '';
        }

        $first = trim((string) ($row->{"{$prefix}first_name"} ?? ''));
        $middle = trim((string) ($row->{"{$prefix}middle_name"} ?? ''));
        $last = trim((string) ($row->{"{$prefix}last_name"} ?? ''));
        $suffix = trim((string) ($row->{"{$prefix}suffix"} ?? ''));

        $middlePart = $middle !== ''
            ? mb_strtoupper(mb_substr($middle, 0, 1)) . '.'
            : '';

        $name = implode(' ', array_filter([$first, $middlePart, $last]));

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

        $chairName = $this->personName($chair, 'dc_');

        if ($chairName === '' && Auth::user()) {
            $chairName = Auth::user()->usr_name ?? '';
        }

        $deanName = '';
        $collegeId = $chair?->dc_college_id;

        if (!$collegeId && $deptId) {
            $collegeId = Departments::where('dept_id', $deptId)
                ->value('dept_college_id');
        }

        if ($collegeId) {
            $dean = Dean::where('dean_college_id', $collegeId)->first();
            $deanName = $this->personName($dean, 'dean_');
        }

        $campusDirector = SystemSetting::get('campus_director_name', '');

        if (!$deptId) {
            return view('chair.mis', [
                'sections' => collect(),
                'groupedRows' => collect(),
                'activeSemester' => $activeSem,
                'chairDepartment' => null,
                'filters' => ['section' => null, 'shift' => $shift],
                'signatories' => [
                    'chair_name' => $chairName,
                    'chair_title' => 'Dept. Chair',
                    'dean_name' => $deanName,
                    'dean_title' => 'Dean',
                    'campus_director' => $campusDirector,
                ],
                'error' => 'Your account is not linked to a department. Contact the administrator.',
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
                ->whereHas(
                    'section',
                    fn($q) => $q->where('sec_dept_id', $deptId)
                )
                ->with(['subject', 'course', 'faculty', 'section', 'room'])
                ->orderBy('sch_day')
                ->orderBy('sch_start_time');

            if ($sectionId && $sections->contains('sec_id', $sectionId)) {
                $query->where('sch_sec_id', $sectionId);
            }

            $schedules = $query->get()
                ->filter(function ($schedule) use ($shift) {
                    try {
                        $hour = (int) Carbon::parse(
                            $schedule->sch_start_time
                        )->format('G');
                    } catch (\Throwable $e) {
                        return true;
                    }

                    return $shift === 'night' ? $hour >= 16 : $hour < 16;
                })
                ->values();
        }

        $groupedRows = $schedules->groupBy(
            fn($schedule) => optional($schedule->section)->sec_name
                ?? 'Unassigned'
        );

        return view('chair.mis', [
            'sections' => $sections,
            'groupedRows' => $groupedRows,
            'activeSemester' => $activeSem,
            'chairDepartment' => $chairDepartment,
            'filters' => [
                'section' => $sectionId,
                'shift' => $shift,
            ],
            'signatories' => [
                'chair_name' => $chairName,
                'chair_title' => 'Chair, ' . ($chairDepartment->dept_code ?? 'Department'),
                'dean_name' => $deanName,
                'dean_title' => 'Dean',
                'campus_director' => $campusDirector,
            ],
            'error' => null,
        ]);
    }

    /**
     * Download the filtered MIS program by updating the schedule tables
     * inside the provided DOCX template. The rest of the template—including
     * logos, page layout, signature blocks, and styles—is retained.
     */
    public function exportWord(Request $request)
    {
        $templatePath = storage_path('app/templates/MIS_Template.docx');

        abort_unless(
            is_file($templatePath),
            500,
            'MIS Word template was not found at storage/app/templates/MIS_Template.docx.'
        );

        $page = $this->index($request);
        $data = $page->getData();

        $groupedRows = $data['groupedRows'] ?? collect();
        $sections = $data['sections'] ?? collect();
        $filters = $data['filters'] ?? ['section' => null, 'shift' => 'day'];
        $activeSemester = $data['activeSemester'] ?? null;

        $tempPath = tempnam(sys_get_temp_dir(), 'mis-template-');

        if ($tempPath === false || !copy($templatePath, $tempPath)) {
            abort(500, 'Could not prepare the MIS Word template for export.');
        }

        $zip = new ZipArchive();

        if ($zip->open($tempPath) !== true) {
            @unlink($tempPath);
            abort(500, 'Could not open the MIS Word template.');
        }

        $documentXml = $zip->getFromName('word/document.xml');

        if ($documentXml === false) {
            $zip->close();
            @unlink($tempPath);
            abort(500, 'The MIS Word template is missing its main document content.');
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;

        if (!$dom->loadXML($documentXml, LIBXML_NONET)) {
            $zip->close();
            @unlink($tempPath);
            abort(500, 'The MIS Word template document could not be read.');
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace(
            'w',
            'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
        );

        $allTables = $xpath->query('//w:tbl');
        $scheduleTables = [];

        foreach ($allTables as $table) {
            // Word may split labels across styled runs and omit spaces between them.
            $tableText = preg_replace(
                '/[^A-Z0-9]/',
                '',
                strtoupper($this->nodeText($xpath, $table))
            );

            if (
                str_contains($tableText, 'MISCODE')
                && str_contains($tableText, 'COURSENO')
                && str_contains($tableText, 'INSTRUCTOR')
            ) {
                $scheduleTables[] = $table;
            }
        }

        if (!$scheduleTables) {
            $zip->close();
            @unlink($tempPath);
            abort(
                500,
                'Could not find the MIS schedule table in the Word template. Check that MIS_Template.docx is the class-program sample.'
            );
        }

        $templateTable = $scheduleTables[0];
        $templateHeading = $this->previousParagraph($templateTable);

        if (!$templateHeading || !$templateHeading->parentNode) {
            $zip->close();
            @unlink($tempPath);
            abort(500, 'Could not locate the section heading in the MIS template.');
        }

        $insertParent = $templateHeading->parentNode;
        $insertAnchor = $templateHeading;

        foreach ($groupedRows as $sectionName => $rows) {
            $headingClone = $templateHeading->cloneNode(true);
            $tableClone = $templateTable->cloneNode(true);

            $this->setNodeText($xpath, $headingClone, (string) $sectionName);
            $this->replaceScheduleRows($xpath, $tableClone, $rows);

            $insertParent->insertBefore($headingClone, $insertAnchor);
            $insertParent->insertBefore($tableClone, $insertAnchor);
        }

        /*
         * Remove each original sample heading and table. Remove the heading
         * first so it can still be found as the table's preceding paragraph.
         */
        foreach ($scheduleTables as $table) {
            $heading = $this->previousParagraph($table);

            if ($heading && $heading->parentNode) {
                $heading->parentNode->removeChild($heading);
            }

            if ($table->parentNode) {
                $table->parentNode->removeChild($table);
            }
        }

        $this->updateTemplateProgramLine(
            $xpath,
            $filters,
            $sections,
            $activeSemester
        );

        $zip->addFromString('word/document.xml', $dom->saveXML());
        $zip->close();

        $sectionId = $filters['section'] ?? null;
        $selectedSection = $sectionId
            ? $sections->firstWhere('sec_id', $sectionId)
            : null;

        $sectionSlug = $selectedSection
            ? str($selectedSection->sec_name)->slug()->toString()
            : 'all-sections';

        $shiftSlug = ($filters['shift'] ?? 'day') === 'night'
            ? 'evening'
            : 'day';

        $filename = "MIS-Class-Program-{$sectionSlug}-{$shiftSlug}.docx";

        return response()
            ->download($tempPath, $filename)
            ->deleteFileAfterSend(true);
    }

    private function replaceScheduleRows(
        DOMXPath $xpath,
        DOMNode $table,
        iterable $schedules
    ): void {
        $rows = $xpath->query('./w:tr', $table);

        if (!$rows || $rows->length < 2) {
            abort(500, 'The MIS template schedule table needs a header row and a sample data row.');
        }

        $headerRow = $rows->item(0);
        $sampleDataRow = $rows->item(1);

        // Keep the original header and table styling; remove its sample data rows.
        for ($i = $rows->length - 1; $i >= 1; $i--) {
            $row = $rows->item($i);
            $row->parentNode?->removeChild($row);
        }

        foreach ($schedules as $schedule) {
            $course = $schedule->course ?? $schedule->subject;

            $courseCode = $course->course_code
                ?? $course->subj_code
                ?? '—';

            $title = $course->course_name
                ?? $course->subj_name
                ?? '—';

            $lecture = $course->course_lecture_hours
                ?? $course->subj_lecture_hours
                ?? 0;

            $lab = $course->course_lab_hours
                ?? $course->subj_lab_hours
                ?? 0;

            $unit = $course->course_units ?? 0;

            try {
                $time = Carbon::parse($schedule->sch_start_time)->format('g:i A')
                    . ' – '
                    . Carbon::parse($schedule->sch_end_time)->format('g:i A');
            } catch (\Throwable $e) {
                $time = trim(
                    ($schedule->sch_start_time ?? '')
                        . ' – '
                        . ($schedule->sch_end_time ?? '')
                );
            }

            $instructor = $schedule->faculty
                ? trim(
                    ($schedule->faculty->fac_first_name ?? '')
                        . ' '
                        . ($schedule->faculty->fac_last_name ?? '')
                )
                : '—';

            $values = [
                $schedule->sch_mis_code ?? '',
                $courseCode,
                $title,
                $time,
                $this->shortDay($schedule->sch_day ?? ''),
                (string) $lecture,
                (string) $lab,
                (string) $unit,
                $schedule->room->room_name ?? '—',
                $instructor,
            ];

            $rowClone = $sampleDataRow->cloneNode(true);
            $cells = $xpath->query('./w:tc', $rowClone);

            if ($cells !== false) {
                foreach ($values as $index => $value) {
                    $cell = $cells->item($index);

                    if ($cell instanceof DOMNode) {
                        $this->setNodeText($xpath, $cell, (string) $value);
                    }
                }
            }

            $table->appendChild($rowClone);
        }

        if (is_iterable($schedules) && count($schedules) === 0) {
            $emptyRow = $sampleDataRow->cloneNode(true);
            $cells = $xpath->query('./w:tc', $emptyRow);

            if ($cells->length > 0) {
                $this->setNodeText($xpath, $cells->item(0), 'No scheduled classes');
            }

            for ($i = 1; $i < $cells->length; $i++) {
                $this->setNodeText($xpath, $cells->item($i), '');
            }

            $table->appendChild($emptyRow);
        }
    }

    private function updateTemplateProgramLine(
        DOMXPath $xpath,
        array $filters,
        $sections,
        ?object $activeSemester
    ): void {
        $sectionId = $filters['section'] ?? null;
        $selectedSection = $sectionId
            ? $sections->firstWhere('sec_id', $sectionId)
            : null;

        $shift = ($filters['shift'] ?? 'day') === 'night'
            ? 'Evening Program'
            : 'Day Program';

        $parts = [
            $shift,
            $activeSemester->label ?? 'No active semester',
            $selectedSection->sec_name ?? 'All Sections',
        ];

        $programLine = implode(' · ', array_filter($parts));
        $paragraphs = $xpath->query('//w:p');

        foreach ($paragraphs as $paragraph) {
            $text = $this->nodeText($xpath, $paragraph);

            if (
                stripos($text, 'Day Program') !== false
                || stripos($text, 'Evening Program') !== false
            ) {
                $this->setNodeText($xpath, $paragraph, $programLine);
                return;
            }
        }
    }

    private function shortDay(string $day): string
    {
        $normalized = strtoupper(trim($day));

        return match ($normalized) {
            'MONDAY', 'MON' => 'MON',
            'TUESDAY', 'TUE', 'TUES' => 'TUE',
            'WEDNESDAY', 'WED' => 'WED',
            'THURSDAY', 'THU', 'THUR' => 'THUR',
            'FRIDAY', 'FRI' => 'FRI',
            'SATURDAY', 'SAT' => 'SAT',
            'SUNDAY', 'SUN' => 'SUN',
            default => $normalized !== '' ? $normalized : '—',
        };
    }

    private function previousParagraph(DOMNode $node): ?DOMNode
    {
        for ($sibling = $node->previousSibling; $sibling; $sibling = $sibling->previousSibling) {
            if (
                $sibling->nodeType === XML_ELEMENT_NODE
                && $sibling->localName === 'p'
            ) {
                return $sibling;
            }

            if (
                $sibling->nodeType === XML_ELEMENT_NODE
                && $sibling->localName === 'tbl'
            ) {
                break;
            }
        }

        return null;
    }

    private function nodeText(DOMXPath $xpath, DOMNode $node): string
    {
        $textNodes = $xpath->query('.//w:t', $node);
        $text = '';

        foreach ($textNodes as $textNode) {
            $text .= $textNode->textContent;
        }

        return trim($text);
    }

    private function setNodeText(
        DOMXPath $xpath,
        DOMNode $node,
        string $value
    ): void {
        $textNodes = $xpath->query('.//w:t', $node);

        if (!$textNodes || $textNodes->length === 0) {
            return;
        }

        $first = $textNodes->item(0);

        if (!$first instanceof \DOMElement) {
            return;
        }

        $first->textContent = $value;

        if (
            $value !== ''
            && (str_starts_with($value, ' ') || str_ends_with($value, ' '))
        ) {
            $first->setAttributeNS(
                'http://www.w3.org/XML/1998/namespace',
                'xml:space',
                'preserve'
            );
        }

        for ($i = 1; $i < $textNodes->length; $i++) {
            $textNode = $textNodes->item($i);

            if ($textNode instanceof \DOMElement) {
                $textNode->textContent = '';
            }
        }
    }

    public function updateMisCode(Request $request, string $id)
    {
        $data = $request->validate([
            'sch_mis_code' => 'nullable|string|max:50',
        ]);

        $deptId = $this->chairDeptId();
        $schedule = Schedule::with('section')->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found.',
            ], 404);
        }

        if ($deptId && optional($schedule->section)->sec_dept_id !== $deptId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit MIS codes under your department.',
            ], 403);
        }

        $schedule->sch_mis_code = ($data['sch_mis_code'] ?? '') !== ''
            ? trim($data['sch_mis_code'])
            : null;
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
