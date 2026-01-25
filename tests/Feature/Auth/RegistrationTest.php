<?php

test('registration screen is not accessible', function () {
    $response = $this->get(route('register'));

    $response->assertNotFound();
});

test('registration route does not exist', function () {
    expect(route('register', absolute: false))->toBe('/register');
    
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNotFound();
});