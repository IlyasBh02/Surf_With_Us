<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CoachCourseController extends Controller
{
    private function requireApproval()
    {
        if (!Auth::user()->coach_approved) {
            return redirect()->route('coach.dashboard')
                ->with('error', 'Your account is pending admin approval. You cannot create courses yet.');
        }
        return null;
    }

    public function index()
    {
        $courses = Course::where('coach_id', Auth::id())
            ->withCount(['reservations as booked_count' => fn($q) => $q->where('status', 'confirmed')])
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('coach.courses.index', compact('courses'));
    }

    public function create()
    {
        if ($redirect = $this->requireApproval()) return $redirect;
        return view('coach.courses.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requireApproval()) return $redirect;

        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string'],
            'date'             => ['required', 'date', 'after:now'],
            'duration'         => ['required', 'integer', 'min:15', 'max:480'],
            'available_places' => ['required', 'integer', 'min:1', 'max:100'],
            'price'            => ['required', 'numeric', 'min:0'],
            'level'            => ['required', 'in:beginner,intermediate,advanced'],
            'location'         => ['nullable', 'string', 'max:255'],
            'thumbnail'        => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $data['coach_id'] = Auth::id();

        Course::create($data);

        return redirect()->route('coach.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $this->authorizeCourse($course);
        $reservations = $course->reservations()->with('surfeur')->get();
        return view('coach.courses.show', compact('course', 'reservations'));
    }

    public function edit(Course $course)
    {
        $this->authorizeCourse($course);
        return view('coach.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string'],
            'date'             => ['required', 'date'],
            'duration'         => ['required', 'integer', 'min:15', 'max:480'],
            'available_places' => ['required', 'integer', 'min:1', 'max:100'],
            'price'            => ['required', 'numeric', 'min:0'],
            'level'            => ['required', 'in:beginner,intermediate,advanced'],
            'location'         => ['nullable', 'string', 'max:255'],
            'thumbnail'        => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update($data);

        return redirect()->route('coach.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $this->authorizeCourse($course);
        if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
        $course->delete();
        return redirect()->route('coach.courses.index')
            ->with('success', 'Course deleted.');
    }

    private function authorizeCourse(Course $course)
    {
        if ($course->coach_id !== Auth::id()) abort(403);
    }
}
