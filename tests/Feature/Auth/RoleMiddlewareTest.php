<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_umum_user_cannot_access_admin_route(): void
    {
        $user = User::factory()->create();
        $user->assignRole('umum');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/admin-only');

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_route(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/admin-only');

        $response->assertStatus(200);
    }

    public function test_koorlap_can_access_scan_route(): void
    {
        $user = User::factory()->create();
        $user->assignRole('koorlap');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/scan-qr');

        $response->assertStatus(200);
    }
}
