<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

///////////////////////////////////////////// Courses public ///////////////////////////////////////////////////
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

//////////////////////////////////////////////// Dashboard redirect ///////////////////////////////////////////// 
Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard')->middleware('auth');

/////////////////////////////////////////////// Role dashboards ///////////////////////////////////////////// 
Route::middleware('auth')->group(function () {
    // Admin
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/users',       fn() => abort(404))->name('admin.users');
    Route::get('/admin/coaches',     fn() => abort(404))->name('admin.coaches');
    Route::get('/admin/courses',     fn() => abort(404))->name('admin.courses');
    Route::get('/admin/reservations',fn() => abort(404))->name('admin.reservations');
    Route::get('/admin/settings',    fn() => abort(404))->name('admin.settings');
    Route::post('/admin/coaches/{user}/approve', fn($user) => abort(404))->name('admin.approve-coach');
    Route::post('/admin/coaches/{user}/reject',  fn($user) => abort(404))->name('admin.reject-coach');

    // Coach
    Route::get('/coach/dashboard',          [DashboardController::class, 'coach'])->name('coach.dashboard');
    Route::get('/coach/courses',            fn() => abort(404))->name('coach.courses.index');
    Route::get('/coach/courses/create',     fn() => abort(404))->name('coach.courses.create');
    Route::get('/coach/courses/{course}',   fn($c) => abort(404))->name('coach.courses.show');
    Route::get('/coach/courses/{course}/edit', fn($c) => abort(404))->name('coach.courses.edit');
    Route::get('/coach/reservations',       fn() => abort(404))->name('coach.reservations');
    Route::get('/coach/profile',            fn() => abort(404))->name('coach.profile');

    // Surfeur
    Route::get('/surfeur/dashboard',        [DashboardController::class, 'surfeur'])->name('surfeur.dashboard');
    Route::get('/surfeur/reservations',     fn() => abort(404))->name('surfer.reservations');
    Route::get('/surfeur/reservations/{reservation}', fn($r) => abort(404))->name('surfer.reservations.show');
    Route::get('/surfeur/profile',          fn() => abort(404))->name('surfeur.profile');
});
