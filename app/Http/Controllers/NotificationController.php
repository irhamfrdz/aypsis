<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect to the notification URL if it exists
        if (isset($notification->data['url'])) {
            $url = $notification->data['url'];
            
            // Fix absolute URL to relative to support different ports (php artisan serve vs apache)
            if (\Illuminate\Support\Str::startsWith($url, ['http://localhost', 'http://127.0.0.1'])) {
                $parsed = parse_url($url);
                $url = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
            }
            
            return redirect($url);
        }

        return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Delete all notifications.
     */
    public function clearAll()
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'Semua riwayat notifikasi berhasil dihapus.');
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications->count(),
        ]);
    }
}
