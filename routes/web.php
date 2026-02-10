<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingReceiptController;
use App\Http\Controllers\BillingExportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Livewire\Academics\Sessions as AcademicSessions;
use App\Livewire\Announcements\Index as AnnouncementsIndex;
use App\Livewire\Billing\Index as BillingIndex;
use App\Livewire\Attendance\Index as AttendanceIndex;
use App\Livewire\Certificates\Index as CertificatesIndex;
use App\Livewire\Events\Index as EventsIndex;
use App\Livewire\Results\Entry as ResultsEntry;
use App\Livewire\Results\Broadsheet as ResultsBroadsheet;
use App\Livewire\Messages\Index as MessagesIndex;
use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Livewire\Promotions\Index as PromotionsIndex;
use App\Livewire\Premium\Devices as PremiumDevices;
use App\Livewire\Students\Form as StudentsForm;
use App\Livewire\Students\Index as StudentsIndex;
use App\Livewire\Timetable\Index as TimetableIndex;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Imports\Index as ImportsIndex;
use App\Livewire\Imports\Students as ImportsStudents;
use App\Livewire\Imports\Subjects as ImportsSubjects;
use App\Livewire\Imports\Teachers as ImportsTeachers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user?->role === 'teacher') {
            return view('pages.dashboard-teacher');
        }

        return view('pages.dashboard');
    })->name('dashboard');
    Route::view('/more-features', 'pages.more-features.index')->name('more-features');

    Route::middleware('role:admin')->group(function () {
        Route::get('/students/create', StudentsForm::class)->name('students.create');
        Route::get('/students/{student}/edit', StudentsForm::class)->name('students.edit');

        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::post('/teachers/{teacher}/photo', [TeacherController::class, 'updatePhoto'])->name('teachers.photo');
        Route::post('/teachers/{teacher}/allocations', [TeacherController::class, 'storeAllocation'])->name('teachers.allocations.store');
        Route::delete('/teachers/{teacher}/allocations/{allocation}', [TeacherController::class, 'destroyAllocation'])->name('teachers.allocations.destroy');

        Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
        Route::patch('/classes/{class}', [SchoolClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');
        Route::post('/classes/{class}/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::patch('/classes/{class}/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/classes/{class}/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::patch('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

        Route::get('/users', UsersIndex::class)->name('users.index');

        Route::get('/imports', ImportsIndex::class)->name('imports.index');
        Route::get('/imports/students', ImportsStudents::class)->name('imports.students');
        Route::get('/imports/teachers', ImportsTeachers::class)->name('imports.teachers');
        Route::get('/imports/subjects', ImportsSubjects::class)->name('imports.subjects');

        Route::post('/settings/school', [SettingsController::class, 'updateSchool'])->name('settings.update-school');
        Route::post('/settings/results', [SettingsController::class, 'updateResults'])->name('settings.update-results');
        Route::post('/settings/certificates', [SettingsController::class, 'updateCertificates'])->name('settings.update-certificates');
    });

    Route::get('/students', StudentsIndex::class)->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

    Route::middleware('role:admin,bursar')->group(function () {
        Route::get('/billing', BillingIndex::class)->name('billing.index');
        Route::get('/billing/receipt/{transaction}', [BillingReceiptController::class, 'download'])
            ->name('billing.receipt');
        Route::get('/billing/export/transactions', [BillingExportController::class, 'transactions'])
            ->name('billing.export.transactions');
    });

    Route::middleware('role:admin,teacher')->group(function () {
        Route::view('/institute', 'pages.institute.index')->name('institute');
        Route::view('/teachers', 'pages.teachers.index')->name('teachers');
        Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes.index');
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/attendance', AttendanceIndex::class)->name('attendance');
        Route::view('/examination', 'pages.examination.index')->name('examination');

        Route::get('/results/entry', ResultsEntry::class)->name('results.entry');
        Route::get('/results/broadsheet', ResultsBroadsheet::class)->name('results.broadsheet');
        Route::get('/results/report-card/{student}', [ReportCardController::class, 'download'])->name('results.report-card');

        Route::get('/events', EventsIndex::class)->name('events');
        Route::get('/timetable', TimetableIndex::class)->name('timetable');
        Route::get('/certificates', CertificatesIndex::class)->name('certificates');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    });

    Route::middleware('role:admin,bursar')->group(function () {
        Route::view('/accounts', 'pages.accounts.index')->name('accounts');
    });

    Route::middleware('role:admin,teacher,bursar')->group(function () {
        Route::get('/messages', MessagesIndex::class)->name('messages');
        Route::get('/announcements', AnnouncementsIndex::class)->name('announcements');
        Route::get('/notifications', NotificationsIndex::class)->name('notifications');
    });

    Route::middleware('role:admin')->group(function () {
        Route::view('/settings', 'pages.settings.index')->name('settings');
        Route::get('/settings/devices', PremiumDevices::class)->name('settings.devices');
        Route::get('/settings/backup', [BackupController::class, 'index'])->name('settings.backup');
        Route::post('/settings/backup', [BackupController::class, 'create'])->name('settings.backup.create');
        Route::post('/settings/restore', [BackupController::class, 'restore'])->name('settings.restore');

        Route::get('/promotions', PromotionsIndex::class)->name('promotions');
        Route::get('/academic-sessions', AcademicSessions::class)->name('academic-sessions');
    });
});
