<?php

namespace App\Http\Controllers;

use App\Notification;
use App\User;
use App\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    public function init()
    {
        $user = Auth::user();

        if (isset($user->id))
        {
            $notifications = Notification::where('user_id', $user->id)->take(5)->latest()->get();
            $user['notifications'] = $notifications;
        }


        return response()->json([
            'user' => $user,
            'installed' => $this->isInstalled()
        ], 200);
    }

    public function install(Request $request)
    {
        $admin = new User();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);
        $admin->admin = 1;
        $admin->save();

        $website = new Website();
        $website->name = $request->website_name;
        $website->save();

        return response()->json(true, 200);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], 1))
        {
            $user = User::find(Auth::id());
            $notifications = Notification::where('user_id', $user->id)->take(5)->latest()->get();
            $user['notifications'] = $notifications;

            return response()->json($user, 200);
        }
        else
        {
            return response()->json(['error' => 401], 200);
        }
    }

    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->admin = 0;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        Auth::login($user, 1);

        return response()->json($user, 200);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(true, 200);
    }

    private function isInstalled()
    {
        $admin = User::where('admin', 1)->first();

        if (!isset($admin->id))
        {
            return false;
        }

        return true;
    }
}
