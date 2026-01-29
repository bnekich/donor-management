<?php

use App\Livewire\Auth\ChangePassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('user with null password_changed_at is redirected to password change page', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('password.change'));
});

test('user can access password change page when password_changed_at is null', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('password.change'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(ChangePassword::class);
});

test('user can change password on first login', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(ChangePassword::class)
        ->set('password', 'NewPassword123!')
        ->set('password_confirmation', 'NewPassword123!')
        ->call('changePassword')
        ->assertRedirect(route('dashboard', absolute: false));

    $user->refresh();
    expect($user->password_changed_at)->not->toBeNull();
    expect($user->mustChangePassword())->toBeFalse();
    expect(Hash::check('NewPassword123!', $user->password))->toBeTrue();
});

test('user cannot bypass password change requirement', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('donations.index'));

    $response->assertRedirect(route('password.change'));
});

test('user with password_changed_at set can access protected routes', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertSuccessful();
});

test('password change requires valid password confirmation', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password_changed_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(ChangePassword::class)
        ->set('password', 'NewPassword123!')
        ->set('password_confirmation', 'DifferentPassword123!')
        ->call('changePassword')
        ->assertHasErrors(['password']);
});

test('login redirects to password change when password_changed_at is null', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'password_changed_at' => null,
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('password.change'));
    $this->assertAuthenticated();
});

test('login redirects to dashboard when password_changed_at is set', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'password_changed_at' => now(),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();
});
