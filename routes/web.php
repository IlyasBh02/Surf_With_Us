<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoachCourseController;
use App\Http\Controllers\NotificationController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [HomeController::class, 'index'])->name('courses.browse');
Route::get('/courses/{course}', fn($course) => abort(404))->name('courses.show');
Route::get('/coaches/{user}',   fn($user)   => abort(404))->name('courses.coach');

// Auth (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard redirect
Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard')->middleware('auth');

Route::middleware('auth')->group(function () {

    // Notifications
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');

    // ── Admin ──────────────────────────────────────────────────────────────
    Route::get('/admin/dashboard',   [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/users',       [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/coaches',     [AdminController::class, 'coaches'])->name('admin.coaches');
    Route::post('/admin/coaches/{user}/approve', [AdminController::class, 'approveCoach'])->name('admin.approve-coach');
    Route::post('/admin/coaches/{user}/reject',  [AdminController::class, 'rejectCoach'])->name('admin.reject-coach');
    Route::post('/admin/users/{user}/status',    [AdminController::class, 'changeUserStatus'])->name('admin.change-user-status');
    Route::delete('/admin/users/{user}',         [AdminController::class, 'deleteUser'])->name('admin.delete-user');
    Route::get('/admin/courses',      fn() => abort(404))->name('admin.courses');
    Route::get('/admin/reservations', fn() => abort(404))->name('admin.reservations');
    Route::get('/admin/settings',     fn() => abort(404))->name('admin.settings');

    // ── Coach ──────────────────────────────────────────────────────────────
    Route::get('/coach/dashboard', [DashboardController::class, 'coach'])->name('coach.dashboard');
    Route::get('/coach/profile',   fn() => abort(404))->name('coach.profile');
    Route::get('/coach/reservations', fn() => abort(404))->name('coach.reservations');

    Route::get('/coach/courses',              [CoachCourseController::class, 'index'])->name('coach.courses.index');
    Route::get('/coach/courses/create',       [CoachCourseController::class, 'create'])->name('coach.courses.create');
    Route::post('/coach/courses',             [CoachCourseController::class, 'store'])->name('coach.courses.store');
    Route::get('/coach/courses/{course}',     [CoachCourseController::class, 'show'])->name('coach.courses.show');
    Route::get('/coach/courses/{course}/reservations', [CoachCourseController::class, 'show'])->name('coach.course.reservations');
    Route::get('/coach/courses/{course}/edit',[CoachCourseController::class, 'edit'])->name('coach.courses.edit');
    Route::put('/coach/courses/{course}',     [CoachCourseController::class, 'update'])->name('coach.courses.update');
    Route::delete('/coach/courses/{course}',  [CoachCourseController::class, 'destroy'])->name('coach.courses.destroy');

    // ── Surfeur ────────────────────────────────────────────────────────────
    Route::get('/surfeur/dashboard',  [DashboardController::class, 'surfeur'])->name('surfeur.dashboard');
    Route::get('/surfeur/profile',    fn() => abort(404))->name('surfeur.profile');
    Route::get('/surfeur/reservations', fn() => abort(404))->name('surfer.reservations');
    Route::get('/surfeur/reservations/{reservation}', fn($r) => abort(404))->name('surfer.reservations.show');
});
