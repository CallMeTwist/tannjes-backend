<?php

namespace Tests\Feature\Api;

use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_active_members_ordered_by_sort_order(): void
    {
        TeamMember::create(['name' => 'B', 'role' => 'r', 'bio' => 'b', 'sort_order' => 2, 'is_active' => true]);
        TeamMember::create(['name' => 'A', 'role' => 'r', 'bio' => 'b', 'sort_order' => 1, 'is_active' => true]);
        TeamMember::create(['name' => 'Hidden', 'role' => 'r', 'bio' => 'b', 'sort_order' => 3, 'is_active' => false]);

        $response = $this->getJson('/api/team');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.name', 'A');
        $response->assertJsonPath('1.name', 'B');
        $response->assertJsonStructure([
            ['name', 'role', 'bio', 'credentials', 'image_url', 'sort_order'],
        ]);
    }

    public function test_image_url_is_absolute_when_image_set(): void
    {
        config(['app.url' => 'https://api.example.com']);
        TeamMember::create(['name' => 'A', 'role' => 'r', 'bio' => 'b', 'image' => 'team/a.jpg', 'sort_order' => 1, 'is_active' => true]);

        $response = $this->getJson('/api/team');

        $response->assertJsonPath('0.image_url', 'https://api.example.com/storage/team/a.jpg');
    }

    public function test_image_url_is_null_when_image_unset(): void
    {
        TeamMember::create(['name' => 'A', 'role' => 'r', 'bio' => 'b', 'sort_order' => 1, 'is_active' => true]);

        $response = $this->getJson('/api/team');

        $response->assertJsonPath('0.image_url', null);
    }
}
