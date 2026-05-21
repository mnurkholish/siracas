<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationCampaign;
use App\Models\User;
use App\Notifications\AdminCampaignNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
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
        $data['is_active'] = false;
        $data['published_at'] = null;

        NotificationCampaign::create($data);

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'Kampanye notifikasi berhasil ditambah');
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
            ->route('admin.campaigns.index')
            ->with('success', 'Kampanye notifikasi berhasil diperbarui');
    }

    public function destroy(NotificationCampaign $notificationCampaign)
    {
        $this->deletePublishedNotifications($notificationCampaign);
        $this->deleteImage($notificationCampaign->image);
        $notificationCampaign->delete();

        return back()->with('success', 'Kampanye notifikasi berhasil dihapus');
    }

    public function publish(NotificationCampaign $notificationCampaign)
    {
        $this->deletePublishedNotifications($notificationCampaign);

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

        return back()->with('success', 'Kampanye notifikasi berhasil dipublish');
    }

    public function unpublish(NotificationCampaign $notificationCampaign)
    {
        $notificationCampaign->update([
            'is_active' => false,
        ]);

        $deletedNotifications = $this->deletePublishedNotifications($notificationCampaign);

        return back()->with('success', "Kampanye notifikasi dinonaktifkan dan {$deletedNotifications} notifikasi customer ditarik");
    }

    private function validatedCampaign(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(NotificationCampaign::TYPES))],
            'title' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:1000'],
            'url' => ['nullable', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
        ], [
            'type.required' => 'Tipe notifikasi wajib dipilih.',
            'type.in' => 'Tipe notifikasi tidak valid.',
            'title.required' => 'Judul wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'image.image' => 'Gambar harus berupa file gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $data['url'] = filled($data['url'] ?? null) ? trim($data['url']) : null;

        return $data;
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

    private function deletePublishedNotifications(NotificationCampaign $campaign): int
    {
        $deletedNotifications = 0;
        $campaignUrl = filled($campaign->url) ? $campaign->url : null;

        DatabaseNotification::query()
            ->where('type', AdminCampaignNotification::class)
            ->chunkById(100, function ($notifications) use ($campaign, $campaignUrl, &$deletedNotifications) {
                $notificationIds = $notifications
                    ->filter(function (DatabaseNotification $notification) use ($campaign, $campaignUrl) {
                        $data = is_array($notification->data) ? $notification->data : [];

                        if ((int) ($data['campaign_id'] ?? 0) === $campaign->id) {
                            return true;
                        }

                        return ! array_key_exists('campaign_id', $data)
                            && ($data['title'] ?? null) === $campaign->title
                            && ($data['message'] ?? null) === $campaign->message
                            && ($data['type'] ?? null) === $campaign->type
                            && ($data['url'] ?? null) === $campaignUrl;
                    })
                    ->modelKeys();

                if ($notificationIds !== []) {
                    $deletedNotifications += DatabaseNotification::query()
                        ->whereKey($notificationIds)
                        ->delete();
                }
            });

        return $deletedNotifications;
    }
}
