<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Dean Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/faculty/faculty_settings.css') }}">
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-logo">
      <div class="sidebar-logo-text">SKED<span>YUL</span></div>
    </div>
    <div class="sidebar-user">
      <div class="sidebar-avatar" id="sb-avatar">A</div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name" id="sb-name">Tech Admin</div>
        <div class="sidebar-user-role" id="sb-role">Technical Administrator</div>
      </div>
    </div>

    <div class="sidebar-nav" id="sidebar-nav"></div>

    <div class="sidebar-bottom">
      <button class="btn-logout" onclick="logout()">⬅ Sign Out</button>
    </div>
  </div>

  <!-- Main --> 
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Settings</div>
    </div>

    <!-- FACULTY SETTINGS PAGE -->
    <div id="page-faculty-settings" class="page active">
      <div style="display:grid;grid-template-columns:220px 1fr;gap:24px;align-items:start;">
        <div class="card" style="padding:12px 0;">
          <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1px;padding:8px 20px 4px;">Settings</div>
          <div class="settings-nav-item active" onclick="showFacSettingsSection('profile',this)">Personal Info</div>
          <div class="settings-nav-item" onclick="showFacSettingsSection('security',this)">Security</div>
          <div class="settings-nav-item" onclick="showFacSettingsSection('notifications',this)">Notifications</div>
        </div>
        <div id="fac-settings-content">

          <!-- PERSONAL INFO -->
          <div id="fac-settings-profile">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Profile Picture</div><div class="card-sub">Upload a new profile photo</div></div></div>
              <div style="display:flex;align-items:center;gap:24px;padding:8px 0;">
                <div style="position:relative;flex-shrink:0;">
                  <div id="fac-pic-preview" style="width:96px;height:96px;border-radius:50%;background:var(--grey2);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#16a34a;border:3px solid var(--border);overflow:hidden;">JB</div>
                  <div onclick="document.getElementById('fac-pic-upload').click()" style="position:absolute;bottom:0;right:0;width:30px;height:30px;background:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid white;font-size:13px;">+</div>
                  <input type="file" id="fac-pic-upload" accept="image/*" style="display:none;" onchange="facPreviewPic(this)">
                </div>
                <div>
                  <div style="font-size:15px;font-weight:700;color:var(--text);">Jerome Bautista</div>
                  <div style="font-size:12px;color:var(--text3);margin-top:2px;">Faculty · BSIS Department</div>
                  <div style="display:flex;gap:8px;margin-top:12px;">
                    <button class="topbar-btn btn-primary" style="font-size:12px;padding:7px 14px;" onclick="document.getElementById('fac-pic-upload').click()">Upload Photo</button>
                    <button class="topbar-btn btn-secondary" style="font-size:12px;padding:7px 14px;" onclick="facResetPic()">Remove</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Personal Information</div><div class="card-sub">Update your personal details</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">First Name</label><input class="field-input" value="Jerome"></div>
                <div class="field-group"><label class="field-label">Last Name</label><input class="field-input" value="Bautista"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Middle Name</label><input class="field-input" placeholder="Optional"></div>
                <div class="field-group"><label class="field-label">Employee ID</label><input class="field-input" value="CTU-2022-045"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Gender</label><select class="field-select"><option selected>Male</option><option>Female</option><option>Prefer not to say</option></select></div>
                <div class="field-group"><label class="field-label">Civil Status</label><select class="field-select"><option selected>Single</option><option>Married</option><option>Widowed</option></select></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Date of Birth</label><input class="field-input" type="date" value="1990-05-15"></div>
                <div class="field-group"><label class="field-label">Nationality</label><input class="field-input" value="Filipino"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Personal info saved successfully!')">Save Changes</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Contact & Office</div><div class="card-sub">How others can reach you</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Email Address</label><input class="field-input" type="email" value="j.bautista@ctu.edu.ph"></div>
                <div class="field-group"><label class="field-label">Phone Number</label><input class="field-input" value="(032) 401-2222"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Office Location</label><input class="field-input" value="Room 205, ICT Building"></div>
                <div class="field-group"><label class="field-label">Department</label><input class="field-input" value="BSIS" readonly style="background:var(--grey2);cursor:not-allowed;"></div>
              </div>
              <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Bio / About</label><textarea class="field-input" rows="3" style="resize:vertical;">Faculty member handling Programming and Systems subjects. Bachelor of Science in Computer Science, CTU.</textarea></div>
              <div style="display:flex;justify-content:flex-end;">
                <button class="topbar-btn btn-primary" onclick="showToast('Contact details saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- SECURITY -->
          <div id="fac-settings-security" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Change Password</div><div class="card-sub">Update your account password</div></div></div>
              <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Current Password</label><input class="field-input" type="password" placeholder="••••••••"></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">New Password</label><input class="field-input" type="password" placeholder="Min. 8 characters"></div>
                <div class="field-group"><label class="field-label">Confirm New Password</label><input class="field-input" type="password" placeholder="Re-enter new password"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Password updated successfully!')">Update Password</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Session Settings</div><div class="card-sub">Manage your login preferences</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Session Timeout</label><select class="field-select"><option>15 minutes</option><option selected>30 minutes</option><option>1 hour</option><option>Never</option></select></div>
                <div class="field-group"><label class="field-label">Max Login Attempts</label><select class="field-select"><option>3</option><option selected>5</option><option>10</option></select></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Security settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- NOTIFICATIONS SETTINGS -->
          <div id="fac-settings-notifications" style="display:none;">
            <div class="card">
              <div class="card-header"><div><div class="card-title">Notification Preferences</div><div class="card-sub">Choose what alerts you receive</div></div></div>
              <div style="display:flex;flex-direction:column;gap:12px;margin-top:4px;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Schedule Updates</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">When your schedule is modified</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">New Assignments</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">When a new subject is assigned to you</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Reminders</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">Deadlines and important announcements</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">System Announcements</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">General system updates and messages</div></div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:16px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Notification preferences saved!')">Save Preferences</button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- MODALS -->
<div class="modal-overlay" id="modal-assign">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Assign Subject to Schedule</div>
      <button class="modal-close" onclick="closeModal('modal-assign')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Faculty Member</label><select class="field-select"><option>Jerome Bautista</option><option>Ana Reyes</option><option>Carlo Mendoza</option><option>Maria Santos</option></select></div>
      <div class="field-group"><label class="field-label">Subject</label><select class="field-select"><option>CC 313 — Web Systems</option><option>CC 401 — Capstone 1</option><option>IT 302 — Networking</option><option>GE 101 — Ethics</option></select></div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Section</label><select class="field-select"><option>BSIS 3-A</option><option>BSIS 3-B</option><option>BSIS 2-A</option></select></div>
      <div class="field-group"><label class="field-label">Room</label><select class="field-select"><option>Room 301</option><option>Room 302</option><option>Lab 1</option><option>Lab 2</option></select></div>
    </div>
    <div class="form-row three">
      <div class="field-group"><label class="field-label">Day</label><select class="field-select"><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option></select></div>
      <div class="field-group"><label class="field-label">Start Time</label><select class="field-select"><option>7:00 AM</option><option>8:30 AM</option><option>10:00 AM</option><option>11:30 AM</option><option>1:00 PM</option><option>2:30 PM</option><option>4:00 PM</option></select></div>
      <div class="field-group"><label class="field-label">End Time</label><select class="field-select"><option>8:30 AM</option><option>10:00 AM</option><option>11:30 AM</option><option>1:00 PM</option><option>2:30 PM</option><option>4:00 PM</option><option>5:30 PM</option></select></div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;">
      ⚡ System will automatically check for conflicts before saving.
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-assign')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-assign');showToast('Subject assigned! No conflicts detected ✓')">Check & Assign</button>
    </div>
  </div>
</div>

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

<!-- ADD SUBJECT MODAL -->
<div class="modal-overlay" id="modal-add-subject">
  <div class="modal" style="width:500px;">
    <div class="modal-header">
      <div class="modal-title">Add New Subject</div>
      <button class="modal-close" onclick="closeModal('modal-add-subject')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Subject Code</label><input class="field-input" id="add-subj-code" placeholder="e.g. CC 314"></div>
      <div class="field-group"><label class="field-label">Department</label>
        <select class="field-select" id="add-subj-dept">
          <option>BSIS</option><option>BSIT</option><option>BIT-CT</option><option>GE</option>
        </select>
      </div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Subject Name</label><input class="field-input" id="add-subj-name" placeholder="e.g. Web Systems and Technologies"></div>
    <div class="form-row three">
      <div class="field-group"><label class="field-label">Units</label><input class="field-input" id="add-subj-units" type="number" min="1" max="6" placeholder="3"></div>
      <div class="field-group"><label class="field-label">Lecture Hrs</label><input class="field-input" id="add-subj-lec" type="number" min="0" max="6" placeholder="2"></div>
      <div class="field-group"><label class="field-label">Lab Hrs</label><input class="field-input" id="add-subj-lab" type="number" min="0" max="6" placeholder="3"></div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Description (optional)</label><textarea class="field-input" id="add-subj-desc" rows="2" placeholder="Brief subject description..." style="resize:vertical;"></textarea></div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-add-subject')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="saveAddSubject()">Add Subject</button>
    </div>
  </div>
</div>

<!-- EDIT SUBJECT MODAL -->
<div class="modal-overlay" id="modal-edit-subject">
  <div class="modal" style="width:500px;">
    <div class="modal-header">
      <div class="modal-title">Edit Subject</div>
      <button class="modal-close" onclick="closeModal('modal-edit-subject')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Subject Code</label><input class="field-input" id="edit-subj-code" placeholder="e.g. CC 313"></div>
      <div class="field-group"><label class="field-label">Department</label>
        <select class="field-select" id="edit-subj-dept">
          <option>BSIS</option><option>BSIT</option><option>BIT-CT</option><option>GE</option>
        </select>
      </div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Subject Name</label><input class="field-input" id="edit-subj-name" placeholder="Subject name"></div>
    <div class="form-row three">
      <div class="field-group"><label class="field-label">Units</label><input class="field-input" id="edit-subj-units" type="number" min="1" max="6"></div>
      <div class="field-group"><label class="field-label">Lecture Hrs</label><input class="field-input" id="edit-subj-lec" type="number" min="0" max="6"></div>
      <div class="field-group"><label class="field-label">Lab Hrs</label><input class="field-input" id="edit-subj-lab" type="number" min="0" max="6"></div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:4px;">
      ⚠️ Editing a subject may affect existing schedule assignments.
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-subject')">Cancel</button>
      <button class="topbar-btn btn-primary" id="edit-subj-save-btn">Save Changes</button>
    </div>
  </div>
</div>

<!-- ADD ROOM MODAL -->
<div class="modal-overlay" id="modal-add-room">
  <div class="modal" style="width:500px;">
    <div class="modal-header">
      <div class="modal-title">Add New Room</div>
      <button class="modal-close" onclick="closeModal('modal-add-room')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Room Name / Number</label><input class="field-input" id="add-room-name" placeholder="e.g. Room 303"></div>
      <div class="field-group"><label class="field-label">Room Type</label>
        <select class="field-select" id="add-room-type">
          <option>Lecture</option><option>Laboratory</option><option>AVR / Function Hall</option><option>Conference Room</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Capacity</label><input class="field-input" id="add-room-capacity" type="number" min="1" placeholder="e.g. 40"></div>
      <div class="field-group"><label class="field-label">Building / Location</label><input class="field-input" id="add-room-location" placeholder="e.g. 2nd Floor, ICT Building"></div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Facilities / Equipment</label><textarea class="field-input" id="add-room-facilities" rows="2" placeholder="e.g. Air-conditioned, projector, whiteboard..." style="resize:vertical;"></textarea></div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-add-room')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="saveAddRoom()">Add Room</button>
    </div>
  </div>
</div>

<!-- VIEW ROOM MODAL -->
<div class="modal-overlay" id="modal-view-room">
  <div class="modal" style="width:480px;">
    <div class="modal-header">
      <div class="modal-title">Room Details</div>
      <button class="modal-close" onclick="closeModal('modal-view-room')">✕</button>
    </div>
    <!-- Room Header -->
    <div style="display:flex;align-items:center;gap:16px;padding:18px;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:14px;margin-bottom:20px;">
      <div style="width:56px;height:56px;border-radius:14px;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0;">🚪</div>
      <div>
        <div id="vr-name" style="font-size:20px;font-weight:800;color:#fff;"></div>
        <div id="vr-type" style="font-size:13px;color:rgba(255,255,255,0.6);margin-top:2px;"></div>
        <div id="vr-status-badge" style="margin-top:6px;"></div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Capacity</div>
        <div id="vr-capacity" style="font-size:22px;font-weight:800;color:var(--text);"></div>
        <div style="font-size:11px;color:var(--text3);">students</div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Location</div>
        <div id="vr-location" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      </div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:12px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Current Assignment</div>
      <div id="vr-assignment" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      <div id="vr-faculty" style="font-size:12px;color:var(--text3);margin-top:3px;"></div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:20px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Facilities</div>
      <div id="vr-facilities" style="font-size:13px;color:var(--text2);line-height:1.6;"></div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-view-room')">Close</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="deleteCurrentRoom('modal-view-room')">Delete</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-view-room');showToast('Room details saved!')">Edit Room</button>
    </div>
  </div>
</div>

<!-- EDIT ROOM MODAL -->
<div class="modal-overlay" id="modal-edit-room">
  <div class="modal" style="width:500px;">
    <div class="modal-header">
      <div class="modal-title">Edit Room</div>
      <button class="modal-close" onclick="closeModal('modal-edit-room')">✕</button>
    </div>
    <div style="display:flex;align-items:center;gap:16px;padding:16px;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:12px;margin-bottom:20px;">
      <div style="width:50px;height:50px;border-radius:12px;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🚪</div>
      <div id="edit-room-header" style="font-size:17px;font-weight:800;color:#fff;"></div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Room Name / Number</label><input class="field-input" id="edit-room-name" placeholder="e.g. Room 301"></div>
      <div class="field-group"><label class="field-label">Room Type</label>
        <select class="field-select" id="edit-room-type">
          <option>Lecture</option><option>Laboratory</option><option>AVR / Function Hall</option><option>Conference Room</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Capacity</label><input class="field-input" id="edit-room-capacity" type="number" min="1"></div>
      <div class="field-group"><label class="field-label">Status</label>
        <select class="field-select" id="edit-room-status">
          <option>Available</option><option>In Use</option><option>Under Maintenance</option>
        </select>
      </div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Building / Location</label><input class="field-input" id="edit-room-location" placeholder="e.g. 2nd Floor, ICT Building"></div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Facilities / Equipment</label><textarea class="field-input" id="edit-room-facilities" rows="2" style="resize:vertical;" placeholder="e.g. Air-conditioned, projector..."></textarea></div>
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:4px;">
      ⚠️ Changes will be reflected immediately in the room directory.
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-room')">Cancel</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="deleteCurrentRoom('modal-edit-room')">Delete</button>
      <button class="topbar-btn btn-primary" id="edit-room-save-btn">Save Changes</button>
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


<!-- EDIT SUBJECT MODAL -->
<div class="modal-overlay" id="modal-edit-subject">
  <div class="modal" style="width:500px;">
    <div class="modal-header">
      <div class="modal-title">Edit Subject</div>
      <button class="modal-close" onclick="closeModal('modal-edit-subject')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Subject Code</label><input class="field-input" id="es-code"></div>
      <div class="field-group"><label class="field-label">Department</label>
        <select class="field-select" id="es-dept"><option>BSIS</option><option>BSIT</option><option>BIT-CT</option><option>GE</option><option>CCICT</option></select>
      </div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Subject Name</label><input class="field-input" id="es-name"></div>
    <div class="form-row three">
      <div class="field-group"><label class="field-label">Units</label><input class="field-input" id="es-units" type="number" min="1" max="6"></div>
      <div class="field-group"><label class="field-label">Lecture Hours</label><input class="field-input" id="es-lec" type="number" min="0" max="6"></div>
      <div class="field-group"><label class="field-label">Lab Hours</label><input class="field-input" id="es-lab" type="number" min="0" max="6"></div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-subject')">Cancel</button>
      <button class="topbar-btn btn-primary" id="es-save-btn">Save Changes</button>
    </div>
  </div>
</div>

<!-- ADD ROOM MODAL -->
<div class="modal-overlay" id="modal-add-room">
  <div class="modal" style="width:480px;">
    <div class="modal-header">
      <div class="modal-title">Add New Room</div>
      <button class="modal-close" onclick="closeModal('modal-add-room')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Room Name / Number</label><input class="field-input" id="ar-name" placeholder="e.g. Room 203"></div>
      <div class="field-group"><label class="field-label">Type</label>
        <select class="field-select" id="ar-type"><option>Lecture</option><option>Laboratory</option><option>AVR</option><option>Function Hall</option></select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Capacity</label><input class="field-input" id="ar-cap" type="number" min="1" placeholder="40"></div>
      <div class="field-group"><label class="field-label">Location / Floor</label><input class="field-input" id="ar-loc" placeholder="e.g. 2nd Floor, ICT Building"></div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Facilities / Notes</label><textarea class="field-input" id="ar-notes" rows="3" placeholder="e.g. Air-conditioned, projector, whiteboard..." style="resize:vertical;"></textarea></div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-add-room')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-add-room');showToast('Room added successfully!')">Add Room</button>
    </div>
  </div>
</div>


<!-- FACULTY SUBJECT DETAIL MODAL -->
<div class="modal-overlay" id="modal-web-subject-detail">
  <div class="modal" style="width:480px;">
    <div class="modal-header">
      <div class="modal-title">Subject Details</div>
      <button class="modal-close" onclick="closeModal('modal-web-subject-detail')">✕</button>
    </div>
    <div id="wsd-header" style="padding:16px 18px;border-radius:12px;margin-bottom:18px;">
      <div id="wsd-code" style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;"></div>
      <div id="wsd-name" style="font-size:20px;font-weight:800;color:#fff;"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Units</div>
        <div id="wsd-units" style="font-size:26px;font-weight:800;color:var(--text);"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Hours</div>
        <div id="wsd-hours" style="font-size:13px;font-weight:600;color:var(--text);margin-top:4px;"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Room</div>
        <div id="wsd-room" style="font-size:16px;font-weight:700;color:var(--text);margin-top:2px;"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Section</div>
        <div id="wsd-section" style="font-size:16px;font-weight:700;color:var(--text);margin-top:2px;"></div>
      </div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:12px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Schedule</div>
      <div id="wsd-schedule" style="font-size:13px;font-weight:600;color:var(--text);"></div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:18px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Faculty</div>
      <div style="font-size:13px;font-weight:600;color:var(--text);">Jerome Bautista</div>
      <div style="font-size:11px;color:var(--text3);margin-top:2px;">Faculty · BSIS Department</div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-web-subject-detail')">Close</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>

// ── STATE ──────────────────────────────────────────────────────────────────
let currentRole = 'admin';

const ROLES = {
  admin: {
    name: 'Tech Admin', label: 'Technical Administrator', avatar: 'TA', color: '#2563eb',
    nav: [
      { section:'Main', items:[
        { icon:'🏠', label:'Dashboard', page:'admin-dashboard' },
        { icon:'👥', label:'User Accounts', page:'user-accounts' },
        { icon:'📚', label:'Subjects', page:'subjects' },
        { icon:'🏫', label:'Rooms', page:'rooms' },
      ]},
      { section:'System', items:[
        { icon:'📊', label:'Reports', page:'reports' },
        { icon:'⚙️', label:'Settings', page:'settings' },
      ]},
    ]
  },
  dean: {
    name: 'Ma. Emie Villaceran', label: 'Dean, CCICT', avatar: 'D', color: '#0891b2',
    nav: [
      { section:'Main', items:[
        { icon:'🏠', label:'Dashboard', page:'dean-dashboard' },
        { icon:'👨‍🏫', label:'Faculty Workload', page:'dean-dashboard' },
        { icon:'📊', label:'Reports', page:'reports' },
      ]},
    ]
  },
  chair: {
    name: 'Rodrigo Tan', label: 'Chair, BSIS Dept.', avatar: 'C', color: '#d97706',
    nav: [
      { section:'Main', items:[
        { icon:'🏠', label:'Dashboard', page:'chair-dashboard' },
        { icon:'📅', label:'Schedule Plotter', page:'schedule-plotter' },
        { icon:'👨‍🏫', label:'Faculty Load', page:'chair-dashboard' },
        { icon:'📚', label:'Subjects', page:'subjects' },
        { icon:'🏫', label:'Rooms', page:'rooms' },
      ]},
      { section:'Reports', items:[
        { icon:'📊', label:'Export Reports', page:'reports', badge:'2' },
      ]},
    ]
  },
  faculty: {
    name: 'Jerome Bautista', label: 'Faculty, BSIS', avatar: 'JB', color: '#16a34a',
    nav: [
      { section:'Main', items:[
        { icon:'', label:'Dashboard', page:'faculty-dashboard' },
        { icon:'', label:'My Subjects', page:'faculty-subjects' },
        { icon:'', label:'My Schedule', page:'faculty-schedule' },
        { icon:'', label:'Settings', page:'faculty-settings' },
      ]},
    ]
  },
};

// ── SUBJECTS ───────────────────────────────────────────────────────────────────
function openEditSubject(code, name, units, lec, lab, dept) {
  document.getElementById('es-code').value = code;
  document.getElementById('es-name').value = name;
  document.getElementById('es-units').value = units;
  document.getElementById('es-lec').value = lec;
  document.getElementById('es-lab').value = lab;
  setSelectValue('es-dept', dept);
  document.getElementById('es-save-btn').onclick = () => {
    closeModal('modal-edit-subject');
    showToast('Subject ' + document.getElementById('es-code').value + ' updated successfully!');
  };
  openModal('modal-edit-subject');
}

// ── ROOMS ──────────────────────────────────────────────────────────────────────
function openViewRoom(name, type, cap, status, sched, faculty, loc, notes) {
  document.getElementById('vr-name').textContent = name;
  document.getElementById('vr-type').textContent = type;
  document.getElementById('vr-cap').textContent = cap + ' seats';
  document.getElementById('vr-loc').textContent = loc;
  document.getElementById('vr-sched').textContent = sched;
  document.getElementById('vr-faculty').textContent = faculty !== '—' ? 'Faculty: ' + faculty : '';
  document.getElementById('vr-notes').textContent = notes;
  const statusColors = { 'Available':'badge-green', 'In Use':'badge-amber' };
  document.getElementById('vr-status-badge').innerHTML = `<span class="badge ${statusColors[status]||'badge-grey'}">${status}</span>`;
  openModal('modal-view-room');
}

// ── SUBJECTS ───────────────────────────────────────────────────────────────────
function saveAddSubject() {
  const code = document.getElementById('add-subj-code').value.trim();
  const name = document.getElementById('add-subj-name').value.trim();
  if (!code || !name) { alert('Please fill in Subject Code and Name.'); return; }
  const units = document.getElementById('add-subj-units').value || '3';
  const lec   = document.getElementById('add-subj-lec').value || '0';
  const lab   = document.getElementById('add-subj-lab').value || '0';
  const dept  = document.getElementById('add-subj-dept').value;
  // Append row to subjects table
  const tbody = document.querySelector('#subjects-table');
  const tr = document.createElement('tr');
  tr.innerHTML = `<td><span style="font-family:var(--mono);font-weight:700">${code}</span></td>
    <td>${name}</td><td>${units}</td><td>${lec}</td><td>${lab}</td><td>${dept}</td>
    <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditSubject('${code}','${name}','${units}','${lec}','${lab}','${dept}')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'${code}')">Delete</button></td>`;
  tbody.appendChild(tr);
  // Clear fields
  ['add-subj-code','add-subj-name','add-subj-units','add-subj-lec','add-subj-lab','add-subj-desc'].forEach(id => document.getElementById(id).value = '');
  closeModal('modal-add-subject');
  showToast('Subject "' + name + '" added successfully!');
}

function openEditSubject(code, name, units, lec, lab, dept) {
  document.getElementById('edit-subj-code').value  = code;
  document.getElementById('edit-subj-name').value  = name;
  document.getElementById('edit-subj-units').value = units;
  document.getElementById('edit-subj-lec').value   = lec;
  document.getElementById('edit-subj-lab').value   = lab;
  setSelectValue('edit-subj-dept', dept);
  document.getElementById('edit-subj-save-btn').onclick = () => {
    closeModal('modal-edit-subject');
    showToast('Subject "' + document.getElementById('edit-subj-name').value + '" updated!');
  };
  openModal('modal-edit-subject');
}

// ── ROOMS ──────────────────────────────────────────────────────────────────────
function saveAddRoom() {
  const name = document.getElementById('add-room-name').value.trim();
  if (!name) { alert('Please enter a room name.'); return; }
  const type       = document.getElementById('add-room-type').value;
  const capacity   = document.getElementById('add-room-capacity').value || '—';
  const location   = document.getElementById('add-room-location').value || '—';
  const facilities = document.getElementById('add-room-facilities').value || '—';
  const tbody = document.querySelector('#rooms-table');
  const tr = document.createElement('tr');
  tr.innerHTML = `<td><b>${name}</b></td><td>${type}</td><td>${capacity}</td>
    <td><span class="badge badge-green">Available</span></td>
    <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;"
      onclick="openViewRoom('${name}','${type}','${capacity}','Available','—','—','${location}','${facilities}')">View</button></td>`;
  tbody.appendChild(tr);
  ['add-room-name','add-room-capacity','add-room-location','add-room-facilities'].forEach(id => document.getElementById(id).value = '');
  closeModal('modal-add-room');
  showToast('Room "' + name + '" added successfully!');
}

function openViewRoom(name, type, capacity, status, assignment, faculty, location, facilities) {
  document.getElementById('vr-name').textContent     = name;
  document.getElementById('vr-type').textContent     = type;
  document.getElementById('vr-capacity').textContent = capacity;
  document.getElementById('vr-location').textContent = location;
  document.getElementById('vr-assignment').textContent = assignment;
  document.getElementById('vr-faculty').textContent  = faculty !== '—' ? 'Faculty: ' + faculty : '';
  document.getElementById('vr-facilities').textContent = facilities;
  const colors = { 'Available':'badge-green', 'In Use':'badge-amber', 'Under Maintenance':'badge-red' };
  document.getElementById('vr-status-badge').innerHTML = `<span class="badge ${colors[status]||'badge-grey'}">${status}</span>`;
  openModal('modal-view-room');
}

// ── THEME ──────────────────────────────────────────────────────────────────────
function applyTheme(theme) {
  if (theme === 'dark') {
    document.body.classList.add('dark');
  } else {
    document.body.classList.remove('dark');
  }
  showToast('Theme switched to ' + (theme === 'dark' ? 'Dark' : 'Light') + ' mode!');
}

// ── SETTINGS ───────────────────────────────────────────────────────────────────
function showSettingsSection(section, el) {
  ['profile','general','academic','notifications','security','system'].forEach(s => {
    const elem = document.getElementById('settings-' + s);
    if (elem) elem.style.display = 'none';
  });
  const target = document.getElementById('settings-' + section);
  if (target) target.style.display = 'block';
  document.querySelectorAll('.settings-nav-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
}

function previewProfilePic(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const preview = document.getElementById('profile-pic-preview');
    preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
    showToast('Profile photo updated!');
  };
  reader.readAsDataURL(input.files[0]);
}

function resetProfilePic() {
  document.getElementById('profile-pic-preview').innerHTML = '';
  document.getElementById('pic-upload').value = '';
  showToast('Profile photo removed.');
}

function savePersonalInfo() {
  const first = document.getElementById('pi-firstname').value.trim();
  const last  = document.getElementById('pi-lastname').value.trim();
  const rank  = document.getElementById('pi-rank').value;
  if (!first || !last) { alert('First and Last name are required.'); return; }
  document.getElementById('sb-name').textContent = first + ' ' + last;
  document.getElementById('sb-role').textContent = rank;
  showToast('Personal info saved — ' + first + ' ' + last + '!');
}

function toggleSwitch(input) {
  const track = input.nextElementSibling;
  if (input.checked) { track.classList.add('on'); }
  else { track.classList.remove('on'); }
  showToast('Notification preference updated!');
}

function openEditRoom(name, type, capacity, status, location, facilities) {
  document.getElementById('edit-room-header').textContent = name;
  document.getElementById('edit-room-name').value       = name;
  document.getElementById('edit-room-capacity').value   = capacity;
  document.getElementById('edit-room-location').value   = location;
  document.getElementById('edit-room-facilities').value = facilities;
  setSelectValue('edit-room-type', type);
  setSelectValue('edit-room-status', status);
  document.getElementById('edit-room-save-btn').onclick = () => {
    closeModal('modal-edit-room');
    showToast('Room "' + document.getElementById('edit-room-name').value + '" updated successfully!');
  };
  openModal('modal-edit-room');
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

function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].value === value || sel.options[i].text === value) {
      sel.selectedIndex = i; break;
    }
  }
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

  // Show/hide rows
  filteredRows.forEach((row, i) => {
    const start = (currentPage - 1) * rowsPerPage;
    row.style.display = (i >= start && i < start + rowsPerPage) ? '' : 'none';
  });

  // Page info
  const start = Math.min((currentPage - 1) * rowsPerPage + 1, total);
  const end = Math.min(currentPage * rowsPerPage, total);
  document.getElementById('page-info').textContent = total === 0 ? 'No results found' : `Showing ${start}–${end} of ${total} users`;

  // Prev / Next buttons
  document.getElementById('btn-prev').disabled = currentPage === 1;
  document.getElementById('btn-prev').style.opacity = currentPage === 1 ? '0.4' : '1';
  document.getElementById('btn-next').disabled = currentPage === totalPages;
  document.getElementById('btn-next').style.opacity = currentPage === totalPages ? '0.4' : '1';

  // Page number buttons
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
  // Hide all first
  all.forEach(r => r.style.display = 'none');
  currentPage = 1;
  renderPage();
}


function selectRole(el, role) {
  document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
  currentRole = role;
}

function switchAccount(email, label) {
  document.getElementById('login-email').value = email;
  // Highlight selected hint
  document.getElementById('hint-admin').style.borderColor = 'rgba(255,255,255,0.12)';
  document.getElementById('hint-faculty').style.borderColor = 'rgba(255,255,255,0.12)';
  if (email === 'admin@ctu.edu.ph') {
    document.getElementById('hint-admin').style.borderColor = 'var(--blue-light)';
  } else {
    document.getElementById('hint-faculty').style.borderColor = 'var(--blue-light)';
  }
}

function doLogin() {
  const email = document.getElementById('login-email') ? document.getElementById('login-email').value : '';
  // Route by email — faculty email → faculty role, everything else → admin
  if (email === 'j.bautista@ctu.edu.ph') {
    currentRole = 'faculty';
  } else {
    currentRole = 'admin';
  }
  document.getElementById('screen-login').classList.remove('active');
  const app = document.getElementById('screen-app');
  app.classList.add('active');
  app.style.display = 'flex';
  setupRole(currentRole);
}

function logout() {
  document.getElementById('screen-app').classList.remove('active');
  document.getElementById('screen-app').style.display = 'none';
  document.getElementById('screen-login').classList.add('active');
}

// ── SETUP ROLE ─────────────────────────────────────────────────────────────
function setupRole(role) {
  const r = ROLES[role];
  document.getElementById('sb-avatar').textContent = r.avatar;
  document.getElementById('sb-avatar').style.background = r.color;
  document.getElementById('sb-name').textContent = r.name;
  const banner = document.getElementById('banner-name');
  if (banner) banner.textContent = r.name;
  // Render role-specific notifications
  renderNotifList(role);
  document.getElementById('sb-role').textContent = r.label;

  // Build nav
  const nav = document.getElementById('sidebar-nav');
  nav.innerHTML = '';
  r.nav.forEach(section => {
    const lbl = document.createElement('div');
    lbl.className = 'nav-section-label';
    lbl.textContent = section.section;
    nav.appendChild(lbl);
    section.items.forEach((item, i) => {
      const el = document.createElement('div');
      el.className = 'nav-item' + (i===0 && section === r.nav[0] ? ' active' : '');
      el.innerHTML = `${item.label}${item.badge ? `<span class="nav-badge">${item.badge}</span>`:''}`;
      el.onclick = () => { goToPage('page-' + item.page); setActiveNav(el); };
      nav.appendChild(el);
    });
  });

  // Show default page
  const defaultPage = r.nav[0].items[0].page;
  goToPage('page-' + defaultPage);
  if (defaultPage === 'admin-dashboard') setTimeout(initQuotes, 100);
}

function setActiveNav(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}

// ── PAGE NAVIGATION ────────────────────────────────────────────────────────
function goToPage(pageId) {
  document.querySelectorAll('.page').forEach(p => {
    p.classList.remove('active');
    p.style.display = 'none';
  });
  const page = document.getElementById(pageId);
  if (!page) return;

  if (pageId === 'page-faculty-dashboard') { setTimeout(initFacQuotes, 0); }
  if (pageId === 'page-faculty-schedule') { setTimeout(startWebRoomCountdown, 0); }
  if (pageId === 'page-faculty-mobile') {
    page.style.display = 'flex';
    setTimeout(initFacultyQuote, 0);
  } else {
    page.style.display = 'block';
  }
  page.classList.add('active');
  if (pageId === 'page-user-accounts') { currentPage = 1; setTimeout(initPagination, 0); }
  if (pageId === 'page-admin-dashboard') { setTimeout(initQuotes, 0); }

  const titles = {
    'page-settings':           'Settings',
    'page-user-accounts':      'User Accounts',
    'page-admin-dashboard':   'Admin Dashboard',
    'page-dean-dashboard':    'Dean Dashboard',
    'page-chair-dashboard':   'Department Chair Dashboard',
    'page-schedule-plotter':  'Schedule Plotter',
    'page-faculty-dashboard':    'My Dashboard',
    'page-faculty-subjects':     'My Subjects',
    'page-faculty-schedule':     'My Schedule',
    'page-faculty-settings':     'Settings',
    'page-faculty-notifications':'Notifications',
    'page-faculty-mobile':    'Faculty Mobile View',
    'page-reports':           'Reports & Export',
    'page-subjects':          'Subject Management',
    'page-rooms':             'Room Management',
  };
  document.getElementById('topbar-title').textContent = titles[pageId] || 'SKEDYUL';
}


// ── NOTIFICATION BELL ──────────────────────────────────────────────────────
const NOTIFS_BY_ROLE = {
  admin: [
    { dot:'var(--red)', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
    { dot:'var(--amber)', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
    { dot:'var(--blue)', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
    { dot:'var(--green)', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
    { dot:'var(--blue)', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
  ],
  faculty: [
    { dot:'var(--blue)', text:'<b>Schedule Updated</b> — CC 313 time changed to 7:00 AM Mon & Wed.', time:'Today, 8:12 AM', unread:true },
    { dot:'var(--green)', text:'<b>New Assignment</b> — CC 401 added to your load for 1st Semester.', time:'Yesterday, 3:45 PM', unread:true },
    { dot:'var(--amber)', text:'<b>Reminder</b> — Faculty schedule submission deadline is Friday.', time:'Mar 16, 9:00 AM', unread:true },
    { dot:'var(--green)', text:'<b>System</b> — 1st Semester AY 2025–2026 schedule has been published.', time:'Mar 10, 10:00 AM', unread:false },
  ],
  dean: [
    { dot:'var(--red)', text:'<b>Overload Alert</b> — Carlo Mendoza exceeds max teaching load.', time:'Today, 08:00 AM', unread:true },
    { dot:'var(--amber)', text:'<b>Schedule Pending</b> — BSIT 2nd year schedule still unsubmitted.', time:'Yesterday, 2:00 PM', unread:true },
    { dot:'var(--green)', text:'<b>Report Ready</b> — Workload summary report is ready for download.', time:'Mar 16, 9:00 AM', unread:false },
  ],
  chair: [
    { dot:'var(--red)', text:'<b>Conflict Detected</b> — BSIS 3-A: CC 313 and IT 401 overlap Tue 10:30.', time:'Today, 09:00 AM', unread:true },
    { dot:'var(--amber)', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
    { dot:'var(--blue)', text:'<b>New Submission</b> — Ana Reyes submitted preferred schedule.', time:'Yesterday, 11:00 AM', unread:false },
  ],
};

function renderNotifList(role) {
  const list = document.getElementById('notif-list');
  if (!list) return;
  const notifs = NOTIFS_BY_ROLE[role] || NOTIFS_BY_ROLE['admin'];
  list.innerHTML = notifs.map((n,i) => `
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

// ── FACULTY SCHEDULE DAY TABS ──────────────────────────────────────────────
function showWebDay(day, el) {
  document.querySelectorAll('.web-day-panel').forEach(p => p.style.display = 'none');
  document.getElementById('wday-' + day).style.display = 'block';
  // Update tab buttons
  const card = el.closest('.card');
  if (card) card.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
}

// ── FACULTY SUBJECT DETAIL MODAL ───────────────────────────────────────────
function openWebSubjectDetail(code, name, units, lec, lab, dept, room, section, schedule, color) {
  document.getElementById('wsd-header').style.background = 'linear-gradient(135deg,' + color + ',#0f172a)';
  document.getElementById('wsd-code').textContent = code + ' · ' + dept;
  document.getElementById('wsd-name').textContent = name;
  document.getElementById('wsd-units').textContent = units + 'u';
  document.getElementById('wsd-hours').textContent = lec + 'h Lecture' + (parseInt(lab) > 0 ? ' · ' + lab + 'h Lab' : '');
  document.getElementById('wsd-room').textContent = room;
  document.getElementById('wsd-section').textContent = section;
  document.getElementById('wsd-schedule').textContent = schedule;
  openModal('modal-web-subject-detail');
}

// ── FACULTY ROOM COUNTDOWN ─────────────────────────────────────────────────
function startWebRoomCountdown() {
  const end = new Date(); end.setHours(8, 30, 0, 0);
  const start = new Date(); start.setHours(7, 0, 0, 0);
  function tick() {
    const cur = new Date();
    const remaining = end - cur;
    const total = end - start;
    const el = document.getElementById('web-room-countdown');
    const prog = document.getElementById('web-room-progress');
    if (!el) return;
    if (remaining <= 0) { el.textContent = '00:00'; el.style.color = '#f87171'; if (prog) prog.style.width = '100%'; return; }
    const mins = Math.floor(remaining / 60000);
    const secs = Math.floor((remaining % 60000) / 1000);
    el.textContent = String(mins).padStart(2,'0') + ':' + String(secs).padStart(2,'0');
    el.style.color = mins < 5 ? '#f87171' : mins < 15 ? '#fbbf24' : '#4ade80';
    if (prog) prog.style.width = Math.min(100, Math.max(0, ((total - remaining) / total) * 100)) + '%';
  }
  tick(); setInterval(tick, 1000);
}

// ── FACULTY SETTINGS ───────────────────────────────────────────────────────
function showFacSettingsSection(section, el) {
  ['profile','security','notifications'].forEach(s => {
    const elem = document.getElementById('fac-settings-' + s);
    if (elem) elem.style.display = 'none';
  });
  const target = document.getElementById('fac-settings-' + section);
  if (target) target.style.display = 'block';
  document.querySelectorAll('#fac-settings-content ~ * .settings-nav-item, #page-faculty-settings .settings-nav-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
}
function facPreviewPic(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const p = document.getElementById('fac-pic-preview');
    p.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
    showToast('Profile photo updated!');
  };
  reader.readAsDataURL(input.files[0]);
}
function facResetPic() {
  const p = document.getElementById('fac-pic-preview');
  if (p) p.innerHTML = 'JB';
  document.getElementById('fac-pic-upload').value = '';
  showToast('Profile photo removed.');
}

// ── FACULTY QUOTES ─────────────────────────────────────────────────────────
const FAC_QUOTES = [
  { text: '"The art of teaching is the art of assisting discovery."', author: '— Mark Van Doren' },
  { text: '"A good teacher can inspire hope, ignite the imagination, and instill a love of learning."', author: '— Brad Henry' },
  { text: '"Teaching is the one profession that creates all other professions."', author: '— Unknown' },
  { text: '"The mediocre teacher tells. The good teacher explains. The great teacher inspires."', author: '— William Arthur Ward' },
  { text: '"To teach is to touch a life forever."', author: '— Unknown' },
  { text: '"Education is not preparation for life; education is life itself."', author: '— John Dewey' },
];
let facQuoteIndex = 0;
let facQuoteTimer = null;

function renderFacQuote() {
  const q = FAC_QUOTES[facQuoteIndex];
  const t = document.getElementById('fac-quote-text');
  const a = document.getElementById('fac-quote-author');
  const d = document.getElementById('fac-quote-dots');
  if (!t) return;
  t.style.opacity = '0'; a.style.opacity = '0';
  setTimeout(() => {
    t.textContent = q.text; a.textContent = q.author;
    t.style.transition = 'opacity 0.5s'; a.style.transition = 'opacity 0.5s';
    t.style.opacity = '1'; a.style.opacity = '1';
  }, 300);
  if (d) {
    d.innerHTML = '';
    FAC_QUOTES.forEach((_, i) => {
      const dot = document.createElement('div');
      dot.style.cssText = `width:6px;height:6px;border-radius:50%;background:${i===facQuoteIndex?'rgba(255,255,255,0.9)':'rgba(255,255,255,0.25)'};cursor:pointer;transition:background 0.3s;`;
      dot.onclick = () => { facQuoteIndex = i; renderFacQuote(); resetFacQuoteTimer(); };
      d.appendChild(dot);
    });
  }
}
function nextFacQuote() { facQuoteIndex = (facQuoteIndex + 1) % FAC_QUOTES.length; renderFacQuote(); resetFacQuoteTimer(); }
function prevFacQuote() { facQuoteIndex = (facQuoteIndex - 1 + FAC_QUOTES.length) % FAC_QUOTES.length; renderFacQuote(); resetFacQuoteTimer(); }
function resetFacQuoteTimer() { clearInterval(facQuoteTimer); facQuoteTimer = setInterval(nextFacQuote, 6000); }
function initFacQuotes() { facQuoteIndex = Math.floor(Math.random() * FAC_QUOTES.length); renderFacQuote(); resetFacQuoteTimer(); }

// ── FACULTY QUOTES (mobile frame) ──────────────────────────────────────────
const FACULTY_QUOTES = [
  { text: '"The art of teaching is the art of assisting discovery."', author: '— Mark Van Doren' },
  { text: '"A good teacher can inspire hope, ignite the imagination, and instill a love of learning."', author: '— Brad Henry' },
  { text: '"Teaching is the one profession that creates all other professions."', author: '— Unknown' },
  { text: '"The mediocre teacher tells. The good teacher explains. The great teacher inspires."', author: '— William Arthur Ward' },
  { text: '"Education is not preparation for life; education is life itself."', author: '— John Dewey' },
  { text: '"To teach is to touch a life forever."', author: '— Unknown' },
];

function initFacultyQuote() {
  const q = FACULTY_QUOTES[Math.floor(Math.random() * FACULTY_QUOTES.length)];
  const t = document.getElementById('faculty-quote');
  const a = document.getElementById('faculty-quote-author');
  if (t) t.textContent = q.text;
  if (a) a.textContent = q.author;
}

// ── MOTIVATIONAL QUOTES ────────────────────────────────────────────────────
const QUOTES = [
  { text: '"Education is the most powerful weapon which you can use to change the world."', author: '— Nelson Mandela' },
  { text: '"The beautiful thing about learning is that no one can take it away from you."', author: '— B.B. King' },
  { text: '"An investment in knowledge pays the best interest."', author: '— Benjamin Franklin' },
  { text: '"Education is not the filling of a pail, but the lighting of a fire."', author: '— W.B. Yeats' },
  { text: '"The more that you read, the more things you will know. The more that you learn, the more places you\'ll go."', author: '— Dr. Seuss' },
  { text: '"Live as if you were to die tomorrow. Learn as if you were to live forever."', author: '— Mahatma Gandhi' },
  { text: '"It does not matter how slowly you go as long as you do not stop."', author: '— Confucius' },
  { text: '"The capacity to learn is a gift; the ability to learn is a skill; the willingness to learn is a choice."', author: '— Brian Herbert' },
];
let quoteIndex = 0;
let quoteTimer = null;

function renderQuote() {
  const q = QUOTES[quoteIndex];
  const textEl = document.getElementById('quote-text');
  const authEl = document.getElementById('quote-author');
  const dotsEl = document.getElementById('quote-dots');
  if (!textEl) return;
  textEl.style.opacity = '0';
  authEl.style.opacity = '0';
  setTimeout(() => {
    textEl.textContent = q.text;
    authEl.textContent = q.author;
    textEl.style.transition = 'opacity 0.5s';
    authEl.style.transition = 'opacity 0.5s';
    textEl.style.opacity = '1';
    authEl.style.opacity = '1';
  }, 300);
  // Dots
  if (dotsEl) {
    dotsEl.innerHTML = '';
    QUOTES.forEach((_, i) => {
      const d = document.createElement('div');
      d.style.cssText = `width:6px;height:6px;border-radius:50%;background:${i === quoteIndex ? 'rgba(255,255,255,0.9)' : 'rgba(255,255,255,0.25)'};transition:background 0.3s;cursor:pointer;`;
      d.onclick = () => { quoteIndex = i; renderQuote(); resetQuoteTimer(); };
      dotsEl.appendChild(d);
    });
  }
}

function nextQuote() { quoteIndex = (quoteIndex + 1) % QUOTES.length; renderQuote(); resetQuoteTimer(); }
function prevQuote() { quoteIndex = (quoteIndex - 1 + QUOTES.length) % QUOTES.length; renderQuote(); resetQuoteTimer(); }

function resetQuoteTimer() {
  clearInterval(quoteTimer);
  quoteTimer = setInterval(nextQuote, 6000);
}

function initQuotes() {
  quoteIndex = Math.floor(Math.random() * QUOTES.length);
  renderQuote();
  resetQuoteTimer();
}


// ── DELETE FUNCTIONS ───────────────────────────────────────────────────────
let _currentDeleteName = '';

function deleteCurrentUser(modalId) {
  const name = document.getElementById('edit-name') 
    ? document.getElementById('edit-name').value 
    : document.getElementById('profile-name').textContent;
  if (!confirm('Delete user: ' + name + '?\nThis action cannot be undone.')) return;
  closeModal(modalId);
  // Remove from users table
  const rows = document.querySelectorAll('#users-table .user-row, #users-table tr');
  rows.forEach(row => {
    if (row.textContent.includes(name)) {
      row.style.transition = 'opacity 0.3s';
      row.style.opacity = '0';
      setTimeout(() => { row.remove(); initPagination(); }, 300);
    }
  });
  showToast(name + ' deleted successfully.');
}

function deleteCurrentRoom(modalId) {
  const name = document.getElementById('vr-name') ? document.getElementById('vr-name').textContent 
    : (document.getElementById('edit-room-name') ? document.getElementById('edit-room-name').value : 'Room');
  if (!confirm('Delete room: ' + name + '?\nThis action cannot be undone.')) return;
  closeModal(modalId);
  const rows = document.querySelectorAll('#rooms-table tr');
  rows.forEach(row => {
    if (row.textContent.includes(name)) {
      row.style.transition = 'opacity 0.3s';
      row.style.opacity = '0';
      setTimeout(() => row.remove(), 300);
    }
  });
  showToast(name + ' deleted successfully.');
}

function deleteTableRow(btn, name) {
  if (!confirm('Delete: ' + name + '?\nThis action cannot be undone.')) return;
  const row = btn.closest('tr');
  if (row) {
    row.style.transition = 'opacity 0.3s';
    row.style.opacity = '0';
    setTimeout(() => { 
      row.remove();
      if (document.getElementById('users-table')) initPagination();
    }, 300);
  }
  showToast(name + ' deleted successfully.');
}

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

function showNotifToast() { showToast('You have 3 new notifications'); }

// ── TABS ───────────────────────────────────────────────────────────────────
document.querySelectorAll('.tab-bar').forEach(bar => {
  bar.querySelectorAll('.tab-btn').forEach(btn => {
    btn.onclick = () => { bar.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active')); btn.classList.add('active'); };
  });
});

</script>
</body>
</html>