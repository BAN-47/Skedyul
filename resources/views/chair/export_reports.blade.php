<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Export Reports</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">

<div class="flex h-screen overflow-hidden">
  @include('partials.chair_sidebar')

  <main class="flex-1 overflow-hidden">
    @include('partials.chair_header', ['title' => 'Export Reports', 'badgeText' => 'BSIS Department'])

    <div id="page-reports" class="page-content">
      <div class="mb-5">
        <div class="text-[20px] font-extrabold text-slate-900">Export Reports</div>
        <div class="mt-1 text-[13px] text-slate-500">BSIS Department · AY 2025–2026 · 1st Semester</div>
      </div>

      <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-[0_10px_30px_rgba(37,99,235,0.10)]" onmouseover="this.style.boxShadow='0 10px 30px rgba(37,99,235,.12)'" onmouseout="this.style.boxShadow=''">
          <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-xl">📅</div>
          <div class="mb-1 text-sm font-bold text-slate-900">Faculty Schedule</div>
          <div class="mb-4 text-xs text-slate-500">Individual timetables per faculty member</div>
          <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-[12px] font-semibold text-white transition hover:bg-blue-700" onclick="exportReport('Faculty Schedule','PDF')">PDF</button>
            <button class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-50" onclick="exportReport('Faculty Schedule','Excel')">Excel</button>
          </div>
        </div>

        <div class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-[0_10px_30px_rgba(37,99,235,0.10)]" onmouseover="this.style.boxShadow='0 10px 30px rgba(37,99,235,.12)'" onmouseout="this.style.boxShadow=''">
          <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-xl">📋</div>
          <div class="mb-1 text-sm font-bold text-slate-900">Section Master List</div>
          <div class="mb-4 text-xs text-slate-500">Complete schedule per section/class</div>
          <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-[12px] font-semibold text-white transition hover:bg-blue-700" onclick="exportReport('Section Master List','PDF')">PDF</button>
            <button class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-50" onclick="exportReport('Section Master List','Excel')">Excel</button>
          </div>
        </div>

        <div class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-[0_10px_30px_rgba(37,99,235,0.10)]" onmouseover="this.style.boxShadow='0 10px 30px rgba(37,99,235,.12)'" onmouseout="this.style.boxShadow=''">
          <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-xl">📊</div>
          <div class="mb-1 text-sm font-bold text-slate-900">Workload Summary</div>
          <div class="mb-4 text-xs text-slate-500">Units per faculty with load breakdown</div>
          <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-[12px] font-semibold text-white transition hover:bg-blue-700" onclick="exportReport('Workload Summary','PDF')">PDF</button>
            <button class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-50" onclick="exportReport('Workload Summary','Excel')">Excel</button>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
          <div>
            <div class="text-[15px] font-bold text-slate-900">Recent Exports</div>
            <div class="text-[12px] text-slate-500">Last generated reports this semester</div>
          </div>
        </div>
        <div id="recent-exports-list" class="space-y-2">
          <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
            <div class="h-2.5 w-2.5 rounded-full bg-blue-500"></div>
            <div class="flex-1 text-sm text-slate-700">Faculty Schedule — PDF</div>
            <div class="text-[11px] text-slate-500">Jun 20, 2026 · 10:34 AM</div>
          </div>
          <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
            <div class="h-2.5 w-2.5 rounded-full bg-emerald-500"></div>
            <div class="flex-1 text-sm text-slate-700">Section Master List — Excel</div>
            <div class="text-[11px] text-slate-500">Jun 18, 2026 · 3:12 PM</div>
          </div>
          <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
            <div class="h-2.5 w-2.5 rounded-full bg-amber-500"></div>
            <div class="flex-1 text-sm text-slate-700">Workload Summary — PDF</div>
            <div class="text-[11px] text-slate-500">Jun 15, 2026 · 9:00 AM</div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
const CHAIR_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>Reminder</b> — Schedule submission deadline is Friday.', time:'Yesterday, 4:00 PM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = CHAIR_NOTIFS.map(n => `
    <div class="flex items-start gap-3 border-b border-slate-100 px-4 py-3 ${n.unread ? 'bg-slate-50' : ''}" onclick="markRead(this)">
      <div class="mt-1.5 h-2.5 w-2.5 rounded-full" style="background:${n.dot};"></div>
      <div class="min-w-0 flex-1">
        <div class="text-[12.5px] leading-relaxed text-slate-600">${n.text}</div>
        <div class="mt-1 text-[11px] text-slate-400">${n.time}</div>
      </div>
    </div>`).join('');
  updateNotifCount();
}

let notifOpen = false;
function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  const dd = document.getElementById('notif-dropdown');
  if (dd) dd.style.display = notifOpen ? 'block' : 'none';
}
document.addEventListener('click', e => {
  const bell = document.getElementById('topbar-notif-bell');
  if (bell && !bell.contains(e.target)) {
    notifOpen = false;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.style.display = 'none';
  }
});
function markRead(el) { el.classList.remove('bg-slate-50'); updateNotifCount(); }
function markAllRead() {
  document.querySelectorAll('#notif-list > div').forEach(el => el.classList.remove('bg-slate-50'));
  updateNotifCount();
}
function updateNotifCount() {
  const unread = document.querySelectorAll('#notif-list > div.bg-slate-50').length;
  const badge = document.getElementById('notif-count');
  if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline-flex' : 'none'; }
}
renderNotifList();

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

const dotColors = { 'Faculty Schedule':'#3b82f6', 'Section Master List':'#10b981', 'Workload Summary':'#f59e0b' };

function exportReport(name, format) {
  showToast(name + ' — ' + format + ' generated successfully!');
  const list = document.getElementById('recent-exports-list');
  const now = new Date();
  const time = now.toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit', hour12:true });
  const item = document.createElement('div');
  item.className = 'flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5';
  item.innerHTML = `
    <div class="h-2.5 w-2.5 rounded-full" style="background:${dotColors[name] || '#3b82f6'};"></div>
    <div class="flex-1 text-sm text-slate-700">${name} — ${format}</div>
    <div class="text-[11px] text-slate-500">${time}</div>`;
  list.insertBefore(item, list.firstChild);
}
</script>
</body>
</html>