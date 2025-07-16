<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = $this->notificationService->getUserNotifications(
            Auth::user(), 
            $request->get('per_page', 15)
        );

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure user can only mark their own notifications as read
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::user());

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notifications count (for AJAX requests).
     */
    public function getUnreadCount()
    {
        $count = $this->notificationService->getUnreadCount(Auth::user());

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for dropdown (AJAX).
     */
    public function getRecent()
    {
        $notifications = Auth::user()->notifications()
                              ->limit(5)
                              ->orderBy('created_at', 'desc')
                              ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $this->notificationService->getUnreadCount(Auth::user())
        ]);
    }
}
