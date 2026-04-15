<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        $pendingCoachesCount = User::where('role', 'coach')->where('coach_approved', false)->count();
        return view('admin.users.index', compact('users', 'pendingCoachesCount'));
    }

    public function coaches()
    {
        $pendingCoaches = User::where('role', 'coach')->where('coach_approved', false)->orderBy('created_at', 'desc')->get();
        $approvedCoaches = User::where('role', 'coach')->where('coach_approved', true)->orderBy('created_at', 'desc')->get();
        $pendingCoachesCount = $pendingCoaches->count();
        return view('admin.users.coaches', compact('pendingCoaches', 'approvedCoaches', 'pendingCoachesCount'));
    }

    public function approveCoach(User $user)
    {
        $user->update(['coach_approved' => true]);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'coach_approved',
            'title'   => '🎉 Your coach account has been approved!',
            'message' => 'Congratulations! Your coach profile has been approved by the admin. You can now create and manage surf courses.',
        ]);

        return back()->with('success', "Coach {$user->name} has been approved.");
    }

    public function rejectCoach(User $user)
    {
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'coach_rejected',
            'title'   => 'Your coach account was not approved',
            'message' => 'Unfortunately, your coach profile was not approved at this time. Please contact the admin for more information.',
        ]);

        $user->update(['role' => 'surfeur', 'coach_approved' => false]);

        return back()->with('success', "Coach {$user->name} has been rejected.");
    }

    public function changeUserStatus(User $user)
    {
        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
        return back()->with('success', "User status updated.");
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', "User deleted.");
    }
}
