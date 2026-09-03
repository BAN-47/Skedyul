<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SKEDYUL — User Accounts</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

  <div class="app-shell">
    @include('partials.admin_sidebar')

    {{-- MAIN --}}
    <div class="app-main">

      {{-- TOPBAR --}}
      @include('partials.admin_header', ['title' => 'User Accounts'])

      {{-- FLASH MESSAGES --}}
      @if(session("success"))
      <script>
        document.addEventListener("DOMContentLoaded", () => showToast("✅ {{ session("
          success ") }}"));
      </script>
      @endif
      @if(session("error"))
      <script>
        document.addEventListener("DOMContentLoaded", () => showToast("❌ {{ session("
          error ") }}"));
      </script>
      @endif

      <div class="page-content" id="page-user-accounts">

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-4 gap-3 mb-4">
          <div class="stat-card">
            <div class="stat-card-bar bg-blue-600"></div>
            <div class="stat-icon">👥</div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ $users->count() }}</div>
            <div class="stat-sub">All roles combined</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-bar bg-green-600"></div>
            <div class="stat-icon">👨‍🏫</div>
            <div class="stat-label">Faculty</div>
            <div class="stat-value">{{ $users->where('usr_role','faculty')->count() }}</div>
            <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-bar bg-amber-500"></div>
            <div class="stat-icon">📋</div>
            <div class="stat-label">Dept. Chairs</div>
            <div class="stat-value">{{ $users->where('usr_role','department_chair')->count() }}</div>
            <div class="stat-sub">Active this semester</div>
          </div>
          <div class="stat-card">
            <div class="stat-card-bar bg-cyan-600"></div>
            <div class="stat-icon">✅</div>
            <div class="stat-label">Active Accounts</div>
            <div class="stat-value">{{ $users->where('usr_is_active', true)->count() }}</div>
            <div class="stat-sub">{{ $users->where('usr_is_active', false)->count() }} inactive</div>
          </div>
        </div>

        {{-- USER TABLE CARD --}}
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">All User Accounts</div>
              <div class="card-sub">Click a name to view full profile</div>
            </div>
            <div class="flex gap-2 items-center">
              <input id="user-search" placeholder="Search users..." oninput="filterUsers(this.value)"
                class="field-input w-[200px]">
              <button type="button" onclick="openModal('modal-add-user')" class="btn btn-primary">
                + Add User
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="data-table" id="users-table">
              <thead>
                <tr>
                  @foreach(['Name','Role','Email','Status','Action'] as $h)
                  <th>{{ $h }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @forelse($users as $user)
                @php
                $colors = ['#2563eb','#16a34a','#d97706','#0891b2','#7c3aed'];
                $colorIndex = ((crc32($user->usr_name) % 5) + 5) % 5;
                $avatarColor = $colors[$colorIndex];
                $roleLabels = [
                'faculty' => 'Faculty',
                'department_chair' => 'Dept. Chair',
                'dean' => 'Dean',
                'system_admin' => 'System Admin',
                ];
                @endphp
                <tr class="user-row"
                  data-user-id="{{ $user->usr_id }}"
                  data-name="{{ $user->usr_name }}"
                  data-role="{{ $user->usr_role }}"
                  data-email="{{ $user->usr_email }}"
                  data-active="{{ $user->usr_is_active ? '1' : '0' }}"
                  data-bio="{{ $user->usr_bio }}"
                  data-room-location="{{ $user->room_location }}">

                  <td>
                    <div class="flex items-center gap-2.5">
                      <div class="w-[34px] h-[34px] rounded-full flex items-center justify-center text-[13px] font-extrabold text-white flex-shrink-0"
                        style="background:{{ $avatarColor }};">
                        {{ strtoupper(substr($user->usr_name, 0, 1)) }}
                      </div>
                      <button
                        type="button"
                        onclick="openProfileModal('{{ $user->usr_id }}')"
                        class="font-semibold text-[13px] text-slate-900 hover:text-blue-600 text-left">
                        {{ $user->usr_name }}
                      </button>
                    </div>
                  </td>

                  <td>
                    <span class="badge badge-grey">
                      {{ $roleLabels[$user->usr_role] ?? ucfirst($user->usr_role) }}
                    </span>
                  </td>

                  <td class="text-slate-500">{{ $user->usr_email }}</td>

                  <td>
                    <span class="badge {{ $user->usr_is_active ? 'badge-green' : 'badge-amber' }}">
                      {{ $user->usr_is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>

                  <td>
                    <div class="flex gap-1.5">
                      <button
                        type="button"
                        class="btn btn-secondary !px-3 !py-1.5 !text-[12px]"
                        onclick="openEditModal(
        '{{ $user->usr_id }}',
        @js($user->usr_name),
        @js($user->usr_email),
        @js($user->usr_role),
        {{ $user->usr_is_active ? 'true' : 'false' }}
    )">
                        Edit
                      </button>
                      <form method="POST" action="{{ route('admin.users.destroy', ['id' => $user->usr_id]) }}" onsubmit="return confirm('Delete this user?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger !px-3 !py-1.5 !text-[12px]">
                          Delete
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-10 text-[14px] text-slate-400">
                    No user accounts found. Click <strong>+ Add User</strong> to get started.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Pagination --}}
          <div class="flex items-center justify-between mt-4 pt-3.5 border-t border-slate-200">
            <div id="page-info" class="text-[13px] text-slate-400"></div>
            <div class="flex items-center gap-1.5">
              <button id="btn-prev" type="button" onclick="changePage(-1)" class="btn btn-secondary">← Prev</button>
              <div id="page-numbers" class="flex gap-1"></div>
              <button id="btn-next" type="button" onclick="changePage(1)" class="btn btn-secondary">Next →</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL: ADD USER --}}
  <div class="modal-overlay" id="modal-add-user">
    <div class="modal-box w-[640px]">
      <div class="modal-header">
        <div class="modal-title">Add New User</div>
        <button type="button" onclick="closeModal('modal-add-user')" class="modal-close">✕</button>
      </div>
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Full Name</label>
            <input name="usr_name" placeholder="e.g. Juan Dela Cruz" required class="field-input">
          </div>
          <div>
            <label class="field-label">Email</label>
            <input name="usr_email" type="email" placeholder="user@ctu.edu.ph" required class="field-input">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Role</label>
            <select name="usr_role" required class="field-input">
              <option value="">— Select role —</option>
              <option value="faculty">Faculty Member</option>
              <option value="department_chair">Department Chair</option>
              <option value="dean">Dean</option>
              <option value="system_admin">Technical Administrator</option>
            </select>
          </div>
          <div>
            <label class="field-label">Employment Type</label>
            <select name="employment_type" class="field-input">
              <option value="full_time">Full-time</option>
              <option value="part_time">Part-time</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Password</label>
            <input name="password" type="password" placeholder="Temporary password" required minlength="8" class="field-input">
          </div>
          <div>
            <label class="field-label">Department</label>
            <select name="department" class="field-input">
              <option value="BSIS">BSIS</option>
              <option value="BSIT">BSIT</option>
              <option value="BIT-CT">BIT-CT</option>
              <option value="CCICT">CCICT</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" onclick="closeModal('modal-add-user')" class="btn btn-secondary">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Account</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL: EDIT USER --}}
  <div class="modal-overlay" id="modal-edit-user">
    <div class="modal-box w-[370px] max-w-[calc(100vw-32px)]">
      <div class="modal-header">
        <div class="modal-title">Edit User</div>
        <button type="button" onclick="closeModal('modal-edit-user')" class="modal-close">✕</button>
      </div>
      <div class="flex items-center gap-3 p-3.5 rounded-xl mb-4" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);">
        <div id="edit-avatar" class="w-10 h-10 rounded-full flex items-center justify-center text-base font-extrabold text-white shrink-0" style="border:2px solid rgba(255,255,255,.2);"></div>
        <div>
          <div id="edit-avatar-name" class="text-[13px] font-extrabold text-white"></div>
          <div id="edit-avatar-role" class="text-[11px] text-white/50 mt-0.5"></div>
        </div>
      </div>
      <form id="form-edit-user" method="POST" action="">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Full Name</label>
            <input id="edit-name" name="usr_name" placeholder="Full Name" required class="field-input">
          </div>
          <div>
            <label class="field-label">Email</label>
            <input id="edit-email" name="usr_email" type="email" placeholder="email@ctu.edu.ph" required class="field-input">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Role</label>
            <select id="edit-role" name="usr_role" class="field-input">
              <option value="faculty">Faculty</option>
              <option value="department_chair">Dept. Chair</option>
              <option value="dean">Dean</option>
              <option value="system_admin">Technical Administrator</option>
            </select>
          </div>
          <div>
            <label class="field-label">Department</label>
            <select id="edit-department" class="field-input">
              <option value="CCICT">CCICT</option>
              <option value="BSIS">BSIS</option>
              <option value="BSIT">BSIT</option>
              <option value="BIT-CT">BIT-CT</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Employment Type</label>
            <select id="edit-employment" class="field-input">
              <option value="full_time">Full-time</option>
              <option value="part_time">Part-time</option>
            </select>
          </div>
          <div>
            <label class="field-label">Status</label>
            <select id="edit-status" name="usr_is_active" class="field-input">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Office Location</label>
            <input id="edit-office" class="field-input" readonly>
          </div>
          <div>
            <label class="field-label">Contact Number</label>
            <input id="edit-contact" class="field-input" value="(032) 401-7777">
          </div>
        </div>
        <div class="mb-3">
          <label class="field-label">About / Bio</label>
          <textarea id="edit-about" name="usr_bio" class="field-input min-h-12 resize-y" rows="2"></textarea>
        </div>
        <div class="bg-amber-100 border border-amber-300 rounded-lg px-3.5 py-2.5 text-[12px] text-amber-800 mb-1">
          ⚠️ Changes will be reflected immediately across the system.
        </div>
        <div class="modal-footer">
          <button type="button" onclick="closeModal('modal-edit-user')" class="btn btn-secondary">Cancel</button>
          <button type="button" onclick="confirmDeleteFromEdit()" class="btn btn-danger">Delete</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL: USER PROFILE --}}
  <div class="modal-overlay" id="modal-user-profile">
    <div class="modal-box w-[370px] max-w-[calc(100vw-32px)]">
      <div class="modal-header">
        <div class="modal-title">User Profile</div>
        <button type="button" onclick="closeModal('modal-user-profile')" class="modal-close">✕</button>
      </div>

      <div class="profile-modal-banner">
        <div id="profile-avatar" class="profile-modal-avatar"></div>
        <div class="min-w-0">
          <div id="profile-name" class="profile-modal-name"></div>
          <div id="profile-role" class="profile-modal-role"></div>
          <span id="profile-status" class="profile-modal-status"></span>
        </div>
      </div>

      <div class="profile-modal-grid">
        <div class="profile-modal-field">
          <div class="profile-modal-label">Email</div>
          <div id="profile-email" class="profile-modal-value"></div>
        </div>
        <div class="profile-modal-field">
          <div class="profile-modal-label">Employment</div>
          <div id="profile-employment" class="profile-modal-value">Full-time</div>
        </div>
        <div class="profile-modal-field">
          <div class="profile-modal-label">Personal Info</div>
          <div id="profile-personal" class="profile-modal-value">Not provided</div>
        </div>
        <div class="profile-modal-field">
          <div class="profile-modal-label">Contact</div>
          <div id="profile-contact" class="profile-modal-value">Not provided</div>
        </div>
        <div class="profile-modal-field profile-modal-field-wide">
          <div class="profile-modal-label">About</div>
          <div id="profile-about" class="profile-modal-value">No biography provided.</div>
        </div>
        <div class="profile-modal-field profile-modal-field-wide">
          <div class="profile-modal-label">Office Location</div>
          <div id="profile-office" class="profile-modal-value">Not provided</div>
        </div>
      </div>

      <div class="profile-modal-actions">
        <button type="button" onclick="closeModal('modal-user-profile')" class="btn btn-secondary">Close</button>
        <button type="button" onclick="editProfileUser()" class="btn btn-primary">Edit Profile</button>
      </div>
    </div>
  </div>

  {{-- TOAST --}}
  <div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

  <script>
    const AVATAR_COLORS = ['#2563eb', '#16a34a', '#d97706', '#0891b2', '#7c3aed'];
    const ROLE_LABELS = {
      faculty: 'Faculty',
      department_chair: 'Dept. Chair',
      dean: 'Dean',
      system_admin: 'System Admin'
    };

    const EDIT_URL_TEMPLATE = "{{ route('admin.users.edit', ['id' => '__ID__']) }}";
    const UPDATE_URL_TEMPLATE = "{{ route('admin.users.update', ['id' => '__ID__']) }}";

    function openModal(id) {
      document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('active');
    }

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('active');
      });
    });

    function simpleHash(str) {
      let hash = 0;
      for (let i = 0; i < str.length; i++) {
        hash = (hash * 31 + str.charCodeAt(i)) >>> 0;
      }
      return hash;
    }

    function openEditModal(userId, name, email, role, isActive) {

      document.getElementById('edit-name').value = name || '';
      document.getElementById('edit-email').value = email || '';
      document.getElementById('edit-role').value = role || 'faculty';
      document.getElementById('edit-status').value = isActive ? '1' : '0';
      const editRow = document.querySelector(`tr[data-user-id="${CSS.escape(String(userId))}"]`);
      document.getElementById('edit-office').value = editRow?.dataset.roomLocation || 'No room assigned';
      document.getElementById('edit-about').value = editRow?.dataset.bio || '';

      const colorIndex =
        simpleHash(name || '') % AVATAR_COLORS.length;

      const avatarEl = document.getElementById('edit-avatar');

      avatarEl.style.background =
        AVATAR_COLORS[colorIndex];

      avatarEl.textContent =
        (name || '?').charAt(0).toUpperCase();

      document.getElementById('edit-avatar-name').textContent =
        name || '';

      document.getElementById('edit-avatar-role').textContent =
        ROLE_LABELS[role] || role || '';

      const form =
        document.getElementById('form-edit-user');

      form.action =
        "{{ url('admin/users') }}/" + userId;

      form.dataset.userId = userId;

      openModal('modal-edit-user');
    }

    let profileUserId = null;

    function getInitials(name) {
      return (name || '?')
        .trim()
        .split(/\s+/)
        .map(part => part.charAt(0))
        .join('')
        .slice(0, 2)
        .toUpperCase();
    }

    function openProfileModal(userId) {
      const row = document.querySelector(`tr[data-user-id="${CSS.escape(String(userId))}"]`);
      if (!row) return;

      profileUserId = userId;
      const name = row.dataset.name || 'Unknown User';
      const role = row.dataset.role || 'faculty';
      const email = row.dataset.email || 'Not provided';
      const isActive = row.dataset.active === '1';
      const colorIndex = simpleHash(name) % AVATAR_COLORS.length;

      document.getElementById('profile-avatar').textContent = getInitials(name);
      document.getElementById('profile-avatar').style.background = AVATAR_COLORS[colorIndex];
      document.getElementById('profile-name').textContent = name;
      document.getElementById('profile-role').textContent = ROLE_LABELS[role] || role;
      document.getElementById('profile-email').textContent = email;
      document.getElementById('profile-status').textContent = isActive ? 'Active' : 'Inactive';
      document.getElementById('profile-status').className = `profile-modal-status ${isActive ? 'is-active' : 'is-inactive'}`;
      document.getElementById('profile-employment').textContent = 'Full-time';
      document.getElementById('profile-personal').textContent = role === 'dean' ? 'Female · Filipino' : 'Not provided';
      document.getElementById('profile-contact').textContent = role === 'dean' ? '(032) 401-7777' : 'Not provided';
      document.getElementById('profile-about').textContent = row.dataset.bio || 'No biography provided.';
      document.getElementById('profile-office').textContent = row.dataset.roomLocation || 'No room assigned';

      openModal('modal-user-profile');
    }

    function editProfileUser() {
      const row = document.querySelector(`tr[data-user-id="${CSS.escape(String(profileUserId))}"]`);
      if (!row) return;
      closeModal('modal-user-profile');
      openEditModal(profileUserId, row.dataset.name, row.dataset.email, row.dataset.role, row.dataset.active === '1');
    }

    function confirmDeleteFromEdit() {
      const form = document.getElementById('form-edit-user');
      const userId = form.dataset.userId;
      if (!userId) return;
      if (!confirm('Delete this user? This cannot be undone.')) return;

      const deleteForm = document.createElement('form');
      deleteForm.method = 'POST';
      deleteForm.action = "{{ url('admin/users') }}/" + userId;
      deleteForm.innerHTML = `
    @csrf
    @method('DELETE')
  `;
      document.body.appendChild(deleteForm);
      deleteForm.submit();
    }

    function showToast(msg) {
      const toast = document.getElementById('toast');
      const toastMsg = document.getElementById('toast-msg');
      if (!toast || !toastMsg) return;
      toastMsg.textContent = msg.replace(/^✅\s?|^❌\s?/, '');
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3000);
    }

    function toggleNotifDropdown() {
      const dd = document.getElementById('notif-dropdown');
      dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
    }

    function markAllRead() {
      document.getElementById('notif-count').style.display = 'none';
    }

    function filterUsers(query) {
      const q = query.trim().toLowerCase();
      document.querySelectorAll('#users-table tbody tr.user-row').forEach(row => {
        const name = (row.dataset.name || '').toLowerCase();
        const email = (row.dataset.email || '').toLowerCase();
        const role = (row.dataset.role || '').toLowerCase();
        const match = name.includes(q) || email.includes(q) || role.includes(q);
        row.style.display = match ? '' : 'none';
      });
    }

    function changePage(direction) {
      // Placeholder — wire up if/when server-side pagination is added
    }
  </script>

</body>

</html>