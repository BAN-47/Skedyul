<div id="chair-export-preview-modal"
    class="fixed inset-0 z-[200] hidden items-center justify-center bg-slate-950/50 p-3 md:p-6" role="dialog"
    aria-modal="true" aria-labelledby="chair-export-preview-title" aria-hidden="true">
    <div class="flex max-h-[94vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <header class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-600">Export preview</p>
                <h2 id="chair-export-preview-title" class="text-lg font-bold text-slate-900">Check the export details
                </h2>
            </div>
            <button type="button" data-export-preview-close aria-label="Close export preview"
                class="rounded-lg p-2 text-xl leading-none text-slate-500 hover:bg-slate-100 hover:text-slate-900">&times;</button>
        </header>
        <div id="chair-export-preview-content" class="min-h-0 overflow-y-auto p-4 md:p-6">
            <div class="py-12 text-center text-sm text-slate-500">Preparing preview…</div>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('chair-export-preview-modal');
        const content = document.getElementById('chair-export-preview-content');
        if (!modal || !content || modal.dataset.ready === 'true') return;
        modal.dataset.ready = 'true';
        // Keep the dialog outside the app shell so its overflow rules cannot clip it.
        if (modal.parentElement !== document.body) document.body.appendChild(modal);
        modal.style.zIndex = '10000';
        modal.style.display = 'none';

        const closeModal = () => {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        };

        const openPreview = async (url) => {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            content.innerHTML =
                '<div class="py-12 text-center text-sm text-slate-500">Preparing preview…</div>';

            try {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/json'
                    }
                });
                const html = await response.text();
                if (!response.ok) {
                    let message = `Could not load the preview (HTTP ${response.status}).`;
                    try {
                        const payload = JSON.parse(html);
                        message = payload.message || message;
                    } catch (_) {}
                    throw new Error(message);
                }
                if (/<html[\s>]/i.test(html)) {
                    throw new Error(
                        'The preview returned a full page. Check that the controller uses the modal response for AJAX requests.'
                        );
                }
                content.innerHTML = html;
                content.querySelector('[data-export-preview-close]')?.focus();
            } catch (error) {
                content.innerHTML = '';
                const message = document.createElement('p');
                message.className = 'rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700';
                message.textContent = error instanceof Error ? error.message :
                    'Could not load the export preview.';
                content.appendChild(message);
            }
        };

        window.openChairExportPreview = openPreview;

        document.addEventListener('click', async (event) => {
            const closeButton = event.target.closest('[data-export-preview-close]');
            if (closeButton) {
                event.preventDefault();
                closeModal();
                return;
            }

            if (event.target === modal) {
                closeModal();
                return;
            }

            const trigger = event.target.closest('[data-export-preview]');
            if (trigger) {
                event.preventDefault();
                await openPreview(trigger.href || trigger.dataset.previewUrl);
                return;
            }

            const confirmButton = event.target.closest('[data-export-pbs-confirm]');
            if (confirmButton) {
                const countInput = content.querySelector('#preview-student-count');
                const count = Number(String(countInput?.value ?? '').trim());
                if (!Number.isInteger(count) || count < 0 || count > 500) {
                    countInput?.focus();
                    window.alert('Enter a whole student count from 0 to 500.');
                    return;
                }

                confirmButton.disabled = true;
                confirmButton.textContent = 'Preparing download…';
                try {
                    const response = await fetch(confirmButton.dataset.studentUrl, {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            sec_id: confirmButton.dataset.sectionId,
                            sec_no_of_student: count
                        })
                    });
                    const result = await response.json().catch(() => ({}));
                    if (!response.ok || !result.success) throw new Error(result.message ||
                        'Could not save the section student count.');
                    window.location.assign(confirmButton.dataset.downloadUrl);
                } catch (error) {
                    window.alert(error instanceof Error ? error.message :
                        'Could not prepare the PBS export.');
                    confirmButton.disabled = false;
                    confirmButton.textContent = 'Confirm and Download Excel';
                }
            }
        });

        document.addEventListener('submit', async (event) => {
            const form = event.target.closest('form[data-export-preview-form]');
            if (!form) return;
            event.preventDefault();
            const url = new URL(form.action, window.location.href);
            new FormData(form).forEach((value, key) => url.searchParams.set(key, String(value)));
            await openPreview(url.toString());
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    })();
</script>
