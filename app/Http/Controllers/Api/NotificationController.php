<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /api/notifications
    public function index(Request $request)
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    // PUT /api/notifications/{id}/read
    // marks a single notification as read when they tap on it
    public function markRead(Request $request, $id)
    {
        $notification = AppNotification::where('user_id', $request->user()->id)->find($id);

        if (! $notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->read_at = now();
        $notification->save();

        return response()->json($notification);
    }

    // PUT /api/notifications/read-all
    // for a "mark all as read" button
    public function markAllRead(Request $request)
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
