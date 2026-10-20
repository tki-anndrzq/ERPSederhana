<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_are_directed_to_the_vue_login_page(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $initial = $this->get('/login')->assertOk();

        $this->get('/login', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => $initial->viewData('page')['version'] ?? '',
        ])
            ->assertOk()
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'Auth/Login')
            ->assertJsonPath('props.auth.user', null);
    }

    public function test_valid_credentials_start_a_session_and_only_share_safe_user_fields(): void
    {
        $user = $this->createUser();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'test-password-123',
            'remember' => true,
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);

        $initial = $this->get('/dashboard')->assertOk();
        $response = $this->get('/dashboard', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => $initial->viewData('page')['version'] ?? '',
        ]);
        $response->assertOk()
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'Dashboard')
            ->assertJsonPath('props.auth.user.name', 'Template Tester')
            ->assertJsonPath('props.auth.user.email', 'tester@example.test')
            ->assertJsonMissingPath('props.auth.user.password')
            ->assertJsonMissingPath('props.auth.user.remember_token');

        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'email'],
            array_keys($response->json('props.auth.user')),
        );

        $this->get('/login')->assertRedirect('/dashboard');
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_invalid_credentials_return_a_form_error_without_authenticating(): void
    {
        $this->createUser();

        $this->from('/login')->post('/login', [
            'email' => 'tester@example.test',
            'password' => 'incorrect-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_validates_input(): void
    {
        $this->from('/login')->post('/login', [
            'email' => 'not-an-email',
            'password' => '',
        ])->assertRedirect('/login')->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_repeated_failed_attempts_are_limited_even_with_correct_credentials(): void
    {
        $this->createUser();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'email' => 'tester@example.test',
                'password' => 'incorrect-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/login', [
            'email' => 'tester@example.test',
            'password' => 'test-password-123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_requires_post_and_removes_dashboard_access(): void
    {
        $this->actingAs($this->createUser());

        $this->get('/logout')->assertStatus(405);
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_old_user_crud_is_removed_and_legacy_dashboard_redirects(): void
    {
        $this->actingAs($this->createUser());

        $this->get('/userData')->assertNotFound();
        $this->get('/dashboard-ecommerce')->assertRedirect('/dashboard');
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Template Tester',
            'email' => 'tester@example.test',
            'password' => Hash::make('test-password-123'),
            'gender' => 'L',
        ]);
    }
}
