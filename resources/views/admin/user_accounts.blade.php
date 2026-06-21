<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — User Accounts</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/user_accounts.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.admin_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">User Accounts</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
          Notifications <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">3</span>
        </button>
        <div id="notif-dropdown" style="display:none;position:absolute;top:44px;right:0;width:340px;background:var(--white);border:1px solid var(--border);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;overflow:hidden;">
          <div style="padding:14px 16px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;color:var(--text);">Notifications</div>
          <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
          <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
            <button onclick="markAllRead()" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

    <div id="page-user-accounts" class="page active">
      <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card" style="--accent:#2563eb"><div class="stat-icon">👥</div><div class="stat-label">Total Users</div><div class="stat-value">27</div><div class="stat-sub">All roles combined</div></div>
        <div class="stat-card" style="--accent:#16a34a"><div class="stat-icon">👨‍🏫</div><div class="stat-label">Faculty</div><div class="stat-value">19</div><div class="stat-sub">BSIS · BSIT · BIT-CT</div></div>
        <div class="stat-card" style="--accent:#d97706"><div class="stat-icon">📋</div><div class="stat-label">Dept. Chairs</div><div class="stat-value">3</div><div class="stat-sub">Active this semester</div></div>
        <div class="stat-card" style="--accent:#0891b2"><div class="stat-icon">✅</div><div class="stat-label">Active Accounts</div><div class="stat-value">25</div><div class="stat-sub">2 pending verification</div></div>
      </div>
      <div class="card">
        <div class="card-header">
          <div><div class="card-title">All User Accounts</div><div class="card-sub">Click a name to view full profile</div></div>
          <div style="display:flex;gap:8px;align-items:center;">
            <input class="field-input" placeholder="Search users..." style="width:200px;padding:8px 12px;font-size:13px;" oninput="filterUsers(this.value)">
            <button class="topbar-btn btn-primary" onclick="openModal('modal-add-user')">+ Add User</button>
          </div>
        </div>
        <div class="table-wrap"><table id="users-table">
          <tr><th>Name</th><th>Role</th><th>Department</th><th>Email</th><th>Employment</th><th>Status</th><th>Action</th></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Ma. Emie Villaceran','Dean','CCICT','villaceran@ctu.edu.ph','Full-time','Active','MV','#0891b2','Female · Filipino','Dean of the College of Computing, Information and Communications Technology. PhD in Information Technology.','Room 401, Admin Building','(032) 401-7777')">Ma. Emie Villaceran</span></td><td><span class="badge badge-blue">Dean</span></td><td>CCICT</td><td style="font-size:12px;color:var(--text3);">villaceran@ctu.edu.ph</td><td>Full-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Ma. Emie Villaceran','Dean','CCICT','villaceran@ctu.edu.ph','Full-time','Active','MV','#0891b2','Room 401, Admin Building','(032) 401-7777','Dean of the College of Computing, Information and Communications Technology. PhD in Information Technology.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Rodrigo Tan','Dept. Chair','BSIS','r.tan@ctu.edu.ph','Full-time','Active','RT','#d97706','Male · Filipino','Department Chair of Bachelor of Science in Information Systems. Masters in Computer Science, CTU.','Room 302, ICT Building','(032) 401-1234')">Rodrigo Tan</span></td><td><span class="badge badge-amber">Dept. Chair</span></td><td>BSIS</td><td style="font-size:12px;color:var(--text3);">r.tan@ctu.edu.ph</td><td>Full-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Rodrigo Tan','Dept. Chair','BSIS','r.tan@ctu.edu.ph','Full-time','Active','RT','#d97706','Room 302, ICT Building','(032) 401-1234','Department Chair of Bachelor of Science in Information Systems. Masters in Computer Science, CTU.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Lourdes Delos Santos','Dept. Chair','BSIT','l.delossantos@ctu.edu.ph','Full-time','Active','LD','#d97706','Female · Filipino','Department Chair of Bachelor of Science in Information Technology. Masters in IT Management.','Room 303, ICT Building','(032) 401-5678')">Lourdes Delos Santos</span></td><td><span class="badge badge-amber">Dept. Chair</span></td><td>BSIT</td><td style="font-size:12px;color:var(--text3);">l.delossantos@ctu.edu.ph</td><td>Full-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Lourdes Delos Santos','Dept. Chair','BSIT','l.delossantos@ctu.edu.ph','Full-time','Active','LD','#d97706','Room 303, ICT Building','(032) 401-5678','Department Chair of Bachelor of Science in Information Technology. Masters in IT Management.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Jerome Bautista','Faculty','BSIS','j.bautista@ctu.edu.ph','Full-time','Active','JB','#16a34a','Male · Filipino','Faculty member handling Programming and Systems subjects. Bachelor of Science in Computer Science, CTU.','Room 205, ICT Building','(032) 401-2222')">Jerome Bautista</span></td><td><span class="badge badge-grey">Faculty</span></td><td>BSIS</td><td style="font-size:12px;color:var(--text3);">j.bautista@ctu.edu.ph</td><td>Full-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Jerome Bautista','Faculty','BSIS','j.bautista@ctu.edu.ph','Full-time','Active','JB','#16a34a','Room 205, ICT Building','(032) 401-2222','Faculty member handling Programming and Systems subjects. Bachelor of Science in Computer Science, CTU.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Ana Reyes','Faculty','BSIT','a.reyes@ctu.edu.ph','Part-time','Pending','AR','#dc2626','Female · Filipino','Part-time faculty handling Web Development and Database subjects. Currently completing her Masters degree.','Room 205, ICT Building','(032) 401-3333')">Ana Reyes</span></td><td><span class="badge badge-grey">Faculty</span></td><td>BSIT</td><td style="font-size:12px;color:var(--text3);">a.reyes@ctu.edu.ph</td><td>Part-time</td><td><span class="badge badge-amber">Pending</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Ana Reyes','Faculty','BSIT','a.reyes@ctu.edu.ph','Part-time','Pending','AR','#dc2626','Room 205, ICT Building','(032) 401-3333','Part-time faculty handling Web Development and Database subjects. Currently completing her Masters degree.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Carlo Mendoza','Faculty','BSIT','c.mendoza@ctu.edu.ph','Full-time','Active','CM','#7c3aed','Male · Filipino','Faculty member specializing in Data Structures, Algorithms, and Computer Networks. MS Computer Science.','Room 206, ICT Building','(032) 401-4444')">Carlo Mendoza</span></td><td><span class="badge badge-grey">Faculty</span></td><td>BSIT</td><td style="font-size:12px;color:var(--text3);">c.mendoza@ctu.edu.ph</td><td>Full-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Carlo Mendoza','Faculty','BSIT','c.mendoza@ctu.edu.ph','Full-time','Active','CM','#7c3aed','Room 206, ICT Building','(032) 401-4444','Faculty member specializing in Data Structures, Algorithms, and Computer Networks. MS Computer Science.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Maria Santos','Faculty','BSIS','m.santos@ctu.edu.ph','Full-time','Active','MS','#0891b2','Female · Filipino','Faculty handling Systems Analysis, Project Management, and Capstone courses. MBA, CTU.','Room 205, ICT Building','(032) 401-5555')">Maria Santos</span></td><td><span class="badge badge-grey">Faculty</span></td><td>BSIS</td><td style="font-size:12px;color:var(--text3);">m.santos@ctu.edu.ph</td><td>Full-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Maria Santos','Faculty','BSIS','m.santos@ctu.edu.ph','Full-time','Active','MS','#0891b2','Room 205, ICT Building','(032) 401-5555','Faculty handling Systems Analysis, Project Management, and Capstone courses. MBA, CTU.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Noel Garcia','Faculty','BIT-CT','n.garcia@ctu.edu.ph','Full-time','Active','NG','#16a34a','Male · Filipino','Faculty handling General Education and Technical Communication subjects. MA in Communication.','Room 101, Main Building','(032) 401-6666')">Noel Garcia</span></td><td><span class="badge badge-grey">Faculty</span></td><td>BIT-CT</td><td style="font-size:12px;color:var(--text3);">n.garcia@ctu.edu.ph</td><td>Full-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Noel Garcia','Faculty','BIT-CT','n.garcia@ctu.edu.ph','Full-time','Active','NG','#16a34a','Room 101, Main Building','(032) 401-6666','Faculty handling General Education and Technical Communication subjects. MA in Communication.')">Edit</button></td></tr>
          <tr class="user-row"><td><span class="user-name-link" onclick="openUserProfile('Liza Cruz','Faculty','BSIT','l.cruz@ctu.edu.ph','Part-time','Active','LC','#2563eb','Female · Filipino','Newly onboarded part-time faculty handling Purposive Communication. BA in Communications.','Room 102, Main Building','(032) 401-7788')">Liza Cruz</span></td><td><span class="badge badge-grey">Faculty</span></td><td>BSIT</td><td style="font-size:12px;color:var(--text3);">l.cruz@ctu.edu.ph</td><td>Part-time</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditUser('Liza Cruz','Faculty','BSIT','l.cruz@ctu.edu.ph','Part-time','Active','LC','#2563eb','Room 102, Main Building','(032) 401-7788','Newly onboarded part-time faculty handling Purposive Communication. BA in Communications.')">Edit</button></td></tr>
        </table></div>
        <!-- Pagination -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
          <div id="page-info" style="font-size:13px;color:var(--text3);"></div>
          <div style="display:flex;align-items:center;gap:6px;">
            <button class="topbar-btn btn-secondary" id="btn-prev" onclick="changePage(-1)" style="padding:6px 14px;font-size:13px;">← Prev</button>
            <div id="page-numbers" style="display:flex;gap:4px;"></div>
            <button class="topbar-btn btn-secondary" id="btn-next" onclick="changePage(1)" style="padding:6px 14px;font-size:13px;">Next →</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ADD USER MODAL -->
<div class="modal-overlay" id="modal-add-user">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add New User</div>
      <button class="modal-close" onclick="closeModal('modal-add-user')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Full Name</label><input class="field-input" placeholder="e.g. Juan Dela Cruz"></div>
      <div class="field-group"><label class="field-label">Email</label><input class="field-input" placeholder="user@ctu.edu.ph" type="email"></div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Role</label><select class="field-select"><option>Faculty Member</option><option>Department Chair</option><option>Dean</option><option>Technical Administrator</option></select></div>
      <div class="field-group"><label class="field-label">Department</label><select class="field-select"><option>BSIS</option><option>BSIT</option><option>BIT-CT</option><option>CCICT</option></select></div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Password</label><input class="field-input" type="password" placeholder="Temporary password"></div>
      <div class="field-group"><label class="field-label">Employment Type</label><select class="field-select"><option>Full-time</option><option>Part-time</option></select></div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-add-user')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-add-user');showToast('User account created successfully!')">Create Account</button>
    </div>
  </div>
</div>

<!-- EDIT USER MODAL -->
<div class="modal-overlay" id="modal-edit-user">
  <div class="modal" style="width:520px;">
    <div class="modal-header">
      <div class="modal-title">Edit User</div>
      <button class="modal-close" onclick="closeModal('modal-edit-user')">✕</button>
    </div>
    <div style="display:flex;align-items:center;gap:16px;padding:16px;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:12px;margin-bottom:20px;">
      <div id="edit-avatar" style="width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0;border:2px solid rgba(255,255,255,0.2);"></div>
      <div>
        <div id="edit-avatar-name" style="font-size:16px;font-weight:800;color:#fff;"></div>
        <div id="edit-avatar-role" style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:2px;"></div>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Full Name</label><input class="field-input" id="edit-name" placeholder="Full Name"></div>
      <div class="field-group"><label class="field-label">Email</label><input class="field-input" id="edit-email" type="email" placeholder="email@ctu.edu.ph"></div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Role</label>
        <select class="field-select" id="edit-role">
          <option>Faculty</option><option>Dept. Chair</option><option>Dean</option><option>Technical Administrator</option>
        </select>
      </div>
      <div class="field-group"><label class="field-label">Department</label>
        <select class="field-select" id="edit-dept">
          <option>BSIS</option><option>BSIT</option><option>BIT-CT</option><option>CCICT</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Employment Type</label>
        <select class="field-select" id="edit-employment">
          <option>Full-time</option><option>Part-time</option>
        </select>
      </div>
      <div class="field-group"><label class="field-label">Status</label>
        <select class="field-select" id="edit-status">
          <option>Active</option><option>Pending</option><option>Inactive</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Office Location</label><input class="field-input" id="edit-office" placeholder="e.g. Room 205, ICT Building"></div>
      <div class="field-group"><label class="field-label">Contact Number</label><input class="field-input" id="edit-contact" placeholder="e.g. (032) 401-0000"></div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">About / Bio</label><textarea class="field-input" id="edit-about" rows="3" style="resize:vertical;"></textarea></div>
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:4px;">
      ⚠️ Changes will be reflected immediately across the system.
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-user')">Cancel</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="deleteCurrentUser('modal-edit-user')">Delete</button>
      <button class="topbar-btn btn-primary" id="edit-save-btn">Save Changes</button>
    </div>
  </div>
</div>

<!-- USER PROFILE MODAL -->
<div class="modal-overlay" id="modal-user-profile">
  <div class="modal" style="width:520px;">
    <div class="modal-header">
      <div class="modal-title">User Profile</div>
      <button class="modal-close" onclick="closeModal('modal-user-profile')">✕</button>
    </div>
    <!-- Profile Header -->
    <div style="display:flex;align-items:center;gap:20px;padding:20px;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:14px;margin-bottom:20px;">
      <div id="profile-avatar" style="width:80px;height:80px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#fff;flex-shrink:0;border:3px solid rgba(255,255,255,0.2);"></div>
      <div>
        <div id="profile-name" style="font-size:20px;font-weight:800;color:#fff;"></div>
        <div id="profile-role" style="font-size:13px;color:rgba(255,255,255,0.6);margin-top:3px;"></div>
        <div id="profile-dept" style="font-size:12px;color:rgba(255,255,255,0.4);margin-top:2px;"></div>
        <div id="profile-status-badge" style="margin-top:8px;"></div>
      </div>
    </div>
    <!-- Info Grid -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Email</div>
        <div id="profile-email" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Employment</div>
        <div id="profile-employment" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Personal Info</div>
        <div id="profile-personal" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Contact</div>
        <div id="profile-contact" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      </div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:16px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">About</div>
      <div id="profile-about" style="font-size:13px;color:var(--text2);line-height:1.6;"></div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:20px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Office Location</div>
      <div id="profile-office" style="font-size:13px;font-weight:600;color:var(--text);"></div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-user-profile')">Close</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-user-profile');showToast('Opening edit form...')">Edit Profile</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const ADMIN_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
  { dot:'var(--green)', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
  { dot:'var(--blue)', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = ADMIN_NOTIFS.map((n) => `
    <div class="notif-drop-item ${n.unread?'unread':''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div><div class="notif-drop-text">${n.text}</div><div class="notif-drop-time">${n.time}</div></div>
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
  const badge = document.getElementById('notif-count');
  if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline' : 'none'; }
}
renderNotifList();

// ── MODALS ─────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].value === value || sel.options[i].text === value) {
      sel.selectedIndex = i; break;
    }
  }
}

// ── EDIT USER ──────────────────────────────────────────────────────────────────
function openEditUser(name, role, dept, email, employment, status, avatar, color, office, contact, about) {
  document.getElementById('edit-avatar').textContent = avatar;
  document.getElementById('edit-avatar').style.background = color;
  document.getElementById('edit-avatar-name').textContent = name;
  document.getElementById('edit-avatar-role').textContent = role + ' · ' + dept;
  document.getElementById('edit-name').value = name;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-office').value = office;
  document.getElementById('edit-contact').value = contact;
  document.getElementById('edit-about').value = about;
  setSelectValue('edit-role', role);
  setSelectValue('edit-dept', dept);
  setSelectValue('edit-employment', employment);
  setSelectValue('edit-status', status);
  document.getElementById('edit-save-btn').onclick = () => {
    closeModal('modal-edit-user');
    showToast('Changes saved for ' + document.getElementById('edit-name').value + ' ✓');
  };
  openModal('modal-edit-user');
}

// ── USER PROFILE ──────────────────────────────────────────────────────────────
function openUserProfile(name, role, dept, email, employment, status, avatar, color, personal, about, office, contact) {
  document.getElementById('profile-avatar').textContent = avatar;
  document.getElementById('profile-avatar').style.background = color;
  document.getElementById('profile-name').textContent = name;
  document.getElementById('profile-role').textContent = role;
  document.getElementById('profile-dept').textContent = dept;
  document.getElementById('profile-email').textContent = email;
  document.getElementById('profile-employment').textContent = employment;
  document.getElementById('profile-personal').textContent = personal;
  document.getElementById('profile-about').textContent = about;
  document.getElementById('profile-office').textContent = office;
  document.getElementById('profile-contact').textContent = contact;
  const statusColors = { Active:'badge-green', Pending:'badge-amber', Inactive:'badge-red' };
  document.getElementById('profile-status-badge').innerHTML = `<span class="badge ${statusColors[status]||'badge-grey'}">${status}</span>`;
  openModal('modal-user-profile');
}

// ── DELETE USER ───────────────────────────────────────────────────────────────
function deleteCurrentUser(modalId) {
  const name = document.getElementById('edit-name')
    ? document.getElementById('edit-name').value
    : document.getElementById('profile-name').textContent;
  if (!confirm('Delete user: ' + name + '?\nThis action cannot be undone.')) return;
  closeModal(modalId);
  const rows = document.querySelectorAll('#users-table .user-row');
  rows.forEach(row => {
    if (row.textContent.includes(name)) {
      row.style.transition = 'opacity 0.3s';
      row.style.opacity = '0';
      setTimeout(() => { row.remove(); initPagination(); }, 300);
    }
  });
  showToast(name + ' deleted successfully.');
}

// ── PAGINATION ─────────────────────────────────────────────────────────────────
let currentPage = 1;
const rowsPerPage = 10;
let filteredRows = [];

function initPagination() {
  filteredRows = Array.from(document.querySelectorAll('#users-table .user-row'));
  renderPage();
}

function renderPage() {
  const total = filteredRows.length;
  const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
  if (currentPage > totalPages) currentPage = totalPages;

  filteredRows.forEach((row, i) => {
    const start = (currentPage - 1) * rowsPerPage;
    row.style.display = (i >= start && i < start + rowsPerPage) ? '' : 'none';
  });

  const start = Math.min((currentPage - 1) * rowsPerPage + 1, total);
  const end = Math.min(currentPage * rowsPerPage, total);
  document.getElementById('page-info').textContent = total === 0 ? 'No results found' : `Showing ${start}–${end} of ${total} users`;

  document.getElementById('btn-prev').disabled = currentPage === 1;
  document.getElementById('btn-prev').style.opacity = currentPage === 1 ? '0.4' : '1';
  document.getElementById('btn-next').disabled = currentPage === totalPages;
  document.getElementById('btn-next').style.opacity = currentPage === totalPages ? '0.4' : '1';

  const container = document.getElementById('page-numbers');
  container.innerHTML = '';
  for (let p = 1; p <= totalPages; p++) {
    const btn = document.createElement('button');
    btn.textContent = p;
    btn.className = 'topbar-btn ' + (p === currentPage ? 'btn-primary' : 'btn-secondary');
    btn.style.cssText = 'padding:6px 11px;font-size:13px;min-width:36px;';
    btn.onclick = () => { currentPage = p; renderPage(); };
    container.appendChild(btn);
  }
}

function changePage(dir) {
  const total = filteredRows.length;
  const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
  currentPage = Math.max(1, Math.min(currentPage + dir, totalPages));
  renderPage();
}

function filterUsers(query) {
  const q = query.toLowerCase();
  const all = Array.from(document.querySelectorAll('#users-table .user-row'));
  filteredRows = q ? all.filter(row => row.textContent.toLowerCase().includes(q)) : all;
  all.forEach(r => r.style.display = 'none');
  currentPage = 1;
  renderPage();
}

initPagination();
</script>
</body>
</html>