<?php
namespace Tests\Feature;

use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_department(): void
    {
        $dept = Department::create([
            'name' => 'Cardiology',
            'slug' => 'cardiology',
            'description' => 'Heart care.',
            'icon' => 'HeartPulse',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('departments', ['slug' => 'cardiology']);
        $this->assertTrue($dept->is_active);
    }
}
