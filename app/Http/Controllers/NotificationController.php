<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class NotificationController extends Controller
{
    public function hasNewActivate()
    {
        $user = User::find(Auth::id());
        $user->has_new_notifications = 1;
        $user->save();
    }

    public function hasNewDeactivate()
    {
        $user = User::find(Auth::id());
        $user->has_new_notifications = 0;
        $user->save();
    }

    public function get()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->paginate(10);

        return response()->json($notifications, 200);
    }
}
