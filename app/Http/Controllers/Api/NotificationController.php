<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get notifications for the logged-in user
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all, read, unread

        $query = AppNotification::where('user_id', $request->user()->id);

        if ($filter === 'read') {
            $query->where('is_read', true);
        } elseif ($filter === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->latest()->paginate(20);

        return response()->json($notifications);
    }

    // Get unread count (for the bell badge)
    public function unreadCount(Request $request)
    {
        $count = AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    // Mark a single notification as read
    public function markRead(Request $request, $id)
    {
        $notification = AppNotification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Marked as read.']);
    }

    // Mark all as read
    public function markAllRead(Request $request)
    {
        AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    // Delete a notification
    public function destroy(Request $request, $id)
    {
        $notification = AppNotification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    // Admin notigicstions
    // Admin: get their notifications
public function adminIndex(Request $request)
{
    $filter = $request->query('filter', 'all');

    $query = AppNotification::where('is_admin', true);

    if ($filter === 'read') {
        $query->where('is_read', true);
    } elseif ($filter === 'unread') {
        $query->where('is_read', false);
    }

    $notifications = $query->latest()->paginate(50);
    return response()->json($notifications);
}

// Admin: mark single read
public function adminMarkRead($id)
{
    $notification = AppNotification::where('id', $id)
        ->where('is_admin', true)->first();

    if (!$notification) return response()->json(['message' => 'Not found.'], 404);

    $notification->update(['is_read' => true]);
    return response()->json(['message' => 'Marked as read.']);
}

// Admin: mark all read
public function adminMarkAllRead()
{
    AppNotification::where('is_admin', true)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['message' => 'All marked as read.']);
}

// Admin: delete
public function adminDestroy($id)
{
    $notification = AppNotification::where('id', $id)
        ->where('is_admin', true)->first();

    if (!$notification) return response()->json(['message' => 'Not found.'], 404);

    $notification->delete();
    return response()->json(['message' => 'Deleted.']);
}

// Admin: unread count
public function adminUnreadCount()
{
    $count = AppNotification::where('is_admin', true)
        ->where('is_read', false)->count();

    return response()->json(['unread_count' => $count]);
}
}