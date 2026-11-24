<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.favicon')
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SKEDYUL — Faculty Designations</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">
  @include('partials.admin_sidebar')

  <div class="app-main">
    @include('partials.admin_header', ['title' => 'Faculty Designations'])

    <div class="page-content">
      <div class="card">
        <div class="card-header border-b-0 pb-0 flex justify-between items-center">
          <div>
            <div class="card-title">Faculty Designations</div>
            <div class="card-sub">Manage dynamic hour caps and faculty count limits</div>
          </div>
          <button type="button" class="btn btn-primary btn-sm" onclick="openDesignationModal()">Add Designation</button>
        </div>
        <div class="p-4">
          <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Designation Name</th>
                  <th>Day Cap</th>
                  <th>Evening Cap</th>
                  <th>Max Faculty</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody id="designations-table-body">
                @foreach($designations as $d)
                  <tr data-id="{{ $d->id }}">
                    <td class="font-semibold">{{ $d->name }}</td>
                    <td>{{ $d->day_cap }}h</td>
                    <td>{{ $d->evening_cap }}h</td>
                    <td>{{ $d->max_faculty_count ?? 'Unlimited' }}</td>
                    <td class="text-right">
                      <button type="button" class="btn btn-secondary btn-sm p-1.5" onclick="editDesignation(@js($d))">
                        ✏️ Edit
                      </button>
                      <button type="button" class="btn btn-secondary btn-sm p-1.5 text-rose-600 hover:bg-rose-50" onclick="deleteDesignation('{{ $d->id }}', '{{ $d->name }}')">
                        🗑️ Delete
                      </button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="designation-modal" class="modal-overlay" style="display:none;">
  <div class="modal-content max-w-md bg-white p-6 rounded-xl shadow-xl">
    <div class="modal-header flex justify-between items-center mb-4">
      <h3 class="modal-title font-bold text-lg" id="designation-modal-title">Add Designation</h3>
      <button type="button" class="modal-close text-slate-400 hover:text-slate-600" onclick="closeDesignationModal()">✕</button>
    </div>
    <div class="modal-body space-y-4">
      <input type="hidden" id="designation-id">
      <div>
        <label class="field-label block text-xs font-bold text-slate-500 mb-1">Designation Name</label>
        <input type="text" id="designation-name" class="field-input w-full" placeholder="e.g. Research Chair">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="field-label block text-xs font-bold text-slate-500 mb-1">Day Hour Cap</label>
          <input type="number" id="designation-day-cap" class="field-input w-full" value="30">
        </div>
        <div>
          <label class="field-label block text-xs font-bold text-slate-500 mb-1">Evening Hour Cap</label>
          <input type="number" id="designation-evening-cap" class="field-input w-full" value="30">
        </div>
      </div>
      <div>
        <label class="field-label block text-xs font-bold text-slate-500 mb-1">Max Faculty Count (Optional)</label>
        <input type="number" id="designation-max-count" class="field-input w-full" placeholder="Unlimited">
        <p class="text-[10px] text-slate-400 mt-1">Leave blank for no limit. Prevents assigning more than this many faculty members.</p>
      </div>
    </div>
    <div class="modal-footer flex justify-end gap-2 mt-6">
      <button type="button" class="btn btn-secondary" onclick="closeDesignationModal()">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="saveDesignation()">Save Designation</button>
    </div>
  </div>
</div>

<div class="toast" id="toast" style="display:none;"><span id="toast-msg"></span></div>

<script>
function csrfToken() {
  var m = document.querySelector('meta[name="csrf-token"]');
  return m ? m.content : '';
}

function showToast(msg) {
  alert(msg);
}

function openDesignationModal(designation = null) {
  document.getElementById('designation-id').value = designation ? designation.id : '';
  document.getElementById('designation-modal-title').textContent = designation ? 'Edit Designation' : 'Add Designation';
  document.getElementById('designation-name').value = designation ? designation.name : '';
  document.getElementById('designation-day-cap').value = designation ? designation.day_cap : 30;
  document.getElementById('designation-evening-cap').value = designation ? designation.evening_cap : 30;
  document.getElementById('designation-max-count').value = designation && designation.max_faculty_count !== null ? designation.max_faculty_count : '';
  document.getElementById('designation-modal').style.display = 'flex';
}

function closeDesignationModal() {
  document.getElementById('designation-modal').style.display = 'none';
}

function editDesignation(designation) {
  openDesignationModal(designation);
}

async function saveDesignation() {
  const id = document.getElementById('designation-id').value;
  const payload = {
    name: document.getElementById('designation-name').value.trim(),
    day_cap: parseInt(document.getElementById('designation-day-cap').value || 30),
    evening_cap: parseInt(document.getElementById('designation-evening-cap').value || 30),
    max_faculty_count: document.getElementById('designation-max-count').value ? parseInt(document.getElementById('designation-max-count').value) : null,
  };

  const url = id ? '{{ url("/admin/designations") }}/' + id : '{{ route("admin.designations.store") }}';
  const method = id ? 'PUT' : 'POST';

  try {
    const res = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
      },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (!res.ok || !data.success) {
      showToast(data.message || 'Failed to save designation');
      return;
    }
    window.location.reload();
  } catch (err) {
    showToast('Network error saving designation');
  }
}

async function deleteDesignation(id, name) {
  if (!confirm('Are you sure you want to delete designation "' + name + '"?')) return;

  try {
    const res = await fetch('{{ url("/admin/designations") }}/' + id, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
      },
    });
    const data = await res.json();
    if (!res.ok || !data.success) {
      showToast(data.message || 'Failed to delete designation');
      return;
    }
    window.location.reload();
  } catch (err) {
    showToast('Network error deleting designation');
  }
}
</script>
</body>
</html>
