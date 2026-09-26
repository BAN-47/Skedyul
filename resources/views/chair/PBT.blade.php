<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SKEDYUL — Program by Teacher (PBT)</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

@php
  // ── Defaults so this page renders before PbtController exists ──
  $schedules = $schedules ?? collect();   // schedules already filtered to $selectedFaculty, if one is chosen
  $subjects  = $subjects  ?? collect();
  $facultyFullTime = $facultyFullTime ?? collect();
  $facultyPartTime = $facultyPartTime ?? collect();
  $deans           = $deans           ?? collect();
  $sections  = $sections  ?? collect();
  $programs  = $programs  ?? collect();
  $semesters = $semesters ?? collect();
  $filters   = $filters   ?? [];
  $selectedFaculty = $selectedFaculty ?? null; // the Faculty (or Dean) model currently being viewed, or null
  $loadStats = $loadStats ?? [
      'preparations' => null,
      'units'        => null,
      'hours_week'   => null,
      'designation'  => null,
      'production'   => null,
      'extension'    => null,
      'research'     => null,
  ];

  $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  // Same Day/Night shift mechanics as PBS.
  $shift = request()->query('shift', 'day') === 'night' ? 'night' : 'day';

  if ($shift === 'night') {
      $gridStart = 16 * 60; // 4:00 PM
      $gridEnd   = 21 * 60; // 9:00 PM
  } else {
      $gridStart = 7 * 60;  // 7:00 AM
      $gridEnd   = 16 * 60; // 4:00 PM
  }

  $slotMinutes = 30;
  $slotCount   = intdiv($gridEnd - $gridStart, $slotMinutes);

  $noonStart = 12 * 60;
  $noonEnd   = 13 * 60;
  $showNoonBreak = $shift === 'day' && $noonStart >= $gridStart && $noonEnd <= $gridEnd;

  $selectedDate = $selectedDate ?? now();
  if (is_string($selectedDate)) {
      $selectedDate = \Illuminate\Support\Carbon::parse($selectedDate);
  }

  // Occupied-cell lookup — same purpose as PBS: don't render a click
  // target under an existing schedule block.
  $occupied = [];
  foreach ($schedules as $s) {
      $dIdx = array_search($s->sch_day, $days, true);
      if ($dIdx === false) continue;
      $start = \Illuminate\Support\Carbon::parse($s->sch_start_time);
      $end   = \Illuminate\Support\Carbon::parse($s->sch_end_time);
      $sMin  = max(($start->hour * 60) + $start->minute, $gridStart);
      $eMin  = min(($end->hour * 60) + $end->minute, $gridEnd);
      for ($m = $sMin; $m < $eMin; $m += $slotMinutes) {
          $rowIdx = intdiv($m - $gridStart, $slotMinutes);
          $occupied[$dIdx . '-' . $rowIdx] = true;
      }
  }
@endphp

<div class="app-shell">

  @include('partials.chair_sidebar')

  {{-- ══════════ MAIN ══════════ --}}
  <div class="app-main">

    {{-- TOPBAR --}}
    @include('partials.chair_header')

    <div class="page-content" id="page-pbt">

      {{-- HEADING + ACTIONS --}}
      <div class="flex items-center justify-between gap-3 mb-4">
        <div class="text-[19px] font-extrabold text-slate-900">PBT Schedule Plotter</div>
        <div class="flex items-center gap-2">
          <button type="button" onclick="saveDraft()" class="btn btn-secondary">Save Draft</button>
          <button type="button" onclick="clearAll()" class="btn btn-secondary">Clear All</button>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-[200px_1fr] gap-4">

        {{-- ══════════ SUMMARY (teacher load stats, not a schedule list) ══════════ --}}
        <aside class="card !p-0 overflow-hidden self-start">
          <div class="bg-blue-600 text-white text-center text-[13px] font-extrabold px-4 py-2.5">
            SUMMARY
          </div>
          <div class="p-3.5 text-[12px] text-slate-700 space-y-1.5">
            <div><span class="font-bold">No. of Preparations:</span> {{ $loadStats['preparations'] ?? '—' }}</div>
            <div><span class="font-bold">No. of Units:</span> {{ $loadStats['units'] ?? '—' }}</div>
            <div><span class="font-bold">No. of Hours/Week:</span> {{ $loadStats['hours_week'] ?? '—' }}</div>
            <div><span class="font-bold">Administrative Designation:</span> {{ $loadStats['designation'] ?? '—' }}</div>

            <div class="border-t border-slate-100 my-2"></div>

            <div><span class="font-bold">Production:</span> {{ $loadStats['production'] ?? '—' }}</div>
            <div><span class="font-bold">Extension:</span> {{ $loadStats['extension'] ?? '—' }}</div>
            <div><span class="font-bold">Research:</span> {{ $loadStats['research'] ?? '—' }}</div>

            @unless($selectedFaculty)
              <div class="pt-3 text-center text-[11px] text-slate-400 italic">
                Select a teacher to view their load.
              </div>
            @endunless
          </div>
        </aside>

        {{-- ══════════ PLOTTER ══════════ --}}
        <section class="card !p-0 overflow-hidden">

          {{-- FILTER TOOLBAR --}}
          <div class="flex flex-wrap items-center gap-1.5 px-3 py-2.5 border-b border-slate-200 bg-slate-50 relative">

            {{-- TEACHER picker: button opens a searchable, grouped popover --}}
            <div class="relative">
              <button type="button" id="teacher-picker-btn" onclick="toggleTeacherPicker()" class="pbs-filter min-w-[130px] text-left">
                {{ $selectedFaculty ? ($selectedFaculty->fac_first_name . ' ' . $selectedFaculty->fac_last_name) : 'TEACHER' }}
              </button>

              <div id="teacher-picker-panel"
                   class="hidden absolute left-0 top-[calc(100%+4px)] w-[260px] max-h-[360px] overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-[0_20px_60px_rgba(15,23,42,0.15)] z-50">
                <div class="p-2 border-b border-slate-100">
                  <input type="text" id="teacher-search" oninput="filterTeacherList(this.value)"
                         placeholder="Search professor..." autocomplete="off"
                         class="w-full rounded-lg border border-slate-200 px-2.5 py-1.5 text-[12px] outline-none focus:border-blue-500">
                </div>

                @foreach([
                    'FULL-TIME' => $facultyFullTime,
                    'PART-TIME' => $facultyPartTime,
                    'DEAN'      => $deans,
                ] as $groupLabel => $groupList)
                  <div class="teacher-group" data-group="{{ $groupLabel }}">
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-400 bg-slate-50">
                      {{ $groupLabel }}
                    </div>
                    @forelse($groupList as $person)
                      @php
                        $pid   = $person->fac_id ?? $person->dean_id ?? '';
                        $pname = trim(($person->fac_first_name ?? $person->dean_first_name ?? '') . ' ' . ($person->fac_last_name ?? $person->dean_last_name ?? ''));
                      @endphp
                      <button type="button"
                              class="teacher-option w-full text-left px-3 py-2 text-[12.5px] text-slate-700 hover:bg-blue-50"
                              data-name="{{ strtolower($pname) }}"
                              onclick="selectTeacher('{{ $pid }}')">
                        {{ $pname }}
                      </button>
                    @empty
                      <div class="px-3 py-2 text-[11.5px] text-slate-400 italic">None</div>
                    @endforelse
                  </div>
                @endforeach
              </div>
            </div>

            <select id="filter-program" onchange="applyFilters()" class="pbs-filter">
              <option value="">PROGRAM</option>
              @foreach($programs as $p)
                <option value="{{ $p->prog_id }}" @selected(($filters['program'] ?? '') === $p->prog_id)>
                  {{ $p->prog_code }}
                </option>
              @endforeach
            </select>

            <select id="filter-semester" onchange="applyFilters()" class="pbs-filter">
              <option value="">SEMESTER</option>
              @foreach($semesters as $sem)
                <option value="{{ $sem->sem_id }}" @selected(($filters['semester'] ?? '') === $sem->sem_id)>
                  {{ $sem->sem_name }}
                </option>
              @endforeach
            </select>

            <div class="flex rounded-md overflow-hidden border border-slate-300">
              <button type="button" id="shift-day" onclick="setShift('day')" class="px-2.5 py-1.5 text-[11px] font-bold uppercase">Day</button>
              <button type="button" id="shift-night" onclick="setShift('night')" class="px-2.5 py-1.5 text-[11px] font-bold uppercase border-l border-slate-300">Night</button>
            </div>

            <div class="ml-auto text-[11px] text-slate-400 italic">
              Click an empty time slot below to add a schedule
            </div>
          </div>

          {{-- ══ WEEK GRID (identical mechanics to PBS) ══ --}}
          <div class="overflow-x-auto">
            <div class="min-w-[840px]">

              <div class="grid bg-slate-100 border-b border-slate-200"
                   style="grid-template-columns: 90px repeat({{ count($days) }}, minmax(0,1fr));">
                <div class="px-2 py-2 text-center text-[10.5px] font-bold uppercase tracking-wide text-slate-500">Time</div>
                @foreach($days as $d)
                  <div class="px-2 py-2 text-center text-[11.5px] font-bold text-slate-700 border-l border-slate-200">{{ $d }}</div>
                @endforeach
              </div>

              <div class="grid relative"
                   style="grid-template-columns: 90px repeat({{ count($days) }}, minmax(0,1fr));
                          grid-template-rows: repeat({{ $slotCount }}, 26px);">

                @for($i = 0; $i < $slotCount; $i += 2)
                  @php
                    $mins      = $gridStart + ($i * $slotMinutes);
                    $bandStart = \Illuminate\Support\Carbon::createFromTime(intdiv($mins, 60), 0);
                    $bandEnd   = (clone $bandStart)->addHour();
                    $label     = $bandStart->format('g') . '-' . $bandEnd->format('g') . ' ' . $bandEnd->format('A');
                  @endphp
                  <div class="bg-slate-50 border-b border-slate-200 px-2 flex items-center justify-end text-[10.5px] font-semibold text-slate-500"
                       style="grid-column: 1; grid-row: {{ $i + 1 }} / span 2;">
                    {{ $label }}
                  </div>
                @endfor

                @for($i = 0; $i < $slotCount; $i++)
                  @php $mins = $gridStart + ($i * $slotMinutes); @endphp
                  @for($c = 0; $c < count($days); $c++)
                    @php
                      $cellDay  = $days[$c];
                      $cellTime = \Illuminate\Support\Carbon::createFromTime(intdiv($mins, 60), $mins % 60)->format('H:i');
                      $isTaken  = isset($occupied[$c . '-' . $i]);
                    @endphp
                    @if($isTaken || !$selectedFaculty)
                      <div class="border-l border-b border-slate-100 {{ $mins % 60 === 0 ? '!border-b-slate-200' : '' }}"
                           style="grid-column: {{ $c + 2 }}; grid-row: {{ $i + 1 }};"></div>
                    @else
                      <button type="button"
                              onclick="openAddModal('{{ $cellDay }}', '{{ $cellTime }}')"
                              class="pbs-cell border-l border-b border-slate-100 {{ $mins % 60 === 0 ? '!border-b-slate-200' : '' }} hover:bg-blue-50 cursor-pointer transition"
                              style="grid-column: {{ $c + 2 }}; grid-row: {{ $i + 1 }};"
                              title="Add schedule — {{ $cellDay }} {{ \Illuminate\Support\Carbon::createFromTime(intdiv($mins, 60), $mins % 60)->format('g:i A') }}">
                      </button>
                    @endif
                  @endfor
                @endfor

                @if($showNoonBreak)
                  @php
                    $noonRow  = intdiv($noonStart - $gridStart, $slotMinutes) + 1;
                    $noonSpan = intdiv($noonEnd - $noonStart, $slotMinutes);
                  @endphp
                  <div class="z-10 flex items-center justify-center bg-slate-900 text-white text-[12px] font-bold tracking-[3px] pointer-events-none"
                       style="grid-column: 2 / span {{ count($days) }}; grid-row: {{ $noonRow }} / span {{ $noonSpan }};">
                    NOON BREAK
                  </div>
                @endif

                {{-- SCHEDULE BLOCKS — Subject / Section / Room (professor is implied: it's the selected teacher) --}}
                @foreach($schedules as $s)
                  @php
                    $dayIndex = array_search($s->sch_day, $days, true);
                    if ($dayIndex === false) continue;

                    $start = \Illuminate\Support\Carbon::parse($s->sch_start_time);
                    $end   = \Illuminate\Support\Carbon::parse($s->sch_end_time);

                    $startMins = max(($start->hour * 60) + $start->minute, $gridStart);
                    $endMins   = min(($end->hour * 60) + $end->minute, $gridEnd);
                    if ($endMins <= $startMins) continue;

                    $row  = intdiv($startMins - $gridStart, $slotMinutes) + 1;
                    $span = max(1, (int) ceil(($endMins - $startMins) / $slotMinutes));
                  @endphp

                  <div class="group relative z-20 m-[2px] rounded-md overflow-hidden bg-emerald-500 border border-emerald-600 text-white px-1.5 py-1 shadow-sm"
                       style="grid-column: {{ $dayIndex + 2 }}; grid-row: {{ $row }} / span {{ $span }};"
                       data-schedule-id="{{ $s->sch_id }}"
                       data-subject="{{ $s->sch_subj_id }}"
                       data-section="{{ $s->sch_sec_id }}"
                       data-room="{{ $s->sch_room_id }}"
                       data-semester="{{ $s->sch_sem_id }}"
                       data-day="{{ $s->sch_day }}"
                       data-start="{{ substr($s->sch_start_time, 0, 5) }}"
                       data-end="{{ substr($s->sch_end_time, 0, 5) }}">

                    <div class="absolute inset-x-0 top-0 hidden group-hover:flex gap-0.5 p-0.5">
                      <button type="button" onclick="event.stopPropagation(); openEditModal(this.closest('[data-schedule-id]'))"
                              class="flex-1 rounded-sm bg-white/90 hover:bg-white py-[2px] text-[8px] font-bold text-slate-800">Edit Schedule</button>
                      <button type="button" onclick="event.stopPropagation(); openDeleteModal(this.closest('[data-schedule-id]'))"
                              class="flex-1 rounded-sm bg-white/90 hover:bg-white py-[2px] text-[8px] font-bold text-red-700">Delete Schedule</button>
                    </div>

                    <div class="pt-3 text-center leading-tight">
                      <div class="text-[10.5px] font-extrabold">{{ $s->subject->subj_code ?? '—' }}</div>
                      <div class="text-[9px] opacity-90">{{ $s->section->sec_name ?? '' }}</div>
                      @if($s->room)<div class="text-[9px] opacity-90">{{ $s->room->room_name }}</div>@endif
                    </div>
                  </div>
                @endforeach

              </div>
            </div>
          </div>

          {{-- DATE NAV --}}
          <div class="flex flex-col items-center gap-1 py-3 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
              <button type="button" onclick="shiftDate(-1)"
                      class="w-7 h-7 rounded-full border border-slate-300 bg-white text-slate-600 hover:bg-slate-100 flex items-center justify-center">‹</button>
              <div class="text-[13px] font-bold text-slate-700">{{ $selectedDate->format('F j, Y (l)') }}</div>
              <button type="button" onclick="shiftDate(1)"
                      class="w-7 h-7 rounded-full border border-slate-300 bg-white text-slate-600 hover:bg-slate-100 flex items-center justify-center">›</button>
            </div>
            <div class="text-[10.5px] text-slate-400" id="ph-clock"></div>
          </div>

        </section>
      </div>
    </div>
  </div>
</div>


{{-- ══════════════ MODAL: ADD ══════════════ --}}
<div class="modal-overlay" id="modal-add-schedule">
  <div class="modal-box w-[520px] max-w-[calc(100vw-32px)]">
    <div class="modal-header">
      <div class="modal-title text-indigo-700">Add Schedule</div>
      <button type="button" onclick="closeModal('modal-add-schedule')" class="modal-close">✕</button>
    </div>

    <div class="max-h-[62vh] overflow-y-auto pr-1">
      <div class="mb-3 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2 text-[12.5px] text-slate-600">
        Teacher: <span class="font-bold text-slate-900">{{ $selectedFaculty ? ($selectedFaculty->fac_first_name . ' ' . $selectedFaculty->fac_last_name) : '—' }}</span>
      </div>

      <div class="mb-3">
        <label class="field-label">Subject</label>
        <select id="add-subject" class="field-input">
          <option value="">-- Select Subject --</option>
          @foreach($subjects as $sub)
            <option value="{{ $sub->subj_id }}">{{ $sub->subj_code }} — {{ $sub->subj_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="field-label">Semester</label>
        <select id="add-semester" class="field-input">
          <option value="">-- Select Semester --</option>
          @foreach($semesters as $sem)
            <option value="{{ $sem->sem_id }}">{{ $sem->sem_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="field-label">Program</label>
        <select id="add-program" class="field-input">
          <option value="">-- Select Program --</option>
          @foreach($programs as $p)
            <option value="{{ $p->prog_id }}">{{ $p->prog_code }} — {{ $p->prog_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="field-label">Section</label>
          <select id="add-section" class="field-input">
            <option value="">-- Select Section --</option>
            @foreach($sections as $sec)
              <option value="{{ $sec->sec_id }}">{{ $sec->sec_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="field-label">Room</label>
          <select id="add-room" class="field-input">
            <option value="">-- Select Room --</option>
            @foreach(($rooms ?? collect()) as $r)
              <option value="{{ $r->room_id }}">{{ $r->room_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="field-label">Day</label>
        <select id="add-day" class="field-input">
          <option value="">-- Select Day --</option>
          @foreach($days as $d)
            <option value="{{ $d }}">{{ $d }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="field-label">Start Time</label>
          <input type="time" id="add-start" step="1800" class="field-input">
        </div>
        <div>
          <label class="field-label">End Time</label>
          <input type="time" id="add-end" step="1800" class="field-input">
        </div>
      </div>

      <div class="mb-3">
        <label class="field-label">Description (optional)</label>
        <textarea id="add-description" rows="3" class="field-input resize-y"></textarea>
      </div>

      <div id="add-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[12.5px] text-red-700 mb-1"></div>
    </div>

    <div class="modal-footer">
      <button type="button" onclick="closeModal('modal-add-schedule')" class="btn btn-secondary">Cancel</button>
      <button type="button" id="add-submit" onclick="submitAdd()" class="btn btn-primary">Confirm Schedule</button>
    </div>
  </div>
</div>


{{-- ══════════════ MODAL: EDIT ══════════════ --}}
<div class="modal-overlay" id="modal-edit-schedule">
  <div class="modal-box w-[520px] max-w-[calc(100vw-32px)]">
    <div class="modal-header">
      <div class="modal-title text-indigo-700">Edit Schedule</div>
      <button type="button" onclick="closeModal('modal-edit-schedule')" class="modal-close">✕</button>
    </div>

    <div class="max-h-[62vh] overflow-y-auto pr-1">
      <input type="hidden" id="edit-id">

      <div class="mb-3">
        <label class="field-label">Subject</label>
        <select id="edit-subject" class="field-input">
          <option value="">-- Select Subject --</option>
          @foreach($subjects as $sub)
            <option value="{{ $sub->subj_id }}">{{ $sub->subj_code }} — {{ $sub->subj_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="field-label">Semester</label>
        <select id="edit-semester" class="field-input">
          <option value="">-- Select Semester --</option>
          @foreach($semesters as $sem)
            <option value="{{ $sem->sem_id }}">{{ $sem->sem_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="field-label">Program</label>
        <select id="edit-program" class="field-input">
          <option value="">-- Select Program --</option>
          @foreach($programs as $p)
            <option value="{{ $p->prog_id }}">{{ $p->prog_code }} — {{ $p->prog_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="field-label">Section</label>
          <select id="edit-section" class="field-input">
            <option value="">-- Select Section --</option>
            @foreach($sections as $sec)
              <option value="{{ $sec->sec_id }}">{{ $sec->sec_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="field-label">Room</label>
          <select id="edit-room" class="field-input">
            <option value="">-- Select Room --</option>
            @foreach(($rooms ?? collect()) as $r)
              <option value="{{ $r->room_id }}">{{ $r->room_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="field-label">Day</label>
        <select id="edit-day" class="field-input">
          <option value="">-- Select Day --</option>
          @foreach($days as $d)
            <option value="{{ $d }}">{{ $d }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="field-label">Start Time</label>
          <input type="time" id="edit-start" step="1800" class="field-input">
        </div>
        <div>
          <label class="field-label">End Time</label>
          <input type="time" id="edit-end" step="1800" class="field-input">
        </div>
      </div>

      <div class="mb-3">
        <label class="field-label">Description (optional)</label>
        <textarea id="edit-description" rows="3" class="field-input resize-y"></textarea>
      </div>

      <div id="edit-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[12.5px] text-red-700 mb-1"></div>
    </div>

    <div class="modal-footer">
      <button type="button" onclick="closeModal('modal-edit-schedule')" class="btn btn-secondary">Cancel</button>
      <button type="button" id="edit-submit" onclick="submitEdit()" class="btn btn-primary">Edit Schedule</button>
    </div>
  </div>
</div>


{{-- ══════════════ MODAL: DELETE ══════════════ --}}
<div class="modal-overlay" id="modal-delete-schedule">
  <div class="modal-box w-[420px] max-w-[calc(100vw-32px)]">
    <div class="modal-header">
      <div class="modal-title text-red-600">Delete Schedule</div>
      <button type="button" onclick="closeModal('modal-delete-schedule')" class="modal-close">✕</button>
    </div>

    <div class="text-center">
      <input type="hidden" id="delete-id">

      <p class="text-[13px] font-semibold text-slate-700 mb-3">
        Are you sure you want to delete this specific schedule?
      </p>

      <p class="text-[12.5px] text-slate-600 mb-2">
        To confirm, please type
        <span class="font-mono font-bold text-slate-900" id="delete-code"></span>
        in the box below:
      </p>

      <input type="text" id="delete-confirm" autocomplete="off" oninput="validateDeleteCode()"
             class="field-input text-center font-mono mb-3">

      <p class="text-[11.5px] text-slate-500 mb-1">
        Note: When confirmed, this schedule won't be retrieved in any way.
      </p>

      <div id="delete-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[12.5px] text-red-700 text-left"></div>
    </div>

    <div class="modal-footer">
      <button type="button" onclick="closeModal('modal-delete-schedule')" class="btn btn-secondary">Cancel</button>
      <button type="button" id="delete-submit" disabled onclick="submitDelete()" class="btn btn-danger opacity-50 cursor-not-allowed">Delete Schedule</button>
    </div>
  </div>
</div>


{{-- TOAST --}}
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
  window.PBT_SELECTED_DATE    = @json($selectedDate->toDateString());
  window.PBT_SELECTED_TEACHER = @json($selectedFaculty->fac_id ?? null);
</script>
<script src="{{ asset('js/pbt.js') }}" defer></script>

@if(session('error') || session('success'))
<script>
  document.addEventListener('DOMContentLoaded', () => {
    showToast(@json(session('error') ?? session('success')));
  });
</script>
@endif

</body>

</html>