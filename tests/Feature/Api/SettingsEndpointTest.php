<?php

namespace Tests\Feature\Api;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_key_value_map(): void
    {
        Setting::create(['key' => 'phone_primary', 'value' => '+1']);
        Setting::create(['key' => 'email', 'value' => 'a@b.c']);

        $response = $this->getJson('/api/settings');

        $response->assertOk();
        $response->assertExactJson([
            'phone_primary' => '+1',
            'email' => 'a@b.c',
        ]);
    }

    public function test_returns_empty_object_when_no_settings(): void
    {
        $response = $this->getJson('/api/settings');

        $response->assertOk();
        $response->assertExactJson([]);
    }
}
