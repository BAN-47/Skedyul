<div id="schedule-reuse-preview-modal" class="modal-overlay" style="display:none" hidden>
    <section class="modal-box w-[760px] max-w-[calc(100vw-32px)] max-h-[92vh] overflow-y-auto !rounded-xl !border !border-slate-200 !bg-white !p-0 !text-slate-900"
        role="dialog" aria-modal="true" aria-labelledby="schedule-reuse-preview-title">
        <header class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4">
            <div>
                <h2 id="schedule-reuse-preview-title" class="text-base font-bold text-indigo-700">Schedule Reuse Preview</h2>
                <p id="schedule-reuse-terms" class="mt-1 text-xs text-slate-500"></p>
            </div>
            <div class="flex items-center gap-3">
                <span id="schedule-reuse-overall-status" class="hidden rounded-full px-2.5 py-1 text-[11px] font-semibold"></span>
                <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-800" data-reuse-close aria-label="Close preview">×</button>
            </div>
        </header>

        <div class="px-5 py-4">
            <div id="schedule-reuse-loading" class="hidden items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                <span aria-hidden="true" class="h-5 w-5 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent"></span>
                Checking archived schedules against the current semester…
            </div>
            <div id="schedule-reuse-global-message" class="hidden mb-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800"></div>
            <div id="schedule-reuse-summary" class="mb-4 grid grid-cols-3 gap-3"></div>
            <div id="schedule-reuse-selection-controls" class="mb-1 flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-700">Assignment checking</h3>
                <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-600">
                    <input id="schedule-reuse-select-all" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Select all schedules
                </label>
            </div>
            <div id="schedule-reuse-rows" class="divide-y divide-slate-200"></div>
            <div id="schedule-reuse-blocked" class="hidden mt-4 gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                <span aria-hidden="true" class="text-amber-600">⚠</span>
                <div>
                    <div class="font-semibold">Copying is paused because one or more assignments need attention.</div>
                    <div class="mt-1 text-amber-800">Unselect assignments you do not want to reuse, or close this preview, make corrections, then click Reuse Schedule again for a fresh check.</div>
                </div>
            </div>
            <div id="schedule-reuse-workload-reason-wrap" class="hidden mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3">
                <label for="schedule-reuse-workload-reason" class="block text-sm font-semibold text-amber-900">Required reason for workload exception</label>
                <textarea id="schedule-reuse-workload-reason" rows="3" maxlength="1000"
                    class="mt-2 w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm text-slate-900"
                    placeholder="Explain why this faculty workload exception is needed."></textarea>
            </div>
            <div id="schedule-reuse-preview-error" class="hidden mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
            <div id="schedule-reuse-preview-success" class="hidden mt-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800"></div>
        </div>

        <footer class="sticky bottom-0 z-10 flex flex-wrap justify-end gap-2 border-t border-slate-200 bg-white px-5 py-3">
            <button type="button" class="rounded-full border border-slate-300 px-5 py-2 text-xs text-slate-700 hover:bg-slate-50" data-reuse-close>Cancel</button>
            <button type="button" id="schedule-reuse-confirm" class="rounded-full bg-indigo-600 px-5 py-2 text-xs font-semibold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50" disabled>Confirm Reuse</button>
            <button type="button" id="schedule-reuse-refresh" class="hidden rounded-full bg-indigo-600 px-5 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Refresh schedule</button>
        </footer>
    </section>
</div>
