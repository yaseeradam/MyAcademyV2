<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingReceiptController;
use App\Http\Controllers\BillingExportController;
use App\Http\Controllers\CbtExportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BulkReportCardsController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MessageAttachmentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Livewire\Classes\ManageSubjects;
use App\Livewire\Cbt\ExamEditor as CbtExamEditor;
use App\Livewire\Cbt\Index as CbtIndex;
use App\Livewire\Cbt\Portal\Start as CbtPortalStart;
use App\Livewire\Cbt\Portal\Take as CbtPortalTake;
use App\Livewire\Academics\Sessions as AcademicSessions;
use App\Livewire\Announcements\Index as AnnouncementsIndex;
use App\Livewire\AuditLogs\Index as AuditLogsIndex;
use App\Livewire\Billing\Index as BillingIndex;
use App\Livewire\Attendance\Index as AttendanceIndex;
use App\Livewire\Certificates\Index as CertificatesIndex;
use App\Livewire\Events\Index as EventsIndex;
use App\Livewire\Results\Entry as ResultsEntry;
use App\Livewire\Results\Broadsheet as ResultsBroadsheet;
use App\Livewire\Messages\Index as MessagesIndex;
use App\Livewire\Marketplace\Index as MarketplaceIndex;
use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Livewire\Promotions\Index as PromotionsIndex;
use App\Livewire\Premium\Devices as PremiumDevices;
use App\Livewire\SavingsLoan\Index as SavingsLoanIndex;
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
    if (config('myacademy.mode') === 'cbt') {
        return redirect()->route('cbt.student');
    }

    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/home', function () {
    if (config('myacademy.mode') === 'cbt') {
        return redirect()->route('cbt.student');
    }

    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(config('myacademy.premium_enforce', true) ? ['premium_public:cbt'] : [])->group(function () {
    Route::get('/cbt/portal', CbtPortalStart::class)->name('cbt.portal');
    Route::get('/cbt/portal/{attempt}', CbtPortalTake::class)->name('cbt.portal.take');

    Route::get('/cbt/student', CbtPortalStart::class)->name('cbt.student');
    Route::get('/cbt/student/{attempt}', CbtPortalTake::class)->name('cbt.student.take');
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
        if (config('myacademy.mode') === 'cbt' && in_array($user?->role, ['admin', 'teacher'], true)) {
            return redirect()->route('cbt.index');
        }
        if ($user?->role === 'teacher') {
            return view('pages.dashboard-teacher');
        }

        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.details');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    Route::view('/more-features', 'pages.more-features.index')->name('more-features');

    Route::middleware('role:admin')->group(function () {
        Route::get('/marketplace', MarketplaceIndex::class)->name('marketplace');

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

        Route::get('/users', UsersIndex::class)->middleware('permission:users.manage')->name('users.index');

        Route::get('/imports', ImportsIndex::class)->name('imports.index');
        Route::get('/imports/students', ImportsStudents::class)->name('imports.students');
        Route::get('/imports/teachers', ImportsTeachers::class)->name('imports.teachers');
        Route::get('/imports/subjects', ImportsSubjects::class)->name('imports.subjects');

        Route::get('/results/bulk-report-cards', [BulkReportCardsController::class, 'index'])
            ->middleware('permission:results.publish')
            ->name('results.bulk-report-cards');
        Route::post('/results/bulk-report-cards', [BulkReportCardsController::class, 'generate'])
            ->middleware('permission:results.publish')
            ->name('results.bulk-report-cards.generate');

        Route::post('/settings/school', [SettingsController::class, 'updateSchool'])->name('settings.update-school');
        Route::post('/settings/results', [SettingsController::class, 'updateResults'])->name('settings.update-results');
        Route::post('/settings/certificates', [SettingsController::class, 'updateCertificates'])->name('settings.update-certificates');
        Route::post('/settings/license', [SettingsController::class, 'updateLicense'])->name('settings.update-license');
    });

    Route::get('/students', StudentsIndex::class)->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

    Route::middleware('role:admin,bursar')->group(function () {
        Route::get('/billing', BillingIndex::class)->middleware('permission:billing.transactions,fees.manage')->name('billing.index');
        Route::get('/billing/receipt/{transaction}', [BillingReceiptController::class, 'download'])
            ->middleware('permission:billing.transactions')
            ->name('billing.receipt');
        Route::get('/billing/export/transactions', [BillingExportController::class, 'transactions'])
            ->middleware('permission:billing.export')
            ->name('billing.export.transactions');

        Route::get('/savings-loan', SavingsLoanIndex::class)
            ->middleware('premium:savings_loan')
            ->name('savings-loan.index');
    });

    Route::middleware('role:admin,teacher')->group(function () {
        Route::view('/institute', 'pages.institute.index')->name('institute');
        Route::view('/teachers', 'pages.teachers.index')->name('teachers');
        Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/{class}/subjects', ManageSubjects::class)->name('classes.subjects');
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/attendance', AttendanceIndex::class)->name('attendance');
        Route::view('/examination', 'pages.examination.index')->name('examination');
        Route::get('/cbt', CbtIndex::class)
            ->middleware(config('myacademy.premium_enforce', true) ? ['premium:cbt'] : [])
            ->name('cbt.index');
        Route::get('/cbt/exams/{exam}', CbtExamEditor::class)
            ->middleware(config('myacademy.premium_enforce', true) ? ['premium:cbt'] : [])
            ->name('cbt.exams.edit');

        Route::get('/results/entry', ResultsEntry::class)->middleware('permission:results.entry,results.review')->name('results.entry');
        Route::get('/results/broadsheet', ResultsBroadsheet::class)->middleware('permission:results.broadsheet')->name('results.broadsheet');
        Route::get('/results/report-card/{student}', [ReportCardController::class, 'download'])->name('results.report-card');

        Route::get('/events', EventsIndex::class)->name('events');
        Route::get('/timetable', TimetableIndex::class)->name('timetable');
        Route::get('/certificates', CertificatesIndex::class)->name('certificates');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    });

    Route::middleware('role:admin,bursar')->group(function () {
        Route::view('/accounts', 'pages.accounts.index')->middleware('permission:billing.transactions')->name('accounts');
    });

    Route::middleware('role:admin,teacher,bursar')->group(function () {
        Route::get('/messages', MessagesIndex::class)->middleware('permission:messages.access')->name('messages');
        Route::get('/messages/attachments/{message}', [MessageAttachmentController::class, 'download'])->name('messages.attachments.download');
        Route::get('/announcements', AnnouncementsIndex::class)->name('announcements');
        Route::get('/notifications', NotificationsIndex::class)->name('notifications');
    });

    Route::middleware('role:admin')->group(function () {
        Route::view('/settings', 'pages.settings.index')->name('settings');
        Route::get('/settings/devices', PremiumDevices::class)->name('settings.devices');
        Route::get('/settings/backup', [BackupController::class, 'index'])->middleware('permission:backup.manage')->name('settings.backup');
        Route::post('/settings/backup', [BackupController::class, 'create'])->middleware('permission:backup.manage')->name('settings.backup.create');
        Route::post('/settings/restore', [BackupController::class, 'restore'])->middleware('permission:backup.manage')->name('settings.restore');
        Route::get('/settings/audit-logs', AuditLogsIndex::class)->middleware('permission:audit.view')->name('settings.audit-logs');

        Route::get('/promotions', PromotionsIndex::class)->name('promotions');
        Route::get('/academic-sessions', AcademicSessions::class)->name('academic-sessions');

        Route::get('/cbt/exams/{exam}/export', [CbtExportController::class, 'examResults'])
            ->middleware(config('myacademy.premium_enforce', true) ? ['premium:cbt'] : [])
            ->name('cbt.exams.export');
    });
});
