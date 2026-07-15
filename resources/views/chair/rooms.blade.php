<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Room Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/chair/rooms.css') }}">
</head>
<style>
    html,
    body {
        overflow: hidden;
    }

    .sidebar-nav {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .sidebar-nav::-webkit-scrollbar {
        display: none;
    }

    .page-content {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .page-content::-webkit-scrollbar {
        display: none;
    }
</style>

<body>

    <div class="screen active" style="display:flex;">
        @include('partials.chair_sidebar')

        <!-- MAIN -->
        <div class="main">

            <!-- TOPBAR -->
            <div class="topbar">
                <div class="topbar-title">Room Management</div>
                <div class="topbar-semester-badge">
                    {{ $academicYear->ay_academic_year ?? 'No AY' }} · {{ $semester->sem_name ?? 'No Semester' }}
                </div>
                <div id="topbar-notif-bell" style="position:relative;">
                    <button onclick="toggleNotifDropdown()" class="topbar-notif-btn">
                        Notifications
                        <span id="notif-dot" class="topbar-notif-dot"></span>
                    </button>
                    <div id="notif-dropdown" class="notif-dropdown">
                        <div class="notif-dropdown-header">Notifications</div>
                        <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
                        <div class="notif-dropdown-footer">
                            <button onclick="markAllRead()" class="notif-mark-all-btn">Mark all as read</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-content" style="display: block;animation: fadeIn .3s ease;">

                <!-- Page header -->
                <div class="page-header">
                    <div>
                        <div class="page-heading">Room Management</div>
                        <div class="page-subheading">{{ $building }} · {{ $academicYear->ay_academic_year ?? '' }} ·
                            {{ $semester->sem_name ?? '' }}</div>
                    </div>
                </div>

                <!-- Stat cards -->
                <div class="stat-grid" style="margin-bottom:24px;">
                    <div class="stat-card" style="--accent:#2563eb">
                        <div class="stat-label">Total Rooms</div>
                        <div class="stat-value">{{ $totalRooms }}</div>
                        <div class="stat-sub">{{ $building }}</div>
                    </div>
                    <div class="stat-card" style="--accent:#16a34a">
                        <div class="stat-label">Available</div>
                        <div class="stat-value">{{ $availableCount }}</div>
                        <div class="stat-sub">Ready to assign</div>
                    </div>
                    <div class="stat-card" style="--accent:#d97706">
                        <div class="stat-label">In Use</div>
                        <div class="stat-value">{{ $inUseCount }}</div>
                        <div class="stat-sub">Currently assigned</div>
                    </div>
                    <div class="stat-card" style="--accent:#0891b2">
                        <div class="stat-label">Laboratories</div>
                        <div class="stat-value">{{ $laboratories }}</div>
                        <div class="stat-sub">Lab rooms</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start;">

                    <!-- Room table -->
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Room Overview</div>
                                <div class="card-sub">View room assignments and availability</div>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Type</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Assigned Subject</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roomData as $item)
                                        @php
                                            $room = $item['room'];
                                            $sch = $item['schedules'];
                                        @endphp
                                        <tr>
                                            <td><b>{{ $room->room_name }}</b></td>
                                            <td>{{ $room->room_type }}</td>
                                            <td>{{ $room->room_capacity }}</td>
                                            <td>
                                                @if ($item['is_booked'])
                                                    <span class="badge badge-amber">In Use</span>
                                                @else
                                                    <span class="badge badge-green">Available</span>
                                                @endif
                                            </td>
                                            <td style="font-size:12px;color:var(--text3);">
                                                @forelse($sch as $s)
                                                    {{ $s->subject->subj_code ?? '—' }} —
                                                    {{ substr($s->sch_day, 0, 3) }}
                                                    {{ \Carbon\Carbon::parse($s->sch_start_time)->format('g:i A') }}<br>
                                                @empty
                                                    —
                                                @endforelse
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="text-align:center;color:var(--text3);">No rooms
                                                found for {{ $building }}.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Room availability sidebar -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Room Availability</div>
                        </div>

                        @foreach ($roomData as $item)
                            @php
                                $room = $item['room'];
                                $sch = $item['schedules'];
                            @endphp
                            <div class="workload-item">
                                <div class="workload-header">
                                    <div class="workload-name">{{ $room->room_name }}</div>
                                    <div class="workload-val"
                                        style="color:{{ $item['is_booked'] ? 'var(--amber)' : 'var(--green)' }};">
                                        {{ $item['is_booked'] ? 'In Use' : 'Free' }}
                                    </div>
                                </div>
                                <div style="font-size:11px;color:var(--text3);margin-bottom:4px;">
                                    {{ $room->room_type }} · Cap. {{ $room->room_capacity }}</div>
                                <div class="room-usage-bar">
                                    <div class="room-usage-fill"
                                        style="width:{{ $item['is_booked'] ? 100 : 0 }}%;background:{{ $item['is_booked'] ? 'var(--amber)' : 'var(--green)' }};">
                                    </div>
                                </div>
                                <div
                                    style="font-size:11px;color:{{ $item['is_booked'] ? 'var(--amber)' : 'var(--green)' }};margin-top:3px;">
                                    @forelse($sch as $s)
                                        {{ $s->subject->subj_code ?? '—' }} — {{ substr($s->sch_day, 0, 3) }}
                                        {{ \Carbon\Carbon::parse($s->sch_start_time)->format('g:i A') }}
                                    @empty
                                        No subject assigned
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        // ── NOTIFICATIONS ─────────────────────────────────────────────────────
        const CHAIR_NOTIFS = [{
                dot: 'var(--red)',
                text: '<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.',
                time: 'Today, 08:30 AM',
                unread: true
            },
            {
                dot: 'var(--amber)',
                text: '<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).',
                time: 'Today, 08:00 AM',
                unread: true
            },
            {
                dot: 'var(--blue)',
                text: '<b>Reminder</b> — Schedule submission deadline is Friday.',
                time: 'Yesterday, 4:00 PM',
                unread: false
            },
        ];

        function renderNotifList() {
            const list = document.getElementById('notif-list');
            if (!list) return;
            list.innerHTML = CHAIR_NOTIFS.map(n => `
    <div class="notif-drop-item ${n.unread ? 'unread' : ''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div>
        <div class="notif-drop-text">${n.text}</div>
        <div class="notif-drop-time">${n.time}</div>
      </div>
    </div>`).join('');
            updateNotifCount();
        }
        let notifOpen = false;

        function toggleNotifDropdown() {
            notifOpen = !notifOpen;
            const dd = document.getElementById('notif-dropdown');
            if (dd) dd.classList.toggle('open', notifOpen);
        }
        document.addEventListener('click', e => {
            const bell = document.getElementById('topbar-notif-bell');
            if (bell && !bell.contains(e.target)) {
                notifOpen = false;
                const dd = document.getElementById('notif-dropdown');
                if (dd) dd.classList.remove('open');
            }
        });

        function markRead(el) {
            el.classList.remove('unread');
            updateNotifCount();
        }

        function markAllRead() {
            document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
            updateNotifCount();
        }

        function updateNotifCount() {
            const unread = document.querySelectorAll('.notif-drop-item.unread').length;
            const dot = document.getElementById('notif-dot');
            if (dot) dot.style.display = unread > 0 ? 'block' : 'none';
        }
        renderNotifList();

        // ── TOAST ─────────────────────────────────────────────────────────────
        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3200);
        }
    </script>
</body>

</html>
