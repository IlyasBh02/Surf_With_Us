<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $course->load('coach', 'reservations');
        $relatedCourses = Course::where('coach_id', $course->coach_id)
            ->where('id', '!=', $course->id)
            ->where('date', '>=', now())
            ->take(3)
            ->get();

        return view('courses.show', compact('course', 'relatedCourses'));
    }

    public function book(Course $course)
    {
        // Must be a surfeur
        if (Auth::user()->role !== 'surfeur') {
            return back()->with('error', 'Only surfeurs can book courses.');
        }

        // Course must not be in the past
        if ($course->date->isPast()) {
            return back()->with('error', 'This course has already taken place.');
        }

        // Check already booked
        $alreadyBooked = Reservation::where('course_id', $course->id)
            ->where('surfeur_id', Auth::id())
            ->exists();

        if ($alreadyBooked) {
            return back()->with('error', 'You have already booked this course.');
        }

        // Check available places
        $bookedCount = $course->reservations()->where('status', 'confirmed')->count();
        if ($bookedCount >= $course->available_places) {
            return back()->with('error', 'Sorry, this course is fully booked.');
        }

        Reservation::create([
            'course_id'  => $course->id,
            'surfeur_id' => Auth::id(),
            'status'     => 'confirmed',
        ]);

        return back()->with('success', 'Course booked successfully! See you on the waves 🏄');
    }
}
