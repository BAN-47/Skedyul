<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Room Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-hidden bg-slate-50 font-sans text-slate-900 antialiased">

<div class="flex h-screen overflow-hidden">
    @include('partials.chair_sidebar')

    <main class="flex-1 overflow-hidden">
        @include('partials.chair_header', [
            'title' => 'Room Management',
            'badgeText' => trim(collect([$academicYear->ay_academic_year ?? 'No AY', $semester->sem_name ?? 'No Semester'])->filter()->implode(' · '))
        ])

        <div class="page-content">
            <div class="mb-5">
                <div class="text-[20px] font-extrabold text-slate-900">Room Management</div>
                <div class="mt-1 text-[13px] text-slate-500">{{ $building }} availability</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr>
                                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Room</th>
                                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Type</th>
                                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Capacity</th>
                                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Status</th>
                                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Assigned Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roomData as $item)
                                @php
                                    $room = $item['room'];
                                    $sch = $item['schedules'];
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">{{ $room->room_name }}</td>
                                    <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">{{ $room->room_type }}</td>
                                    <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">{{ $room->room_capacity }}</td>
                                    <td class="border-b border-slate-100 px-3 py-3">
                                        @if ($item['is_booked'])
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-700">In Use</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Available</span>
                                        @endif
                                    </td>
                                    <td class="border-b border-slate-100 px-3 py-3 text-[12px] text-slate-500">
                                        @forelse($sch as $s)
                                            {{ $s->subject->subj_code ?? '—' }} — {{ substr($s->sch_day, 0, 3) }} {{ \Carbon\Carbon::parse($s->sch_start_time)->format('g:i A') }}<br>
                                        @empty
                                            —
                                        @endforelse
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-5 text-center text-sm text-slate-500">No rooms found for {{ $building }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3200);
}
</script>
</body>
</html>