<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'name' => 'Fortify Admin User',
        'email' => 'admin@safevoice.org',
        'password' => bcrypt('Password123!'),
        'role' => UserRole::ADMIN,
    ]);
});

test('public users cannot access registration route', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('public users can access reporting and tracking pages without login', function () {
    $this->get('/report')->assertStatus(200);
    $this->get('/track')->assertStatus(200);
});

test('unauthenticated users are redirected from admin dashboard to login screen', function () {
    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/login');
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)->assertSee('SafeVoice Portal');
});

test('admin can authenticate using fortify login screen and redirect to admin dashboard', function () {
    $response = $this->post('/login', [
        'email' => 'admin@safevoice.org',
        'password' => 'Password123!',
    ]);

    $this->assertAuthenticatedAs($this->admin);
    $response->assertRedirect('/admin/dashboard');
});

test('admin can logout successfully', function () {
    $response = $this->actingAs($this->admin)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
