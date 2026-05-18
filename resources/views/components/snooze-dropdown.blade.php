@props(['task'])

<div class="dropdown d-inline-block snooze-dropdown">
    <button type="button"
            class="btn btn-sm btn-ghost"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false"
            data-snooze-route="{{ route('tasks.snooze', $task) }}"
            title="Snooze task"
            style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);color:var(--purple-500);border-radius:12px;padding:.35rem .75rem;font-weight:500;">
        <i class="bi bi-alarm"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:190px;">
        <li>
            <button type="button" class="dropdown-item snooze-preset-btn" data-offset="1">
                <i class="bi bi-sun me-2 text-warning"></i> Tomorrow
            </button>
        </li>
        <li>
            <button type="button" class="dropdown-item snooze-preset-btn" data-offset="7">
                <i class="bi bi-calendar2-week me-2 text-primary"></i> Next week
            </button>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
        <li class="px-3 py-2">
            <label class="form-label mb-1" style="font-size:.75rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Pick a date</label>
            <div class="d-flex gap-1">
                <input type="date"
                       class="form-control form-control-sm snooze-date-input"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       style="font-size:.82rem;">
                <button type="button"
                        class="btn btn-sm snooze-custom-btn"
                        style="background:linear-gradient(135deg,var(--purple-500),var(--purple-600));border:none;color:#fff;border-radius:8px;padding:.3rem .75rem;white-space:nowrap;">
                    Go
                </button>
            </div>
        </li>
    </ul>
</div>

@pushOnce('scripts')
<script>
(function () {
    function csrfToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }

    function snoozeTask(route, until) {
        fetch(route, {
            method: 'PATCH',
            body: JSON.stringify({ until: until }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            }
        })
        .then(function (r) {
            if (!r.ok) throw new Error('Server error');
            return r.json();
        })
        .then(function (data) {
            if (typeof window.toast === 'function') {
                window.toast('Snoozed to ' + data.due_date, 'success');
            }
            setTimeout(function () { window.location.reload(); }, 800);
        })
        .catch(function () {
            if (typeof window.toast === 'function') {
                window.toast('Could not snooze task. Please try again.', 'error');
            }
        });
    }

    function offsetDate(days) {
        var d = new Date();
        d.setDate(d.getDate() + days);
        return d.toISOString().split('T')[0];
    }

    document.addEventListener('click', function (e) {
        var presetBtn = e.target.closest('.snooze-preset-btn');
        if (presetBtn) {
            var dropdown = presetBtn.closest('.snooze-dropdown');
            var toggle   = dropdown && dropdown.querySelector('[data-snooze-route]');
            if (!toggle) return;

            var days  = parseInt(presetBtn.dataset.offset, 10);
            var until = offsetDate(days);

            snoozeTask(toggle.dataset.snoozeRoute, until);

            var bsDd = bootstrap.Dropdown.getInstance(toggle);
            if (bsDd) bsDd.hide();
            return;
        }

        var customBtn = e.target.closest('.snooze-custom-btn');
        if (customBtn) {
            var dropdown = customBtn.closest('.snooze-dropdown');
            var toggle   = dropdown && dropdown.querySelector('[data-snooze-route]');
            var input    = dropdown && dropdown.querySelector('.snooze-date-input');
            if (!toggle || !input || !input.value) {
                if (typeof window.toast === 'function') {
                    window.toast('Please pick a date first.', 'error');
                }
                return;
            }

            snoozeTask(toggle.dataset.snoozeRoute, input.value);

            var bsDd = bootstrap.Dropdown.getInstance(toggle);
            if (bsDd) bsDd.hide();
        }
    });
})();
</script>
@endPushOnce
