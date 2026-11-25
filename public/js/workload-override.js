(() => {
    function formatHours(value) {
        return Number(value || 0).toFixed(2).replace(/\.?0+$/, "");
    }

    function requestWorkloadOverride(workload) {
        return new Promise((resolve) => {
            const overlay = document.createElement("div");
            overlay.className =
                "fixed inset-0 flex items-center justify-center bg-slate-950/50 p-4";
            overlay.style.zIndex = "1100";
            overlay.setAttribute("role", "presentation");

            const dialog = document.createElement("section");
            dialog.className =
                "w-full max-w-lg rounded-2xl bg-white p-5 shadow-2xl";
            dialog.setAttribute("role", "dialog");
            dialog.setAttribute("aria-modal", "true");
            dialog.setAttribute("aria-labelledby", "workload-override-title");

            const title = document.createElement("h2");
            title.id = "workload-override-title";
            title.className = "text-lg font-bold text-rose-700";
            title.textContent = "Faculty workload limit exceeded";
            dialog.appendChild(title);

            const message = document.createElement("p");
            message.className = "mt-2 text-sm text-slate-600";
            message.textContent =
                "Force Add will record this exception and your required reason for workload analytics.";
            dialog.appendChild(message);

            const summary = document.createElement("dl");
            summary.className =
                "mt-4 grid grid-cols-2 gap-x-4 gap-y-2 rounded-xl bg-rose-50 p-3 text-sm";
            [
                ["Faculty", workload.faculty_name],
                ["Designation", workload.designation],
                ["Workload", workload.workload_label],
                ["Course", workload.course_code],
                ["Current hours", `${formatHours(workload.hours_before)}h`],
                ["Hours added", `${formatHours(workload.hours_added)}h`],
                [
                    "Projected load",
                    `${formatHours(workload.hours_after)} / ${formatHours(workload.cap_hours)}h`,
                ],
                ["Over the cap by", `${formatHours(workload.over_by)}h`],
            ].forEach(([label, value]) => {
                const group = document.createElement("div");
                const term = document.createElement("dt");
                term.className = "text-xs font-medium text-slate-500";
                term.textContent = label;
                const detail = document.createElement("dd");
                detail.className = "font-semibold text-slate-800";
                detail.textContent = value || "—";
                group.append(term, detail);
                summary.appendChild(group);
            });
            dialog.appendChild(summary);

            const label = document.createElement("label");
            label.className = "mt-4 block text-sm font-semibold text-slate-800";
            label.htmlFor = "workload-override-reason";
            label.textContent = "Required reason for this designation exception";
            dialog.appendChild(label);

            const reason = document.createElement("textarea");
            reason.id = "workload-override-reason";
            reason.className =
                "mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-100";
            reason.rows = 3;
            reason.maxLength = 1000;
            reason.required = true;
            reason.setAttribute("aria-describedby", "workload-override-error");
            dialog.appendChild(reason);

            const error = document.createElement("p");
            error.id = "workload-override-error";
            error.className = "mt-1 hidden text-xs text-rose-700";
            error.textContent = "Enter a reason before forcing this assignment.";
            dialog.appendChild(error);

            const actions = document.createElement("div");
            actions.className = "mt-4 flex justify-end gap-2";
            const cancel = document.createElement("button");
            cancel.type = "button";
            cancel.className =
                "rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50";
            cancel.textContent = "Cancel";
            const confirm = document.createElement("button");
            confirm.type = "button";
            confirm.className =
                "rounded-lg bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800";
            confirm.textContent = "Force Add";
            actions.append(cancel, confirm);
            dialog.appendChild(actions);
            overlay.appendChild(dialog);
            document.body.appendChild(overlay);

            let settled = false;
            const finish = (result) => {
                if (settled) return;
                settled = true;
                document.removeEventListener("keydown", onKeyDown);
                overlay.remove();
                resolve(result);
            };
            const onKeyDown = (event) => {
                if (event.key === "Escape") finish(null);
            };
            const submit = () => {
                const value = reason.value.trim();
                if (!value) {
                    error.classList.remove("hidden");
                    reason.focus();
                    return;
                }
                finish(value);
            };

            cancel.addEventListener("click", () => finish(null));
            confirm.addEventListener("click", submit);
            reason.addEventListener("input", () => error.classList.add("hidden"));
            overlay.addEventListener("click", (event) => {
                if (event.target === overlay) finish(null);
            });
            document.addEventListener("keydown", onKeyDown);
            reason.focus();
        });
    }

    window.submitScheduleWithWorkloadOverride = async function (url, payload) {
        const send = async (body) => {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                    Accept: "application/json",
                },
                credentials: "same-origin",
                body: JSON.stringify(body),
            });
            const data = await response.json().catch(() => ({
                message: `The server returned an unreadable response (${response.status}).`,
            }));
            if (!response.ok && !data.message && !data.errors) {
                data.message = `Schedule could not be saved (HTTP ${response.status}).`;
            }
            return { response, data };
        };

        const firstAttempt = await send(payload);
        if (!firstAttempt.data.requires_workload_override) {
            return { ...firstAttempt, cancelled: false };
        }

        const reason = await requestWorkloadOverride(
            firstAttempt.data.workload || {},
        );
        if (reason === null) {
            return { ...firstAttempt, cancelled: true };
        }

        return {
            ...(await send({
                ...payload,
                workload_override: true,
                workload_override_reason: reason,
            })),
            cancelled: false,
        };
    };
})();
