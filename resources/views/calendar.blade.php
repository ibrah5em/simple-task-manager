@extends('layouts.app')

@push('head')
{{-- FullCalendar v6 injects its own CSS via JS — no separate CSS file needed --}}
<style>
    #calendar-wrap {
        border-radius: 16px;
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 1.25rem;
        min-height: 500px;
    }
    .fc .fc-toolbar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
    }
    .fc .fc-button {
        background: var(--surface-2) !important;
        border: 1px solid var(--border) !important;
        color: var(--text) !important;
        border-radius: 8px !important;
        font-size: .8rem !important;
        font-weight: 600 !important;
        padding: .3rem .75rem !important;
        box-shadow: none !important;
    }
    .fc .fc-button:hover,
    .fc .fc-button-active {
        background: rgba(124,58,237,.15) !important;
        border-color: rgba(124,58,237,.4) !important;
        color: var(--purple-500) !important;
    }
    .fc .fc-today-button {
        background: linear-gradient(135deg, var(--purple-500), var(--purple-600)) !important;
        border-color: transparent !important;
        color: #fff !important;
    }
    .fc th {
        font-size: .8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: .5rem 0 !important;
    }
    .fc .fc-col-header-cell-cushion {
        color: var(--text-muted) !important;
    }
    .fc .fc-daygrid-day {
        border-color: var(--border) !important;
    }
    .fc .fc-daygrid-day-number {
        color: var(--text-muted);
        font-size: .82rem;
        padding: .3rem .5rem;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
        background: var(--purple-500);
        color: #fff;
        border-radius: 50%;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: .25rem .25rem 0 auto;
    }
    .fc .fc-day-today {
        background: rgba(124,58,237,.05) !important;
    }
    .fc-event {
        border-radius: 6px !important;
        font-size: .75rem !important;
        font-weight: 600 !important;
        padding: .1rem .35rem !important;
        cursor: pointer;
    }
    .fc-event-completed {
        opacity: .55;
        text-decoration: line-through;
    }
    .fc .fc-col-header-cell-cushion,
    .fc .fc-daygrid-day-number {
        text-decoration: none !important;
    }
    .fc .fc-daygrid-more-link {
        font-size: .75rem;
        color: var(--purple-500);
        font-weight: 600;
    }
    [data-bs-theme="dark"] .fc .fc-daygrid-day {
        background: transparent;
    }
    [data-bs-theme="dark"] .fc-theme-standard td,
    [data-bs-theme="dark"] .fc-theme-standard th {
        border-color: rgba(167,139,250,0.18) !important;
    }
    [data-bs-theme="dark"] .fc-scrollgrid,
    [data-bs-theme="dark"] .fc-scrollgrid-section > * {
        border-color: rgba(167,139,250,0.18) !important;
    }
    [data-bs-theme="dark"] .fc .fc-col-header-cell {
        background: rgba(20,14,45,0.6);
    }
    [data-bs-theme="dark"] .fc .fc-daygrid-day-frame {
        background: transparent;
    }
    [data-bs-theme="dark"] .fc-day-other .fc-daygrid-day-top {
        opacity: .35;
    }
    [data-bs-theme="dark"] .fc-day-other {
        background: rgba(0,0,0,0.15) !important;
    }
    [data-bs-theme="dark"] .fc .fc-daygrid-body,
    [data-bs-theme="dark"] .fc .fc-scrollgrid-sync-table {
        background: transparent;
    }
    [data-bs-theme="dark"] .fc .fc-scroller {
        background: transparent;
    }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 anim-fade-up flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--text)">Calendar</h4>
        <p class="mb-0" style="color:var(--text-muted);font-size:.875rem">
            Click a day to add a task, click a task to edit it.
        </p>
    </div>
    <a href="{{ route('tasks.index') }}" class="btn btn-ghost btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-list-task me-1"></i> All Tasks
    </a>
</div>

<div id="calendar-wrap" class="anim-fade-up anim-delay-1">
    <div id="calendar"></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
(function () {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    if (typeof FullCalendar === 'undefined') {
        calendarEl.innerHTML = '<p class="text-center py-5" style="color:var(--text-muted)">Calendar library failed to load. Please check your internet connection and refresh.</p>';
        return;
    }

    var feedUrl   = '{{ route('tasks.calendar') }}';
    var csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    var cal = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,dayGridWeek',
        },
        height: 'auto',
        /* Simple URL event source — FullCalendar automatically appends
           start, end, and timeZone query params on every fetch. */
        events: {
            url:     feedUrl,
            failure: function () {
                if (typeof window.toast === 'function') {
                    window.toast('Could not load tasks.', 'error');
                }
            },
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        dateClick: function (info) {
            var modal = document.getElementById('quickAddModal');
            if (!modal) return;

            var input = modal.querySelector('#quick-add-input');
            if (input) {
                var d      = new Date(info.dateStr + 'T00:00:00');
                var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                var label  = months[d.getMonth()] + ' ' + d.getDate();
                input.value = label + ' ';
                input.dispatchEvent(new Event('input'));
            }

            var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
            bsModal.show();

            /* After modal opens, put cursor at start so user types the title */
            modal.addEventListener('shown.bs.modal', function focusFix() {
                if (input) { input.setSelectionRange(0, 0); input.focus(); }
                modal.removeEventListener('shown.bs.modal', focusFix);
            });
        },
        eventDidMount: function (info) {
            info.el.title = info.event.title;
        },
        dayMaxEvents: 4,
        nowIndicator: true,
    });

    cal.render();
})();
</script>
@endpush
