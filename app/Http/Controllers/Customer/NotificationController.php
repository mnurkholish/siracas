<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('customer.notifications.index', compact('notifications'));
    }

    public function read(DatabaseNotification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json([
                'unread_count' => Auth::user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    public function destroy(DatabaseNotification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus');
    }

    public function destroyAll()
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'Semua notifikasi berhasil dihapus');
    }

    private function authorizeNotification(DatabaseNotification $notification): void
    {
        abort_unless(
            $notification->notifiable_type === Auth::user()::class
            && (int) $notification->notifiable_id === Auth::id(),
            403
        );
    }
}
