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
        <button type="button" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
          Notifications <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">3</span>
        </button>
        <div id="notif-dropdown" style="display:none;position:absolute;top:44px;right:0;width:340px;background:var(--white);border:1px solid var(--border);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;overflow:hidden;">
          <div style="padding:14px 16px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;color:var(--text);">Notifications</div>
          <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
          <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
            <button type="button" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

    {{-- ── FLASH MESSAGES ─────────────────────────────────────────────────── --}}
    @if(session("success"))
      <script>document.addEventListener("DOMContentLoaded", () => showToast("✅ {{ session("success") }}"));</script>
    @endif
    @if(session("error"))
      <script>document.addEventListener("DOMContentLoaded", () => showToast("❌ {{ session("error") }}"));</script>
    @endif

    <div id="page-user-accounts" class="page active">

      {{-- ── STAT CARDS ──────────────────────────────────────────────────── --}}
      <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card" style="--accent:#2563eb">
          <div class="stat-icon">👥</div>
          <div class="stat-label">Total Users</div>
          <div class="stat-value">{{ $users->count() }}</div>
          <div class="stat-sub">All roles combined</div>
        </div>
        <div class="stat-card" style="--accent:#16a34a">
          <div class="stat-icon">👨‍🏫</div>
          <div class="stat-label">Faculty</div>
          <div class="stat-value">{{ $users->where('usr_role','faculty')->count() }}</div>
          <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
        </div>
        <div class="stat-card" style="--accent:#d97706">
          <div class="stat-icon">📋</div>
          <div class="stat-label">Dept. Chairs</div>
          <div class="stat-value">{{ $users->where('usr_role','department_chair')->count() }}</div>
          <div class="stat-sub">Active this semester</div>
        </div>
        <div class="stat-card" style="--accent:#0891b2">
          <div class="stat-icon">✅</div>
          <div class="stat-label">Active Accounts</div>
          <div class="stat-value">{{ $users->where('usr_is_active', true)->count() }}</div>
          <div class="stat-sub">{{ $users->where('usr_is_active', false)->count() }} inactive</div>
        </div>
      </div>

      {{-- ── USER TABLE CARD ─────────────────────────────────────────────── --}}
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">All User Accounts</div>
            <div class="card-sub">Click a name to view full profile</div>
          </div>
          <div style="display:flex;gap:8px;align-items:center;">
            <input class="field-input" placeholder="Search users..." style="width:200px;padding:8px 12px;font-size:13px;">
            <a class="topbar-btn btn-primary" href="#add-user-form" style="text-decoration:none;display:inline-flex;align-items:center;">+ Add User</a>
          </div>
        </div>

        <div id="add-user-form" style="margin-bottom:16px;padding:16px;border:1px solid var(--border);border-radius:12px;background:var(--grey);">
          <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:10px;">Quick Add User</div>
          <form method="POST" action="{{ route('admin.users.store') }}" style="display:grid;grid-template-columns:repeat(4, minmax(0, 1fr));gap:12px;align-items:end;">
            @csrf
            <div class="field-group">
              <label class="field-label">Full Name</label>
              <input class="field-input" name="usr_name" placeholder="e.g. Juan Dela Cruz" required>
            </div>
            <div class="field-group">
              <label class="field-label">Email</label>
              <input class="field-input" name="usr_email" type="email" placeholder="user@ctu.edu.ph" required>
            </div>
            <div class="field-group">
              <label class="field-label">Role</label>
              <select class="field-select" name="usr_role" required>
                <option value="">— Select role —</option>
                <option value="faculty">Faculty Member</option>
                <option value="department_chair">Department Chair</option>
                <option value="dean">Dean</option>
                <option value="system_admin">Technical Administrator</option>
              </select>
            </div>
            <div class="field-group">
              <label class="field-label">Password</label>
              <input class="field-input" name="password" type="password" placeholder="Temporary password" required minlength="8">
            </div>
            <div class="field-group" style="grid-column:span 4;display:flex;justify-content:flex-end;">
              <button class="topbar-btn btn-primary" type="submit">Create Account</button>
            </div>
          </form>
        </div>

        <div class="table-wrap">
          <table id="users-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
              @php
                $colors     = ['#2563eb','#16a34a','#d97706','#0891b2','#7c3aed'];
                $colorIndex = ((crc32($user->usr_name) % 5) + 5) % 5;
                $avatarColor = $colors[$colorIndex];
                $roleLabels  = [
                  'faculty'          => 'Faculty',
                  'department_chair' => 'Dept. Chair',
                  'dean'             => 'Dean',
                  'system_admin'     => 'System Admin',
                ];
              @endphp
              <tr class="user-row"
                  data-name="{{ $user->usr_name }}"
                  data-role="{{ $user->usr_role }}"
                  data-email="{{ $user->usr_email }}">

                <td>
                  <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:50%;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;">
                      {{ strtoupper(substr($user->usr_name, 0, 1)) }}
                    </div>
                    <span style="font-weight:600;color:var(--text);">{{ $user->usr_name }}</span>
                  </div>
                </td>

                <td>
                  <span class="badge badge-grey">
                    {{ $roleLabels[$user->usr_role] ?? ucfirst($user->usr_role) }}
                  </span>
                </td>

                <td style="color:var(--text2);font-size:13px;">{{ $user->usr_email }}</td>

                <td>
                  <span class="badge {{ $user->usr_is_active ? 'badge-green' : 'badge-amber' }}">
                    {{ $user->usr_is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                <td>
                  <div style="display:flex;gap:6px;">
                    <a class="topbar-btn btn-secondary"
                      href="{{ route('admin.users') }}"
                      style="padding:5px 12px;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;">
                      Edit
                    </a>
                    <form method="POST" action="{{ route('admin.users.destroy', ['id' => $user->usr_id]) }}" onsubmit="return confirm('Delete this user?')" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button class="topbar-btn"
                        type="submit"
                        style="padding:5px 12px;font-size:12px;background:var(--red-light);color:var(--red);">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:var(--text3);font-size:14px;">
                  No user accounts found. Click <strong>+ Add User</strong> to get started.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
          <div id="page-info" style="font-size:13px;color:var(--text3);"></div>
          <div style="display:flex;align-items:center;gap:6px;">
            <button class="topbar-btn btn-secondary" id="btn-prev" type="button" style="padding:6px 14px;font-size:13px;">← Prev</button>
            <div id="page-numbers" style="display:flex;gap:4px;"></div>
            <button class="topbar-btn btn-secondary" id="btn-next" type="button" style="padding:6px 14px;font-size:13px;">Next →</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: ADD USER  →  POST to store()
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-add-user">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add New User</div>
      <button class="modal-close" type="button" onclick="closeModal('modal-add-user')">✕</button>
    </div>
    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Full Name</label>
          <input class="field-input" name="usr_name" placeholder="e.g. Juan Dela Cruz" required>
        </div>
        <div class="field-group">
          <label class="field-label">Email</label>
          <input class="field-input" name="usr_email" type="email" placeholder="user@ctu.edu.ph" required>
        </div>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Role</label>
          <select class="field-select" name="usr_role" required>
            <option value="">— Select role —</option>
            <option value="faculty">Faculty Member</option>
            <option value="department_chair">Department Chair</option>
            <option value="dean">Dean</option>
            <option value="system_admin">Technical Administrator</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Employment Type</label>
          <select class="field-select" name="employment_type">
            <option value="full_time">Full-time</option>
            <option value="part_time">Part-time</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Password</label>
          <input class="field-input" name="password" type="password" placeholder="Temporary password" required minlength="8">
        </div>
        <div class="field-group">
          <label class="field-label">Department</label>
          <select class="field-select" name="department">
            <option value="BSIS">BSIS</option>
            <option value="BSIT">BSIT</option>
            <option value="BIT-CT">BIT-CT</option>
            <option value="CCICT">CCICT</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="topbar-btn btn-secondary" type="button" onclick="closeModal('modal-add-user')">Cancel</button>
        <button class="topbar-btn btn-primary" type="submit">Create Account</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: EDIT USER  →  PUT to update()
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-edit-user">
  <div class="modal" style="width:520px;">
    <div class="modal-header">
      <div class="modal-title">Edit User</div>
      <button class="modal-close" type="button" onclick="closeModal('modal-edit-user')">✕</button>
    </div>
    <div style="display:flex;align-items:center;gap:16px;padding:16px;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:12px;margin-bottom:20px;">
      <div id="edit-avatar" style="width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0;border:2px solid rgba(255,255,255,0.2);"></div>
      <div>
        <div id="edit-avatar-name" style="font-size:16px;font-weight:800;color:#fff;"></div>
        <div id="edit-avatar-role" style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:2px;"></div>
      </div>
    </div>
    <form id="form-edit-user" method="POST" action="">
      @csrf
      @method('PUT')
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Full Name</label>
          <input class="field-input" id="edit-name" name="usr_name" placeholder="Full Name" required>
        </div>
        <div class="field-group">
          <label class="field-label">Email</label>
          <input class="field-input" id="edit-email" name="usr_email" type="email" placeholder="email@ctu.edu.ph" required>
        </div>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Role</label>
          <select class="field-select" id="edit-role" name="usr_role">
            <option value="faculty">Faculty</option>
            <option value="department_chair">Dept. Chair</option>
            <option value="dean">Dean</option>
            <option value="system_admin">Technical Administrator</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Status</label>
          <select class="field-select" id="edit-status" name="usr_is_active">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Office Location</label>
          <input class="field-input" id="edit-office" name="office" placeholder="e.g. Room 205, ICT Building">
        </div>
        <div class="field-group">
          <label class="field-label">Contact Number</label>
          <input class="field-input" id="edit-contact" name="contact" placeholder="e.g. (032) 401-0000">
        </div>
      </div>
      <div class="field-group" style="margin-bottom:16px;">
        <label class="field-label">About / Bio</label>
        <textarea class="field-input" id="edit-about" name="about" rows="3" style="resize:vertical;"></textarea>
      </div>
      <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:4px;">
        ⚠️ Changes will be reflected immediately across the system.
      </div>
      <div class="modal-footer">
        <button class="topbar-btn btn-secondary" type="button" onclick="closeModal('modal-edit-user')">Cancel</button>
        <button class="topbar-btn" type="button" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="confirmDeleteFromEdit()">Delete</button>
        <button class="topbar-btn btn-primary" type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     HIDDEN DELETE FORM  →  DELETE to destroy()
     ══════════════════════════════════════════════════════════════ --}}
<form id="form-delete-user" method="POST" action="" style="display:none;">
  @csrf
  @method('DELETE')
</form>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>
</body>
</html>