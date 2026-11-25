(() => {
    const modal = document.getElementById("schedule-reuse-preview-modal");
    const confirmButton = document.getElementById("schedule-reuse-confirm");
    const refreshButton = document.getElementById("schedule-reuse-refresh");
    const closeButtons = document.querySelectorAll("[data-reuse-close]");
    const previewError = document.getElementById("schedule-reuse-preview-error");
    const globalMessage = document.getElementById("schedule-reuse-global-message");
    const loading = document.getElementById("schedule-reuse-loading");
    const overallStatus = document.getElementById("schedule-reuse-overall-status");
    const blockedNotice = document.getElementById("schedule-reuse-blocked");
    const previewSuccess = document.getElementById("schedule-reuse-preview-success");
    const rowsContainer = document.getElementById("schedule-reuse-rows");
    const summaryContainer = document.getElementById("schedule-reuse-summary");
    const reasonWrap = document.getElementById("schedule-reuse-workload-reason-wrap");
    const reasonInput = document.getElementById("schedule-reuse-workload-reason");

    if (!modal || !confirmButton) return;

    const state = {
        config: null,
        preview: null,
        applying: false,
    };

    if (modal.parentElement !== document.body) document.body.appendChild(modal);

    function showModal() {
        modal.removeAttribute("hidden");
        modal.style.display = "flex";
        modal.classList.add("active");
    }

    function hideModal() {
        if (state.applying) return;
        modal.style.display = "none";
        modal.setAttribute("hidden", "");
        modal.classList.remove("active");
    }

    function setMessage(element, message) {
        element.textContent = message || "";
        element.classList.toggle("hidden", !message);
    }

    function makeElement(tag, className, text) {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (text !== undefined) element.textContent = text;
        return element;
    }

    function getPayload() {
        return {
            [state.config.scope === "pbs" ? "section_id" : "faculty_id"]:
                state.config.scope === "pbs" ? state.config.sourceSectionId : state.config.targetId,
            destination_semester_id: state.config.destinationSemesterId,
            source_semester_id: state.config.sourceSemesterId,
        };
    }

    async function postJson(url, body) {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                Accept: "application/json",
            },
            credentials: "same-origin",
            body: JSON.stringify(body),
        });
        const data = await response.json().catch(() => ({
            message: `The server returned an unreadable response (${response.status}).`,
        }));
        if (!response.ok && !data.message) {
            const validationErrors = Object.values(data.errors || {}).flat().join(" ");
            data.message = validationErrors || `The request failed (HTTP ${response.status}).`;
        }
        return data;
    }

    function clearPreview() {
        rowsContainer.replaceChildren();
        summaryContainer.replaceChildren();
        loading.classList.add("hidden");
        loading.classList.remove("flex");
        overallStatus.classList.add("hidden");
        blockedNotice.classList.add("hidden");
        blockedNotice.classList.remove("flex");
        setMessage(previewError, "");
        setMessage(globalMessage, "");
        setMessage(previewSuccess, "");
        reasonWrap.classList.add("hidden");
        reasonInput.value = "";
        confirmButton.classList.remove("hidden");
        confirmButton.textContent = "Confirm Reuse";
        refreshButton.classList.add("hidden");
        confirmButton.disabled = true;
        state.preview = null;
    }

    function renderSummary(summary) {
        const errors = (summary.conflicts || 0) + (summary.errors || 0);
        const cards = [
            ["Valid", summary.valid || 0, "text-emerald-700"],
            ["Conflicts", summary.conflicts || 0, "text-red-700"],
            ["Needs attention", (summary.needs_review || 0) + errors + (summary.workload_overrides || 0), "text-amber-700"],
        ];
        summaryContainer.replaceChildren(...cards.map(([label, count, color]) => {
            const card = makeElement("div", "rounded-lg border border-slate-200 bg-slate-50 px-2 py-1");
            card.append(
                makeElement("div", `text-lg font-bold ${color}`, String(count)),
                makeElement("div", "text-[10px] text-slate-500", label),
            );
            return card;
        }));
    }

    function statusStyle(status) {
        return {
            valid: "bg-emerald-100 text-emerald-800",
            workload_override: "bg-amber-100 text-amber-800",
            needs_review: "bg-amber-100 text-amber-800",
            conflict: "bg-red-100 text-red-800",
            error: "bg-red-100 text-red-800",
            excluded: "bg-slate-100 text-slate-700",
        }[status] || "bg-slate-100 text-slate-700";
    }

    function statusLabel(status) {
        return {
            workload_override: "Reason required",
            needs_review: "Needs review",
            valid: "Valid",
            conflict: "Conflict",
            error: "Error",
            excluded: "Not available",
        }[status] || status;
    }

    function renderRow(row) {
        const card = makeElement("article", "py-3 text-slate-900");
        const heading = makeElement("div", "flex flex-wrap items-center justify-between gap-2");
        heading.append(
            makeElement("h4", "text-xs font-bold text-slate-900", `${row.course_code || "Course"}${row.course_name ? ` — ${row.course_name}` : ""} · ${row.section_name || "Section unavailable"}`),
            makeElement("span", `rounded-full px-2 py-1 text-[11px] font-bold ${statusStyle(row.status)}`, statusLabel(row.status)),
        );
        card.appendChild(heading);
        card.appendChild(makeElement(
            "p",
            "mt-1 text-[10px] text-slate-600",
            `Faculty: ${row.faculty_name || "—"} · ${row.room_name || "—"} · ${row.day || "—"} ${row.start_time || ""}–${row.end_time || ""}`,
        ));
        if (row.message) {
            const color = ["conflict", "error"].includes(row.status) ? "text-red-700" : "text-slate-600";
            card.appendChild(makeElement("p", `mt-1 text-[10px] ${color}`, row.message));
        }

        return card;
    }

    function renderPreview(data) {
        loading.classList.add("hidden");
        loading.classList.remove("flex");
        if (!Array.isArray(data.rows) || !data.summary) {
            state.preview = null;
            setMessage(previewError, data.message || "Unable to check the archived schedules.");
            confirmButton.disabled = true;
            return;
        }

        state.preview = data;
        setMessage(previewError, "");
        renderSummary(data.summary);
        rowsContainer.replaceChildren(...data.rows.map(renderRow));
        if (!data.rows.length) {
            rowsContainer.appendChild(makeElement("p", "py-5 text-sm text-slate-500", "No schedules are available in this archived section or faculty record."));
        }

        const blocked = (data.summary.blocked || 0) > 0;
        blockedNotice.classList.toggle("hidden", !blocked);
        blockedNotice.classList.toggle("flex", blocked);
        const notices = [];
        if (!data.can_copy && !blocked) notices.push(data.message || "");
        setMessage(globalMessage, notices.filter(Boolean).join(" "));

        const hasOverrides = (data.summary.workload_overrides || 0) > 0;
        reasonWrap.classList.toggle("hidden", !hasOverrides);
        confirmButton.disabled = !data.can_copy || state.applying || (hasOverrides && !reasonInput.value.trim());
        overallStatus.textContent = data.can_copy ? "Ready to reuse" : "Needs attention";
        overallStatus.className = data.can_copy
            ? "rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800"
            : "rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800";
        overallStatus.classList.remove("hidden");
        document.getElementById("schedule-reuse-terms").textContent =
            `${data.source_label || state.config.sourceLabel} → ${data.destination_label || state.config.destinationLabel} · ${state.config.targetLabel}`;
    }

    function showUnavailable(message) {
        clearPreview();
        showModal();
        document.getElementById("schedule-reuse-terms").textContent =
            `${state.config.sourceLabel} → ${state.config.destinationLabel} · ${state.config.targetLabel}`;
        setMessage(previewError, message);
        overallStatus.textContent = "Needs attention";
        overallStatus.className = "rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800";
        overallStatus.classList.remove("hidden");
    }

    async function checkSchedules() {
        if (!state.config?.sourceSemesterId) {
            showUnavailable("Select an archived semester before reusing schedules.");
            return;
        }
        if (!state.config.destinationSemesterId) {
            showUnavailable("No writable current semester matches this archive's semester type. Ask an administrator to enable the matching semester for plotting.");
            return;
        }

        clearPreview();
        showModal();
        loading.classList.remove("hidden");
        loading.classList.add("flex");
        document.getElementById("schedule-reuse-terms").textContent =
            `${state.config.sourceLabel} → ${state.config.destinationLabel} · ${state.config.targetLabel}`;

        try {
            const data = await postJson(state.config.previewUrl, getPayload());
            renderPreview(data);
        } catch (error) {
            loading.classList.add("hidden");
            loading.classList.remove("flex");
            setMessage(previewError, error instanceof Error ? error.message : "Unable to check these schedules.");
        }
    }

    async function applyReuse() {
        if (!state.preview?.can_copy || state.applying) return;
        const hasOverrides = (state.preview.summary?.workload_overrides || 0) > 0;
        if (hasOverrides && !reasonInput.value.trim()) {
            reasonInput.focus();
            setMessage(previewError, "Enter a reason for the workload exception before continuing.");
            return;
        }

        state.applying = true;
        confirmButton.disabled = true;
        confirmButton.setAttribute("aria-busy", "true");
        confirmButton.innerHTML = '<span class="inline-flex items-center gap-2"><span aria-hidden="true" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>Reusing schedules…</span>';
        closeButtons.forEach((button) => { button.disabled = true; });

        const payload = getPayload();
        if (hasOverrides) payload.workload_override_reason = reasonInput.value.trim();

        try {
            const result = await postJson(state.config.applyUrl, payload);
            if (result.success) {
                confirmButton.classList.add("hidden");
                setMessage(previewSuccess, result.message || "Schedules reused successfully.");
                refreshButton.classList.remove("hidden");
                return;
            }
            if (result.preview) renderPreview(result.preview);
            setMessage(previewError, result.error?.message
                ? `${result.message || "Reuse failed."} ${result.error.message}`
                : (result.message || "Schedules were not copied."));
            if (result.requires_reason) reasonWrap.classList.remove("hidden");
        } catch (error) {
            setMessage(previewError, error instanceof Error ? error.message : "Unable to reuse schedules. No schedules were copied.");
            state.preview = null;
            confirmButton.disabled = true;
        } finally {
            state.applying = false;
            confirmButton.removeAttribute("aria-busy");
            if (!confirmButton.classList.contains("hidden")) {
                confirmButton.textContent = "Confirm Reuse";
                confirmButton.disabled = !state.preview?.can_copy
                    || ((state.preview?.summary?.workload_overrides || 0) > 0 && !reasonInput.value.trim());
            }
            closeButtons.forEach((button) => { button.disabled = false; });
        }
    }

    document.addEventListener("click", (event) => {
        const button = event.target instanceof Element ? event.target.closest("#schedule-reuse-open") : null;
        if (!button) return;

        state.config = {
            scope: button.dataset.reuseScope,
            sourceSectionId: button.dataset.sourceSectionId,
            targetId: button.dataset.targetId,
            targetLabel: button.dataset.targetLabel || "Schedule",
            sourceSemesterId: button.dataset.sourceSemesterId,
            sourceLabel: button.dataset.sourceLabel || "Archived semester",
            destinationSemesterId: button.dataset.destinationSemesterId,
            destinationLabel: button.dataset.destinationLabel || "Current semester",
            previewUrl: button.dataset.previewUrl,
            applyUrl: button.dataset.applyUrl,
        };
        if (state.config.scope === "pbs" && !state.config.sourceSectionId) {
            showUnavailable("Select a section from the archived semester before reusing its schedules.");
            return;
        }
        checkSchedules();
    });

    confirmButton.addEventListener("click", applyReuse);
    reasonInput.addEventListener("input", () => {
        setMessage(previewError, "");
        const hasOverrides = (state.preview?.summary?.workload_overrides || 0) > 0;
        confirmButton.disabled = !state.preview?.can_copy || (hasOverrides && !reasonInput.value.trim());
    });
    refreshButton.addEventListener("click", () => window.location.reload());
    closeButtons.forEach((button) => button.addEventListener("click", hideModal));
    modal.addEventListener("click", (event) => {
        if (event.target === modal && !state.applying) hideModal();
    });
})();
