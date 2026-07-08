<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class OnlineUserController extends Controller
{
    public function index()
    {
        // Get users active in the last 15 minutes
        $limit = time() - (config('session.lifetime') * 60);

        // Subquery to get the latest session per user
        $latestSessions = DB::table('sessions')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $limit)
            ->groupBy('user_id');

        $onlineUsers = User::joinSub($latestSessions, 'latest_sessions', function ($join) {
                $join->on('users.id', '=', 'latest_sessions.user_id');
            })
            ->select('users.*', 'latest_sessions.last_activity')
            ->orderBy('latest_sessions.last_activity', 'desc')
            ->get();

        return view('master-user.online', compact('onlineUsers'));
    }
}
