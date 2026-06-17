<?php

use App\Notifications\AdminCampaignNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer bisa melihat daftar dan detail notifikasi', function () {
    $customer = customerUser();
    $campaign = testNotificationCampaign(['url' => route('products.index')]);
    $customer->notify(new AdminCampaignNotification($campaign));
    $notification = $customer->notifications()->first();

    $this->actingAs($customer)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertViewHas('notifications');

    $this->actingAs($customer)
        ->get(route('notifications.detail', $notification))
        ->assertRedirect(route('products.index'));

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('customer bisa menandai notifikasi sebagai sudah dibaca dan jumlah unread berubah', function () {
    $customer = customerUser();
    $campaign = testNotificationCampaign();
    $customer->notify(new AdminCampaignNotification($campaign));
    $notification = $customer->notifications()->first();

    expect($customer->unreadNotifications()->count())->toBe(1);

    $this->actingAs($customer)
        ->patch(route('notifications.read', $notification))
        ->assertRedirect();

    expect($customer->unreadNotifications()->count())->toBe(0);
});

it('customer bisa menghapus notifikasi miliknya', function () {
    $customer = customerUser();
    $campaign = testNotificationCampaign();
    $customer->notify(new AdminCampaignNotification($campaign));
    $notification = $customer->notifications()->first();

    $this->actingAs($customer)
        ->delete(route('notifications.destroy', $notification))
        ->assertRedirect();

    $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
});

it('customer tidak bisa menghapus atau mengubah notifikasi milik customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    $campaign = testNotificationCampaign();
    $owner->notify(new AdminCampaignNotification($campaign));
    $notification = $owner->notifications()->first();

    $this->actingAs($otherCustomer)
        ->patch(route('notifications.read', $notification))
        ->assertForbidden();

    $this->actingAs($otherCustomer)
        ->delete(route('notifications.destroy', $notification))
        ->assertForbidden();
});
