<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StarterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_users_with_the_existing_uuid_schema(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $user->email]);
        $this->assertTrue(\Illuminate\Support\Str::isUuid($user->id));
        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->assertArrayNotHasKey('remember_token', $user->toArray());
    }

    public function test_default_seeder_does_not_create_an_account_with_a_fixed_password(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_a_user_can_be_created_interactively_without_a_default_password(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('Nama', 'Starter Admin')
            ->expectsQuestion('Email', 'admin@example.test')
            ->expectsQuestion('Password (minimal 12 karakter)', 'local-test-password')
            ->expectsQuestion('Konfirmasi password', 'local-test-password')
            ->assertSuccessful();

        $user = User::where('email', 'admin@example.test')->firstOrFail();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('local-test-password', $user->password));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_invalid_user_input_is_rejected_without_creating_an_account(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('Nama', 'Starter Admin')
            ->expectsQuestion('Email', 'invalid-email')
            ->expectsQuestion('Password (minimal 12 karakter)', 'short')
            ->expectsQuestion('Konfirmasi password', 'different')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
