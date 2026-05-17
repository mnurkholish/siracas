<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationCampaign;
use App\Models\User;
use App\Notifications\AdminCampaignNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NotificationCampaignController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $type = (string) $request->query('type');

        $campaigns = NotificationCampaign::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when(array_key_exists($type, NotificationCampaign::TYPES), fn ($query) => $query->where('type', $type))
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.notification-campaigns.index', [
            'campaigns' => $campaigns,
            'types' => NotificationCampaign::TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedCampaign($request);
        $data['image'] = $this->storeImage($request);

        NotificationCampaign::create($data);

        return redirect()
            ->route('admin.notification-campaigns.index')
            ->with('success', 'Campaign notifikasi berhasil ditambah');
    }

    public function update(Request $request, NotificationCampaign $notificationCampaign)
    {
        $data = $this->validatedCampaign($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($notificationCampaign->image);
            $data['image'] = $this->storeImage($request);
        } else {
            unset($data['image']);
        }

        $notificationCampaign->update($data);

        return redirect()
            ->route('admin.notification-campaigns.index')
            ->with('success', 'Campaign notifikasi berhasil diperbarui');
    }

    public function destroy(NotificationCampaign $notificationCampaign)
    {
        $this->deleteImage($notificationCampaign->image);
        $notificationCampaign->delete();

        return back()->with('success', 'Campaign notifikasi berhasil dihapus');
    }

    public function publish(NotificationCampaign $notificationCampaign)
    {
        User::query()
            ->where('role', 'customer')
            ->where('is_active', true)
            ->chunkById(100, function ($customers) use ($notificationCampaign) {
                foreach ($customers as $customer) {
                    $customer->notify(new AdminCampaignNotification($notificationCampaign));
                }
            });

        $notificationCampaign->update([
            'is_active' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Campaign notifikasi berhasil dipublish');
    }

    public function unpublish(NotificationCampaign $notificationCampaign)
    {
        $notificationCampaign->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Campaign notifikasi dinonaktifkan');
    }

    private function validatedCampaign(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(array_keys(NotificationCampaign::TYPES))],
            'title' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:1000'],
            'url' => ['nullable', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'type.required' => 'Tipe notifikasi wajib dipilih.',
            'type.in' => 'Tipe notifikasi tidak valid.',
            'title.required' => 'Judul wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'image.image' => 'Gambar harus berupa file gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        $file = $request->file('image');

        if (! $file || ! $file->isValid()) {
            return null;
        }

        return $file->store('notification-campaigns', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
