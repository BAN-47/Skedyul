{{--
    Combined topbar + Notify Chairs modal for dean pages.

    Usage:
        @include('partials.dean_header', ['title' => 'Dean Dashboard'])

    Optional:
        'showExport'        => false   (hides Export Report button)
        'showNotifications' => false   (hides Notifications button)

    The $chairsList used by the modal is injected automatically by the
    DeanChairsComposer view composer (see AppServiceProvider) — no need
    to pass it manually from the controller.
--}}

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-title" id="topbar-title">{{ $title ?? 'SKEDYUL' }}</div>
    <div class="flex items-center gap-2.5">
        @if($showExport ?? true)
            <button class="btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
        @endif
        @if($showNotifications ?? true)
            <button class="btn btn-secondary" onclick="showToast('3 pending approvals')">Notifications</button>
        @endif
    </div>
</div>

<!-- MODAL: NOTIFY CHAIRS -->
<div class="modal-overlay" id="modal-notify">
  <div class="modal-box w-[520px]">
    <div class="modal-header">
      <div class="modal-title">Send Notification to Chairs</div>
      <button class="modal-close" onclick="closeModal('modal-notify')">✕</button>
    </div>
    <div>
      <div class="mb-4">
        <div class="field-label mb-2">Recipients</div>
        <div class="flex flex-col gap-2">
          <label class="flex items-center gap-2.5 px-3.5 py-2.5 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
            <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)" class="w-[15px] h-[15px] accent-blue-600">
            <span class="text-[13px] font-bold text-slate-900">All Department Chairs</span>
          </label>
          <div class="flex flex-col gap-1.5 pl-3">
            @forelse ($chairsList ?? [] as $c)
              <label class="flex items-center gap-2.5 px-3.5 py-2 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
                <input type="checkbox" class="chair-check w-3.5 h-3.5 accent-blue-600" value="{{ $c['id'] }}" checked onchange="syncAllChairs()">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0" style="background:{{ $c['color'] ?? '#0891b2' }};">
                    {{ $c['initials'] ?? strtoupper(substr($c['chair'], 0, 2)) }}
                  </div>
                  <div>
                    <div class="text-[13px] font-semibold text-slate-900">{{ $c['chair'] }}</div>
                    <div class="text-[11px] text-slate-400">Chair · {{ $c['code'] }}</div>
                  </div>
                </div>
              </label>
            @empty
              <div class="text-[12px] text-slate-400 p-2">No department chairs found.</div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="mb-3.5">
        <label class="field-label">Notification Type</label>
        <select class="field-input" id="notif-type">
          <option value="info">General Info</option>
          <option value="reminder">Reminder</option>
          <option value="urgent">Urgent</option>
          <option value="deadline">Deadline Notice</option>
        </select>
      </div>

      <div class="mb-3.5">
        <label class="field-label">Title</label>
        <input class="field-input" id="notif-title" placeholder="e.g. Schedule Submission Reminder">
      </div>

      <div class="mb-3.5">
        <label class="field-label">Message</label>
        <textarea class="field-input resize-y" id="notif-message" rows="4" placeholder="Write your message to the chairs..."></textarea>
      </div>

      <div>
        <div class="text-[12px] font-bold text-slate-400 uppercase tracking-[.6px] mb-2">Recently Sent</div>
        <div id="notif-history" class="flex flex-col gap-1.5">
          <div class="text-[12px] text-slate-400 p-2 text-center">No notifications sent yet this session.</div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
      <button class="btn btn-primary" onclick="sendNotifToChairs()">Send Notification</button>
    </div>
  </div>
</div>