<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Submit to Dean</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">

<div class="flex h-screen overflow-hidden">
  @include('partials.chair_sidebar')

  <main class="flex-1 overflow-hidden">
    @include('partials.chair_header', ['title' => 'Submit to Dean', 'badgeText' => 'BSIS Department'])

    <div id="page-submit" class="page-content">
      <div class="mb-5">
        <div class="text-[20px] font-extrabold text-slate-900">Submit Schedule to Dean</div>
        <div class="mt-1 text-[13px] text-slate-500">Final review before sending to Dean Villaceran</div>
      </div>

      <div id="submit-blocked" class="mb-5 flex items-center gap-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <div class="leading-relaxed"><strong class="font-bold">Submission Blocked</strong> — 1 unresolved conflict exists. The schedule cannot be published or sent to the Dean until all conflicts are fixed.</div>
        <a href="{{ route('chair.conflict_checker') }}" class="ml-auto whitespace-nowrap rounded-lg bg-red-100 px-3 py-1.5 text-[12px] font-semibold text-red-600 transition hover:bg-red-200">Go Fix</a>
      </div>

      <div id="submit-ready" class="mb-5 hidden items-center gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        <div><strong class="font-bold">Ready to submit!</strong> All conflicts resolved. Click Submit below to send to Dean Villaceran.</div>
      </div>

      <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-3">
          <div>
            <div class="text-[15px] font-bold text-slate-900">Submission Checklist</div>
            <div class="mt-1 text-[12px] text-slate-500">All items must pass before submission</div>
          </div>
          <span id="submit-overall-badge" class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-600">Not Ready</span>
        </div>

        <div class="flex items-center gap-3 border-b border-slate-100 py-3">
          <div class="h-2.5 w-2.5 rounded-full bg-amber-500"></div>
          <div class="flex-1 text-sm text-slate-700">All subjects assigned to faculty</div>
          <div class="text-[12px] text-amber-600">1 unassigned (CC 501)</div>
        </div>
        <div class="flex items-center gap-3 border-b border-slate-100 py-3">
          <div class="h-2.5 w-2.5 rounded-full bg-red-500" id="chk-conflict-dot"></div>
          <div class="flex-1 text-sm text-slate-700">No scheduling conflicts</div>
          <div class="text-[12px] text-red-600" id="chk-conflict-val">1 conflict — cannot publish</div>
        </div>
        <div class="flex items-center gap-3 border-b border-slate-100 py-3">
          <div class="h-2.5 w-2.5 rounded-full bg-emerald-500"></div>
          <div class="flex-1 text-sm text-slate-700">All faculty within 30-unit load</div>
          <div class="text-[12px] text-emerald-600">Pass</div>
        </div>
        <div class="flex items-center gap-3 border-b border-slate-100 py-3">
          <div class="h-2.5 w-2.5 rounded-full bg-emerald-500"></div>
          <div class="flex-1 text-sm text-slate-700">All sections have complete subjects</div>
          <div class="text-[12px] text-emerald-600">Pass</div>
        </div>
        <div class="flex items-center gap-3 py-3">
          <div class="h-2.5 w-2.5 rounded-full bg-emerald-500"></div>
          <div class="flex-1 text-sm text-slate-700">All rooms assigned</div>
          <div class="text-[12px] text-emerald-600">Pass</div>
        </div>

        <div class="mt-5 flex justify-end gap-3 border-t border-slate-200 pt-4">
          <button class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-50" onclick="showToast('Draft PDF exported!')">Export Draft</button>
          <button class="rounded-xl bg-red-600 px-3.5 py-2 text-[12px] font-semibold text-white transition hover:bg-red-700" id="btn-submit-dean" onclick="attemptSubmit()">Submit to Dean</button>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-3">
          <div>
            <div class="text-[15px] font-bold text-slate-900">Schedule Summary</div>
            <div class="mt-1 text-[12px] text-slate-500">BSIS Department · AY 2025–2026 · 1st Semester</div>
          </div>
          <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600">Draft</span>
        </div>

        <div class="mb-4 grid gap-3 md:grid-cols-3">
          <div class="rounded-xl bg-slate-100 p-4">
            <div class="mb-2 text-[10px] font-bold uppercase tracking-[0.8px] text-slate-400">Faculty</div>
            <div class="text-[22px] font-extrabold text-slate-900">4</div>
            <div class="text-[11px] text-slate-500">assigned</div>
          </div>
          <div class="rounded-xl bg-slate-100 p-4">
            <div class="mb-2 text-[10px] font-bold uppercase tracking-[0.8px] text-slate-400">Subjects</div>
            <div class="text-[22px] font-extrabold text-slate-900">6</div>
            <div class="text-[11px] text-slate-500">total (1 unassigned)</div>
          </div>
          <div class="rounded-xl bg-slate-100 p-4">
            <div class="mb-2 text-[10px] font-bold uppercase tracking-[0.8px] text-slate-400">Sections</div>
            <div class="text-[22px] font-extrabold text-slate-900">4</div>
            <div class="text-[11px] text-slate-500">BSIS 1-A to 4-A</div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Faculty</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Subjects</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Total Load</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr class="hover:bg-slate-50">
                <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">Jerome Bautista</td>
                <td class="border-b border-slate-100 px-3 py-3 text-[12px] text-slate-600">CC 313, CC 401</td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="font-mono text-sm font-bold text-emerald-600">24u</span></td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">OK</span></td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">Felicitas Lagman</td>
                <td class="border-b border-slate-100 px-3 py-3 text-[12px] text-slate-600">IT 302, CC 202</td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="font-mono text-sm font-bold text-amber-600">27u</span></td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-700">Near Max</span></td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">Maria Santos</td>
                <td class="border-b border-slate-100 px-3 py-3 text-[12px] text-slate-600">GE 102, IT 101</td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="font-mono text-sm font-bold text-blue-600">18u</span></td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-600">Has Conflict</span></td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">Ana Reyes</td>
                <td class="border-b border-slate-100 px-3 py-3 text-[12px] text-slate-600">IT 401, GE 101</td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="font-mono text-sm font-bold text-cyan-600">18u</span></td>
                <td class="border-b border-slate-100 px-3 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-500">Part-time</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
let hasConflict = true;
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
function markAllRead() { document.querySelectorAll('#notif-list > div').forEach(el => el.classList.remove('bg-slate-50')); updateNotifCount(); }
function updateNotifCount() {
  const unread = document.querySelectorAll('#notif-list > div.bg-slate-50').length;
  const badge = document.getElementById('notif-count');
  if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline-flex' : 'none'; }
}
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
renderNotifList();
</script>
</body>
</html>

// ── ATTEMPT SUBMIT ─────────────────────────────────────────────────────────────
function attemptSubmit() {
  if (hasConflict) {
    showToast('Cannot submit — fix the conflict first!');
    return;
  }
  const btn = document.getElementById('btn-submit-dean');
  btn.textContent = 'Submitted ✓';
  btn.disabled = true;
  btn.className = 'topbar-btn btn-secondary';
  const badge = document.getElementById('submit-overall-badge');
  if (badge) { badge.textContent = 'Submitted'; badge.className = 'badge badge-green'; }
  document.getElementById('submit-ready').innerHTML = '<div class="success-alert-text"><strong>Schedule submitted!</strong> Sent to Dean Villaceran. Awaiting approval.</div>';
  showToast('Schedule submitted to Dean Villaceran! Awaiting approval.');
}

// ── SIMULATE CONFLICT RESOLVED (for demo — wire to real state later) ──────────
// Call clearConflict() from console or a button to test the "ready" state
function clearConflict() {
  hasConflict = false;
  document.getElementById('submit-blocked').style.display = 'none';
  document.getElementById('submit-ready').style.display = 'flex';
  document.getElementById('chk-conflict-dot').style.background = 'var(--green)';
  document.getElementById('chk-conflict-val').style.color = 'var(--green)';
  document.getElementById('chk-conflict-val').textContent = 'Pass — no conflicts';
  const btn = document.getElementById('btn-submit-dean');
  btn.className = 'topbar-btn btn-primary';
  btn.textContent = 'Submit to Dean';
  const badge = document.getElementById('submit-overall-badge');
  if (badge) { badge.textContent = 'Ready'; badge.className = 'badge badge-green'; }
}
</script>
</body>
</html>