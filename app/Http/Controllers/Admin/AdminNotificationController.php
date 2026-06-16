<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function read(DatabaseNotification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    public function detail(DatabaseNotification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->markAsRead();

        $detailUrl = trim((string) ($notification->data['url'] ?? ''));

        return $detailUrl !== ''
            ? redirect()->to($detailUrl)
            : redirect()->route('admin.notifications.index');
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
