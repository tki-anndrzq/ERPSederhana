<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class DataTablesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_reference_requires_login(): void
    {
        $this->get('/ui/datatables')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_open_the_vue_table_reference(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get('/ui/datatables')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Ui/DataTables'));
    }
}
