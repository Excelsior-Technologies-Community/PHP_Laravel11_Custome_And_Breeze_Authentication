<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->get();

        return view('dashboard', compact('sessions'));
    }

    public function destroy($id)
    {
        DB::table('sessions')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Session removed successfully.');
    }

    public function logoutOtherDevices()
    {
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', session()->getId())
            ->delete();

        return back()->with('success', 'Logged out from all other devices.');
    }
}