<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SKEDYUL — Class Program for MIS</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @media print {
      .no-print { display: none !important; }
      .app-shell, .app-main, .page-content { overflow: visible !important; height: auto !important; }
      body { background: white !important; }
    }
  </style>
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

@php
  $sections        = $sections ?? collect();
  $groupedRows     = $groupedRows ?? collect();
  $activeSemester  = $activeSemester ?? null;
  $chairDepartment = $chairDepartment ?? null;
  $filters         = $filters ?? ['section' => null, 'shift' => 'day'];
  $error           = $error ?? null;
  $signatories     = $signatories ?? [];
  $shift           = $filters['shift'] ?? 'day';
@endphp

<div class="app-shell">
  @include('partials.chair_sidebar')

  <div class="app-main">
    @include('partials.chair_header')

    <div class="page-content" id="page-mis">

      <div class="flex items-center justify-between gap-3 mb-4 no-print">
        <div class="text-[19px] font-extrabold text-slate-900">Class Program for MIS</div>
        <div class="flex items-center gap-2">
          <button type="button" onclick="window.print()" class="btn btn-secondary">Print</button>
        </div>
      </div>

      @if($error)
        <div class="mb-3 p-3 rounded-lg text-[12px] bg-amber-50 border border-amber-200 text-amber-900">{{ $error }}</div>
      @endif

      {{-- HEADER FILTERS --}}
      <div class="card mb-4 no-print">
        <div class="flex flex-wrap items-center gap-2 p-3">
          @if($chairDepartment)
            <span class="px-2.5 py-1.5 rounded-md bg-blue-600 text-white text-[11px] font-extrabold uppercase">
              {{ $chairDepartment->dept_code ?? 'DEPT' }}
            </span>
          @endif

          <select id="filter-section" onchange="applyMisFilters()" class="pbs-filter min-w-[140px]">
            <option value="">All sections</option>
            @foreach($sections as $sec)
              <option value="{{ $sec->sec_id }}" @selected(($filters['section'] ?? '') === $sec->sec_id)>
                {{ $sec->sec_name }}
              </option>
            @endforeach
          </select>

          <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-slate-100 border border-slate-200 text-[11px] font-bold text-slate-700">
            {{ optional($activeSemester)->label ?? 'No active semester' }}
          </span>

          <div class="flex rounded-md overflow-hidden border border-slate-300">
            <button type="button" onclick="setMisShift('day')"
                    class="px-2.5 py-1.5 text-[11px] font-bold uppercase {{ $shift === 'day' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600' }}">
              Day
            </button>
            <button type="button" onclick="setMisShift('night')"
                    class="px-2.5 py-1.5 text-[11px] font-bold uppercase border-l border-slate-300 {{ $shift === 'night' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600' }}">
              Evening
            </button>
          </div>
        </div>
      </div>

      {{-- TITLE --}}
      <div class="text-center mb-3">
        <div class="text-[14px] font-extrabold tracking-wide text-slate-800">CLASS PROGRAM FOR MIS</div>
        <div class="text-[12px] font-semibold text-slate-600">
          {{ optional($activeSemester)->label ?? '—' }}
        </div>
      </div>

      @forelse($groupedRows as $sectionName => $rows)
        <div class="card mb-4 !p-0 overflow-hidden">
          <div class="px-3 py-2 bg-slate-100 border-b border-slate-200 text-[13px] font-extrabold text-slate-800">
            {{ $sectionName }}
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-[11px] border-collapse min-w-[900px]">
              <thead>
                <tr class="bg-slate-50 text-slate-600 uppercase font-bold text-[10px]">
                  <th class="px-2 py-2 text-left border-b border-slate-200 w-[90px]">MIS Code</th>
                  <th class="px-2 py-2 text-left border-b border-l border-slate-200">Course No.</th>
                  <th class="px-2 py-2 text-left border-b border-l border-slate-200">Descriptive Title</th>
                  <th class="px-2 py-2 text-left border-b border-l border-slate-200">Time</th>
                  <th class="px-2 py-2 text-left border-b border-l border-slate-200">Day</th>
                  <th class="px-2 py-2 text-center border-b border-l border-slate-200">Lec</th>
                  <th class="px-2 py-2 text-center border-b border-l border-slate-200">Lab</th>
                  <th class="px-2 py-2 text-center border-b border-l border-slate-200">Total Hrs</th>
                  <th class="px-2 py-2 text-center border-b border-l border-slate-200">Unit</th>
                  <th class="px-2 py-2 text-left border-b border-l border-slate-200">Room</th>
                  <th class="px-2 py-2 text-left border-b border-l border-slate-200">Instructor</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $s)
                  @php
                    $course = $s->course ?? $s->subject;
                    $code  = $course->course_code ?? $course->subj_code ?? '—';
                    $title = $course->course_name ?? $course->subj_name ?? '—';
                    $lec   = $course->course_lecture_hours ?? $course->subj_lecture_hours ?? 0;
                    $lab   = $course->course_lab_hours ?? $course->subj_lab_hours ?? 0;
                    $totalHours = $course->course_total_hours ?? ((float) $lec + (float) $lab);
                    $unit  = (float) ($course->course_units ?? ((float) $lec + (float) $lab));
                    try {
                      $time = \Illuminate\Support\Carbon::parse($s->sch_start_time)->format('g:i A')
                        . ' – '
                        . \Illuminate\Support\Carbon::parse($s->sch_end_time)->format('g:i A');
                    } catch (\Throwable $e) {
                      $time = trim(($s->sch_start_time ?? '') . ' – ' . ($s->sch_end_time ?? ''));
                    }
                    $day = $s->sch_day ?? '—';
                    $room = optional($s->room)->room_name ?? '—';
                    $inst = $s->faculty
                      ? trim(($s->faculty->fac_first_name ?? '') . ' ' . ($s->faculty->fac_last_name ?? ''))
                      : '—';
                  @endphp
                  <tr class="border-b border-slate-100 hover:bg-slate-50/80">
                    <td class="px-2 py-1.5 align-top">
                      <input type="text"
                             class="w-full min-w-[72px] rounded border border-slate-300 px-1.5 py-1 text-[11px] font-mono"
                             value="{{ $s->sch_mis_code ?? '' }}"
                             placeholder="—"
                             data-sch-id="{{ $s->sch_id }}"
                             onchange="saveMisCode(this)"
                             onblur="saveMisCode(this)">
                    </td>
                    <td class="px-2 py-1.5 font-bold text-slate-800 whitespace-nowrap border-l border-slate-100">{{ $code }}</td>
                    <td class="px-2 py-1.5 text-slate-700 border-l border-slate-100">{{ $title }}</td>
                    <td class="px-2 py-1.5 whitespace-nowrap border-l border-slate-100">{{ $time }}</td>
                    <td class="px-2 py-1.5 whitespace-nowrap border-l border-slate-100 uppercase">{{ $day }}</td>
                    <td class="px-2 py-1.5 text-center border-l border-slate-100">{{ $lec }}</td>
                    <td class="px-2 py-1.5 text-center border-l border-slate-100">{{ $lab }}</td>
                    <td class="px-2 py-1.5 text-center border-l border-slate-100">{{ $totalHours }}</td>
                    <td class="px-2 py-1.5 text-center font-semibold border-l border-slate-100">{{ $unit }}</td>
                    <td class="px-2 py-1.5 whitespace-nowrap border-l border-slate-100">{{ $room }}</td>
                    <td class="px-2 py-1.5 border-l border-slate-100">{{ $inst }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @empty
        <div class="card p-6 text-center text-[13px] text-slate-500">
          @if(!$activeSemester)
            No active semester. Set Academic Year in Admin Settings first.
          @elseif(empty($filters['section']))
            No schedules plotted yet for this department. Plot classes in PBS / PBT first.
          @else
            No schedules for this section in the active semester ({{ $shift }} shift).
          @endif
        </div>
      @endforelse

      {{-- FOOTER SIGNATURES --}}
      <div class="card mt-4 overflow-x-auto">
        <table class="w-full text-[11px] min-w-[720px]">
          <thead>
            <tr class="text-slate-500 font-bold uppercase text-[10px]">
              <th class="px-3 py-2 text-left border-b border-slate-200 w-1/3">Prepared by:</th>
              <th class="px-3 py-2 text-left border-b border-slate-200 w-1/3">Reviewed &amp; Certified True and Correct:</th>
              <th class="px-3 py-2 text-left border-b border-slate-200 w-1/3">APPROVED:</th>
            </tr>
          </thead>
          <tbody>
            <tr class="text-slate-800">
              <td class="px-3 py-3 align-top">
                <div class="font-bold text-[12px]">
                  {{ $signatories['chair_name'] ?: '—' }}
                </div>
                <div class="text-slate-500 text-[10px] mt-1">
                  {{ $signatories['chair_title'] ?? 'Dept. Chair' }}
                  @if(!empty($chairDepartment?->dept_name))
                    · {{ $chairDepartment->dept_name }}
                  @endif
                </div>
              </td>
              <td class="px-3 py-3 align-top">
                <div class="font-bold text-[12px]">
                  {{ $signatories['dean_name'] ?: '—' }}
                </div>
                <div class="text-slate-500 text-[10px] mt-1">
                  {{ $signatories['dean_title'] ?? 'Dean' }}
                  @if(!empty($chairDepartment))
                    · same college as department
                  @endif
                </div>
              </td>
              <td class="px-3 py-3 align-top">
                <div class="no-print">
                  <input type="text"
                         id="campus-director-input"
                         class="w-full rounded border border-slate-300 px-2 py-1 text-[12px] font-bold text-slate-800"
                         placeholder="Type Campus Director name"
                         value="{{ $signatories['campus_director'] ?? '' }}"
                         onchange="saveCampusDirector(this)"
                         onblur="saveCampusDirector(this)">
                  <div class="text-slate-500 text-[10px] mt-1">Campus Director (click to edit)</div>
                </div>
                <div class="hidden print:block">
                  <div class="font-bold text-[12px]">{{ $signatories['campus_director'] ?: '—' }}</div>
                  <div class="text-slate-500 text-[10px] mt-1">Campus Director</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<div class="toast no-print" id="toast"><span id="toast-msg"></span></div>

<script>
function showToast(msg) {
  const el = document.getElementById('toast');
  const m = document.getElementById('toast-msg');
  if (!el || !m) { alert(msg); return; }
  m.textContent = msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2200);
}

function applyMisFilters() {
  const section = document.getElementById('filter-section')?.value || '';
  const params = new URLSearchParams(window.location.search);
  if (section) params.set('section', section); else params.delete('section');
  const shift = params.get('shift') || 'day';
  params.set('shift', shift);
  window.location.search = params.toString();
}

function setMisShift(shift) {
  const params = new URLSearchParams(window.location.search);
  params.set('shift', shift);
  const section = document.getElementById('filter-section')?.value || '';
  if (section) params.set('section', section); else params.delete('section');
  window.location.search = params.toString();
}

const _misSaving = new Set();
function saveMisCode(input) {
  const id = input.getAttribute('data-sch-id');
  if (!id || _misSaving.has(id)) return;
  const code = (input.value || '').trim();
  _misSaving.add(id);

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  fetch(`/chair/mis/${id}/code`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrf,
      'X-Requested-With': 'XMLHttpRequest',
    },
    credentials: 'same-origin',
    body: JSON.stringify({ sch_mis_code: code }),
  })
    .then(async (res) => {
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.success) {
        showToast(data.message || 'Could not save MIS code');
        return;
      }
      showToast('MIS code saved');
    })
    .catch(() => showToast('Network error'))
    .finally(() => _misSaving.delete(id));
}
</script>
</body>
</html>
