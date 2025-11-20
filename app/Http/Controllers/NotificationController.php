<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Show all notifications for current user.
     */
    public function index()
    {
        $notifications = Notification::where(function($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show unread notifications only.
     */
    public function unread()
    {
        $notifications = Notification::where(function($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })->where('read', false)
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::findOrFail($id);

            if ($notification->user_id !== null && $notification->user_id !== auth()->id()) {
                abort(403);
            }

            $notification->markAsRead();
            return back()->with('success', 'Notification marked as read.');
        } catch (\Illuminate\Database\QueryException $e) {
            // If DB schema is missing expected columns, return gracefully
            return back()->with('error', 'Unable to mark notification as read at this time.');
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        try {
            \App\Models\Notification::where(function($q) {
                $q->where('user_id', auth()->id());
            })->where('read', false)->update(['read' => true, 'read_at' => now()]);

            return back()->with('success', 'All notifications marked as read.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Unable to mark notifications as read.');
        }
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            if ($notification->user_id !== null && $notification->user_id !== auth()->id()) {
                abort(403);
            }

            $notification->delete();
            return back()->with('success', 'Notification deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Unable to delete notification.');
        }
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll()
    {
        try {
            // Delete only notifications that belong to this user (do not delete global/system notifications)
            auth()->user()->notifications()->delete();
            return redirect()->route('notifications.index')->with('success', 'All notifications deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('notifications.index')->with('error', 'Unable to delete notifications.');
        }
    }

    /**
     * Get unread notification count (AJAX).
     */
    public function getUnreadCount()
    {
        try {
            $count = Notification::where(function($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })->where('read', false)->count();
            return response()->json(['count' => $count]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Get latest notifications (AJAX).
     */
    public function getLatest()
    {
        try {
            $notifications = Notification::where(function($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })->latest()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'read' => $notification->read,
                        'created_at_human' => $notification->created_at_human,
                        'icon' => $notification->icon,
                        'badge_color' => $notification->badge_color,
                        'data' => $notification->data,
                    ];
                });

            $unreadCount = Notification::where(function($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })->where('read', false)->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }
    }
}