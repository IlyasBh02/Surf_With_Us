<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirect()
    {
        return match (Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'coach'   => redirect()->route('coach.dashboard'),
            'surfeur' => redirect()->route('surfeur.dashboard'),
            default   => redirect()->route('home'),
        };
    }

    public function admin()
    {
        return view('admin.dasboard', [
            'totalUsers'             => User::count(),
            'totalAdmins'            => User::where('role', 'admin')->count(),
            'totalCoaches'           => User::where('role', 'coach')->count(),
            'totalSurfeurs'          => User::where('role', 'surfeur')->count(),
            'activeCoaches'          => User::where('role', 'coach')->where('coach_approved', true)->count(),
            'pendingCoachesCount'    => User::where('role', 'coach')->where('coach_approved', false)->count(),
            'pendingCoaches'         => User::where('role', 'coach')->where('coach_approved', false)->get(),
            'activeCourses'          => Course::where('date', '>=', now())->count(),
            'completedCourses'       => Course::where('date', '<', now())->count(),
            'upcomingCourses'        => Course::whereBetween('date', [now(), now()->addWeek()])->count(),
            'totalReservations'      => Reservation::count(),
            'newUsersThisMonth'      => User::whereMonth('created_at', now()->month)->count(),
            'newReservationsThisWeek'=> Reservation::where('created_at', '>=', now()->startOfWeek())->count(),
            'lastBackup'             => 'N/A',
            'recentActivities'       => [],
        ]);
    }

    public function coach()
    {
        $coach = Auth::user();
        return view('coach.dashboard', [
            'activeCourses'       => Course::where('coach_id', $coach->id)->where('date', '>=', now())->count(),
            'upcomingCourses'     => Course::where('coach_id', $coach->id)->whereBetween('date', [now(), now()->addWeek()])->count(),
            'totalStudents'       => Reservation::whereHas('course', fn($q) => $q->where('coach_id', $coach->id))->distinct('surfeur_id')->count(),
            'totalReservations'   => Reservation::whereHas('course', fn($q) => $q->where('coach_id', $coach->id))->count(),
            'newStudentsThisMonth'=> 0,
            'newReservationsThisWeek' => 0,
            'upcomingCoursesList' => [],
            'recentReservations'  => [],
        ]);
    }

    public function surfeur()
    {
        $user = Auth::user();
        $reservations = $user->reservations()->with('course.coach')->get();
        $upcomingReservations = $reservations->filter(fn($r) => $r->course && $r->course->date >= now());
        $completedCourses = $reservations->filter(fn($r) => $r->course && $r->course->date < now() && $r->status === 'confirmed')->count();

        return view('surfeur.dashboard', [
            'totalReservations'    => $reservations->count(),
            'confirmedReservations'=> $reservations->where('status', 'confirmed')->count(),
            'completedCourses'     => $completedCourses,
            'upcomingReservations' => $upcomingReservations,
            'recommendedCourses'   => Course::with('coach')->where('date', '>=', now())->take(3)->get(),
        ]);
    }
}
