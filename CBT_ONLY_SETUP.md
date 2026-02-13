# CBT Only Mode (Reusable Deployment)

This project can run as a **standalone CBT system** (LAN/offline) without using the other modules.

## 1) Enable CBT mode

In `.env`:

- `MYACADEMY_MODE=cbt`

Optional:

- `MYACADEMY_PREMIUM_ENFORCE=false` (disables license gating for CBT routes/portal)

## 2) Migrate database

Run:

- `php artisan migrate`

## 3) Create staff accounts

Create at least:

- An `admin` user (approves exams, monitors attempts)
- One or more `teacher` users (creates exams/questions)

You can use the existing **User Management** screen (admin).

## 4) Set up academic data

Admin should create:

- Classes
- Subjects
- Students (assigned to classes)
- Subject allocations (assign teacher -> class -> subject)

## 5) Use CBT

- Teacher: `CBT` -> create exam -> add questions -> submit to admin
- Admin: open exam -> **Approve** -> share generated **Exam Code**
- Student portal (no login): `/cbt/portal` -> enter code + admission number -> take exam

Notes:

- Admin can **Pause/Go Live** on approved exams.
- Admin can **Reset** a student attempt from the exam page (allows retake).
- Student portal main URL: `/cbt/student` (alias: `/cbt/portal`).
