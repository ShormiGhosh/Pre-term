<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated student
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        $notifications = $student->notifications()->paginate(20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $student->unreadNotificationsCount()
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $student = Auth::guard('student')->user();
        
        return response()->json([
            'success' => true,
            'count' => $student->unreadNotificationsCount()
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $student = Auth::guard('student')->user();
        $notification = Notification::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $student = Auth::guard('student')->user();
        
        Notification::where('student_id', $student->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification
     */
    public function delete($id)
    {
        $student = Auth::guard('student')->user();
        $notification = Notification::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
}
