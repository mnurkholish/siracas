<?php

use App\Notifications\CustomResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

it('user bisa meminta link reset password dengan email valid', function () {
    Notification::fake();
    $customer = customerUser();

    $this->from(route('password.request'))
        ->post(route('password.email'), ['email' => $customer->email])
        ->assertRedirect(route('password.request'))
        ->assertSessionHas('status');

    Notification::assertSentTo($customer, CustomResetPassword::class);
});

it('gagal meminta reset password jika email tidak terdaftar', function () {
    Notification::fake();

    $this->from(route('password.request'))
        ->post(route('password.email'), ['email' => 'tidakada@example.test'])
        ->assertRedirect(route('password.request'))
        ->assertSessionHasErrors('email');

    Notification::assertNothingSent();
});

it('gagal meminta reset password jika form kosong', function () {
    $this->from(route('password.request'))
        ->post(route('password.email'), [])
        ->assertRedirect(route('password.request'))
        ->assertSessionHasErrors('email');
});

it('user bisa mengganti password menggunakan token valid', function () {
    $customer = customerUser();
    $token = Password::broker()->createToken($customer);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $customer->email,
        'password' => validPassword(),
        'password_confirmation' => validPassword(),
    ])->assertRedirect(route('login'));

    expect(Hash::check(validPassword(), $customer->fresh()->password))->toBeTrue();
});

it('gagal reset password jika konfirmasi tidak sesuai', function () {
    $customer = customerUser();
    $token = Password::broker()->createToken($customer);

    $this->from(route('password.reset', $token))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => $customer->email,
            'password' => validPassword(),
            'password_confirmation' => 'Beda123!',
        ])
        ->assertRedirect(route('password.reset', $token))
        ->assertSessionHasErrors('password');
});

it('gagal reset password jika form kosong', function () {
    $this->from(route('password.reset', 'token-test'))
        ->post(route('password.update'), [])
        ->assertRedirect(route('password.reset', 'token-test'))
        ->assertSessionHasErrors(['token', 'email', 'password']);
});
