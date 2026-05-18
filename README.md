# Simple Task Manager

A personal task management web app built on Laravel. Originally a course project for **Antakya Private University's Advanced Web Programming** module (instructors: Abdo Ibrahim Al-Khouri, Kenan Farhani), now being grown into a daily-use product.

The pristine course submission is preserved at tag [`submission-v1`](https://github.com/ibrah5em/simple-task-manager/releases/tag/submission-v1) — `git checkout submission-v1` if you ever need that exact snapshot.

## Features (shipped)

**Core app**
- Email/password auth (Laravel Breeze).
- Full task CRUD with per-user scoping enforced by policies.
- Inline complete-toggle, bulk actions (complete / uncomplete / delete), snooze (tomorrow / next week / pick a date).
- Pagination, search, filters (active / completed / overdue / due this week), sort.
- Categories (many-to-many, per-user) with color chips.
- Priority (low/med/high) with colored badges.
- Profile page (name / email / phone / password).

**Daily-use power**
- **Smart views**: Today, Inbox, Upcoming — surfaced in the sidebar with live count badges.
- **Quick-add modal** (press `c` anywhere): natural-language parser extracts `!high`/`!med`/`!low`, `#category` tags, and date phrases (`tomorrow`, `next mon`, `5pm`, `jul 4`, `in 3 days`, …) from a single text field, with live preview.
- **Recurring tasks** (RRULE / iCal via `simshaun/recurr`) — daily, weekdays, weekly, monthly. New instance auto-generated when the current one is completed; scheduler pre-materializes 7 days ahead.
- **Subtasks / checklists** with drag-to-reorder and a `3/5` progress badge in lists.
- **Markdown notes** in task descriptions (`league/commonmark`).
- **Drag-to-reorder** tasks within a view (SortableJS).
- **Calendar view** (FullCalendar) — click a day to quick-add, click a task to edit.

**UI**
- Collapsible sidebar (offcanvas on mobile, static ≥ md), state persisted to `localStorage`.
- Dark mode via Bootstrap 5.3 `data-bs-theme`, defaults to system preference.
- Toast notifications (server-side + client `window.toast(msg, type)`).
- Empty states with CTA.
- Button loading spinners.

## Roadmap

The full multi-phase plan lives in `.claude/plan.md`. Phases 1–2 are merged into `main`. Still pending:

| Phase | Theme |
|---|---|
| 3 | PWA, browser push notifications, email reminders, mobile polish |
| 4 | Dashboard analytics, admin user-management, Arabic/RTL i18n, attachments, time tracking, pomodoro, export/import |
| 5 | Email verification, password reset, Google OAuth (Socialite) |
| 6 | AI chatbot (OpenRouter) + AI-powered quick-add |
| 7 | Voice-to-text (Web Speech + Groq Whisper) |
| 8 | Deployment |

## Tech Stack

- **Backend:** Laravel (latest stable), PHP 8.x, MySQL
- **Frontend:** Blade + Bootstrap 5.3, vanilla JS
- **Auth:** Laravel Breeze (Blade stack)
- **Recurrence:** `simshaun/recurr`
- **Markdown:** `league/commonmark`
- **JS libs:** FullCalendar, SortableJS

## Local Setup

```bash
# Install
composer install
npm install && npm run build

# Configure
cp .env.example .env
php artisan key:generate
# Set DB_* values in .env, then:
php artisan migrate --seed

# Run
php artisan serve
php artisan schedule:work   # second terminal — recurring task materialization
```

**Seeded users** (password `password123` for all):

| Email | Role |
|---|---|
| admin@example.com | admin |
| user1@example.com … user5@example.com | user |

User1 has 4 example tasks; the rest are empty.

## Scheduler Setup (Recurring Tasks & Reminders)

The app uses Laravel's scheduler for recurring task materialization and (in Phase 3) push/email reminders.

**Production** — add this single cron entry to the server's crontab (`crontab -e`):

```
* * * * * cd /path/to/simple-task-manager && php artisan schedule:run >> /dev/null 2>&1
```

**Development** — run in a second terminal alongside `php artisan serve`:

```bash
php artisan schedule:work
```

The schedule runs `tasks:materialize-recurring` daily at 02:00, which pre-generates the next 7 days of recurring task instances so they show up in the calendar view ahead of time.

## License

The Laravel framework itself is MIT-licensed. Application code in this repository is for personal/educational use.
