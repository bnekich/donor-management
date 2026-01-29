<?php

use App\Models\User;
use Livewire\Livewire;

test('admin can view users index', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);

    $response = $this->actingAs($admin)->get(route('users.index'));

    $response->assertSuccessful();
    $response->assertSeeLivewire('users');
});

test('non-admin cannot view users index', function () {
    $user = User::factory()->withoutTwoFactor()->create(['is_admin' => false, 'password_changed_at' => now()]);

    $response = $this->actingAs($user)->get(route('users.index'));

    $response->assertForbidden();
});

test('admin can view create user page', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);

    $response = $this->actingAs($admin)->get(route('users.create'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(\App\Livewire\Users\Create::class);
});

test('non-admin cannot view create user page', function () {
    $user = User::factory()->withoutTwoFactor()->create(['is_admin' => false, 'password_changed_at' => now()]);

    $response = $this->actingAs($user)->get(route('users.create'));

    $response->assertForbidden();
});

test('admin can create user and user has temporary password state', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('name', 'New User')
        ->set('email', 'newuser@example.com')
        ->set('is_admin', false)
        ->call('save')
        ->assertRedirect(route('users.index'));

    $user = User::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New User');
    expect($user->password_changed_at)->toBeNull();
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->is_admin)->toBeFalse();
});

test('admin can view user show page', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);
    $user = User::factory()->withoutTwoFactor()->create(['password_changed_at' => now()]);

    $response = $this->actingAs($admin)->get(route('users.show', $user));

    $response->assertSuccessful();
    $response->assertSee($user->name);
    $response->assertSee($user->email);
});

test('non-admin cannot view user show page', function () {
    $user = User::factory()->withoutTwoFactor()->create(['is_admin' => false, 'password_changed_at' => now()]);
    $other = User::factory()->withoutTwoFactor()->create(['password_changed_at' => now()]);

    $response = $this->actingAs($user)->get(route('users.show', $other));

    $response->assertForbidden();
});

test('admin can view user edit page', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);
    $user = User::factory()->withoutTwoFactor()->create(['password_changed_at' => now()]);

    $response = $this->actingAs($admin)->get(route('users.edit', $user));

    $response->assertSuccessful();
    $response->assertSeeLivewire(\App\Livewire\Users\Edit::class);
});

test('non-admin cannot view user edit page', function () {
    $user = User::factory()->withoutTwoFactor()->create(['is_admin' => false, 'password_changed_at' => now()]);
    $other = User::factory()->withoutTwoFactor()->create(['password_changed_at' => now()]);

    $response = $this->actingAs($user)->get(route('users.edit', $other));

    $response->assertForbidden();
});

test('admin can update user', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);
    $user = User::factory()->withoutTwoFactor()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'password_changed_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Edit::class, ['user' => $user])
        ->set('name', 'Updated Name')
        ->set('email', 'updated@example.com')
        ->call('save')
        ->assertRedirect(route('users.show', $user));

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
});

test('admin can delete another user', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);
    $user = User::factory()->withoutTwoFactor()->create(['password_changed_at' => now()]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('delete', $user->id)
        ->assertHasNoErrors();

    expect(User::find($user->id))->toBeNull();
});

test('admin cannot delete themselves', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('delete', $admin->id);

    expect(User::find($admin->id))->not->toBeNull();
});

test('users index shows temporary password after create redirect', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['is_admin' => true, 'password_changed_at' => now()]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Create::class)
        ->set('name', 'Flash User')
        ->set('email', 'flash@example.com')
        ->call('save')
        ->assertRedirect(route('users.index'));

    $created = User::where('email', 'flash@example.com')->first();
    expect($created)->not->toBeNull();
    expect($created->password_changed_at)->toBeNull();

    $response = $this->actingAs($admin)->get(route('users.index'));
    $response->assertSuccessful();
    $response->assertSee('Flash User');
    $response->assertSee('flash@example.com');
});
