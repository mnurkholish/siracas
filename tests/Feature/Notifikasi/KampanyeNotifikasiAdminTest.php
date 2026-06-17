<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin bisa membuat melihat mengubah dan menghapus kampanye notifikasi', function () {
    $admin = adminUser();

    $this->actingAs($admin)
        ->post(route('admin.campaigns.store'), [
            'type' => 'promo',
            'title' => 'Promo Panen',
            'message' => 'Diskon khusus.',
            'url' => route('products.index'),
        ])
        ->assertRedirect(route('admin.campaigns.index'));

    $campaign = App\Models\NotificationCampaign::where('title', 'Promo Panen')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.campaigns.index'))
        ->assertOk()
        ->assertViewHas('campaigns');

    $this->actingAs($admin)
        ->put(route('admin.campaigns.update', $campaign), [
            'type' => 'announcement',
            'title' => 'Info Baru',
            'message' => 'Pesan diperbarui.',
        ])
        ->assertRedirect(route('admin.campaigns.index'));

    expect($campaign->fresh()->type)->toBe('announcement');

    $this->actingAs($admin)
        ->delete(route('admin.campaigns.destroy', $campaign))
        ->assertRedirect();

    $this->assertDatabaseMissing('notification_campaigns', ['id' => $campaign->id]);
});

it('gagal membuat atau mengubah kampanye jika data kosong atau tidak valid', function () {
    $admin = adminUser();
    $campaign = testNotificationCampaign();

    $this->actingAs($admin)
        ->from(route('admin.campaigns.index'))
        ->post(route('admin.campaigns.store'), [])
        ->assertRedirect(route('admin.campaigns.index'))
        ->assertSessionHasErrors(['type', 'title', 'message']);

    $this->actingAs($admin)
        ->from(route('admin.campaigns.index'))
        ->put(route('admin.campaigns.update', $campaign), [
            'type' => 'salah',
            'title' => '',
            'message' => '',
        ])
        ->assertRedirect(route('admin.campaigns.index'))
        ->assertSessionHasErrors(['type', 'title', 'message']);
});

it('admin bisa publish dan unpublish kampanye notifikasi', function () {
    $admin = adminUser();
    $activeCustomer = customerUser();
    $inactiveCustomer = customerUser(['is_active' => false]);
    $campaign = testNotificationCampaign(['url' => route('products.index')]);

    $this->actingAs($admin)
        ->patch(route('admin.campaigns.publish', $campaign))
        ->assertRedirect();

    expect($campaign->fresh()->is_active)->toBeTrue()
        ->and($activeCustomer->notifications()->count())->toBe(1)
        ->and($inactiveCustomer->notifications()->count())->toBe(0);

    $this->actingAs($admin)
        ->patch(route('admin.campaigns.unpublish', $campaign))
        ->assertRedirect();

    expect($campaign->fresh()->is_active)->toBeFalse()
        ->and($activeCustomer->notifications()->count())->toBe(0);
});

it('customer tidak boleh mengakses halaman kampanye notifikasi admin', function () {
    $this->actingAs(customerUser())
        ->get(route('admin.campaigns.index'))
        ->assertForbidden();
});
