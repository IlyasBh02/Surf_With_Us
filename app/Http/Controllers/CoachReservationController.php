<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoachReservationController extends Controller
{
    public function index(Request $request)
    {
        $coachId = Auth::id();

        $query = Reservation::with(['surfeur', 'course'])
            ->whereHas('course', fn($q) => $q->where('coach_id', $coachId));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('surfeur', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        $reservations = $query->latest()->paginate(15);

        $courses = Course::where('coach_id', $coachId)->orderBy('date', 'desc')->get();

        $courseStats = $courses->mapWithKeys(fn($course) => [
            $course->id => [
                'title'     => $course->title,
                'date'      => $course->date->format('d M Y'),
                'available' => $course->available_places,
                'booked'    => $course->reservations()->where('status', 'confirmed')->count(),
                'remaining' => $course->available_places - $course->reservations()->where('status', 'confirmed')->count(),
            ]
        ]);

        return view('coach.reservations.index', compact('reservations', 'courses', 'courseStats'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        if ($reservation->course->coach_id !== Auth::id()) abort(403);

        $request->validate(['status' => ['required', 'in:confirmed,cancelled']]);

        $reservation->update(['status' => $request->status]);

        return back()->with('success', 'Reservation status updated to ' . $request->status . '.');
    }
}
