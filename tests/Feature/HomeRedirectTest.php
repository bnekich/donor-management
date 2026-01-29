<?php

use App\Models\User;

test('guest is redirected to login when visiting home', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});

test('authenticated user with password not changed is redirected to password change when visiting home', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertRedirect(route('password.change'));
});

test('authenticated user with password changed is redirected to dashboard when visiting home', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertRedirect(route('dashboard'));
});
