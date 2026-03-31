<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $courses = Course::with('coach', 'reservations')
            ->where('date', '>=', now())
            ->orderBy('date')
            ->take(6)
            ->get();

        $coaches = User::where('role', 'coach')
            ->where('coach_approved', true)
            ->get();

        return view('welcome', compact('courses', 'coaches'));
    }
}
