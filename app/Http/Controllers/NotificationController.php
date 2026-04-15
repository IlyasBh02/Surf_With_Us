<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markRead()
    {
        Auth::user()->notifications()->where('read', false)->update(['read' => true]);
        return response()->json(['success' => true]);
    }
}
