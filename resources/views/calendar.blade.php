@extends('layouts.app')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<style>
    #calendar-wrap {
        border-radius: 16px;
        overflow: hidden;
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 1.25rem;
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
        color: var(--text-muted);
        font-size: .8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: .5rem 0 !important;
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

    var feedUrl  = '{{ route('tasks.calendar') }}';
    var parseUrl = '{{ route('tasks.parse') }}';
    var storeUrl = '{{ route('tasks.store') }}';
    var csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek'
        },
        height: 'auto',
        eventSources: [{
            url: feedUrl,
            extraParams: function () {
                var view = calendar.view;
                return {
                    start: view.currentStart.toISOString().split('T')[0],
                    end:   view.currentEnd.toISOString().split('T')[0],
                };
            },
            failure: function () {
                if (typeof window.toast === 'function') {
                    window.toast('Could not load tasks. Please refresh.', 'error');
                }
            },
        }],
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        dateClick: function (info) {
            /* Pre-fill the quick-add modal with the clicked date */
            var modal = document.getElementById('quickAddModal');
            if (!modal) return;

            var input = modal.querySelector('#quick-add-input');
            if (input) {
                /* Format date as "Mon DD" so the parser picks it up */
                var d = new Date(info.dateStr + 'T00:00:00');
                var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                var label  = months[d.getMonth()] + ' ' + d.getDate();
                input.value = label + ' ';
                input.dispatchEvent(new Event('input'));
                input.focus();
                /* Move cursor to start so user types the title first */
                input.setSelectionRange(0, 0);
            }

            var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
            bsModal.show();
        },
        eventDidMount: function (info) {
            info.el.title = info.event.title;
        },
        dayMaxEvents: 4,
        moreLinkClick: 'popover',
        nowIndicator: true,
    });

    calendar.render();
})();
</script>
@endpush
