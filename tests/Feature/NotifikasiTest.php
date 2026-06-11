<?php

use App\Models\NotificationCampaign;
use App\Notifications\AdminCampaignNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('mempublikasikan notifikasi kampanye hanya ke customer aktif', function () {
    $admin = adminUser();
    $activeCustomer = customerUser();
    $inactiveCustomer = customerUser(['is_active' => false]);
    $campaign = NotificationCampaign::create([
        'type' => 'promo',
        'title' => 'Promo Test',
        'message' => 'Diskon khusus testing.',
        'url' => route('products.index'),
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.campaigns.publish', $campaign))
        ->assertRedirect();

    expect($campaign->fresh()->is_active)->toBeTrue()
        ->and($activeCustomer->notifications()->count())->toBe(1)
        ->and($inactiveCustomer->notifications()->count())->toBe(0);
});

it('customer dapat membaca dan menghapus notifikasi miliknya sendiri', function () {
    $customer = customerUser();
    $campaign = NotificationCampaign::create([
        'type' => 'announcement',
        'title' => 'Info',
        'message' => 'Pesan untuk customer.',
    ]);
    $customer->notify(new AdminCampaignNotification($campaign));
    $notification = $customer->notifications()->first();

    $this->actingAs($customer)
        ->patch(route('notifications.read', $notification))
        ->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();

    $this->actingAs($customer)
        ->delete(route('notifications.destroy', $notification))
        ->assertRedirect();

    $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
});

it('mencegah customer membaca notifikasi milik user lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    $campaign = NotificationCampaign::create([
        'type' => 'product',
        'title' => 'Produk Baru',
        'message' => 'Produk baru tersedia.',
    ]);
    $owner->notify(new AdminCampaignNotification($campaign));
    $notification = $owner->notifications()->first();

    $this->actingAs($otherCustomer)
        ->patch(route('notifications.read', $notification))
        ->assertForbidden();
});

it('customer dapat menghapus semua notifikasinya tanpa menghapus notifikasi user lain', function () {
    $customer = customerUser();
    $otherCustomer = customerUser();
    $campaign = NotificationCampaign::create([
        'type' => 'promo',
        'title' => 'Promo',
        'message' => 'Promo terbatas.',
    ]);

    $customer->notify(new AdminCampaignNotification($campaign));
    $otherCustomer->notify(new AdminCampaignNotification($campaign));

    $this->actingAs($customer)
        ->delete(route('notifications.clear'))
        ->assertRedirect();

    expect($customer->notifications()->count())->toBe(0)
        ->and($otherCustomer->notifications()->count())->toBe(1);
});
