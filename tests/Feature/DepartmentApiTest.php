<?php
namespace Tests\Feature;

use App\Models\Department;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_active_departments_with_doctor_count(): void
    {
        $dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology', 'description' => 'Heart', 'icon' => 'HeartPulse', 'is_active' => true]);
        TeamMember::create(['name' => 'Dr. A', 'role' => 'Consultant', 'bio' => 'x', 'department_id' => $dept->id, 'is_active' => true, 'is_consultant' => true]);

        $res = $this->getJson('/api/departments');

        $res->assertOk()->assertJsonFragment(['slug' => 'cardiology', 'doctor_count' => 1]);
    }

    public function test_show_returns_department_with_doctors(): void
    {
        $dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology', 'is_active' => true]);
        TeamMember::create(['name' => 'Dr. A', 'role' => 'Consultant', 'bio' => 'x', 'department_id' => $dept->id, 'is_active' => true, 'is_consultant' => true]);

        $res = $this->getJson('/api/departments/cardiology');

        $res->assertOk()
            ->assertJsonPath('department.slug', 'cardiology')
            ->assertJsonPath('doctors.0.name', 'Dr. A');
    }

    public function test_show_returns_404_for_unknown_slug(): void
    {
        $this->getJson('/api/departments/nope')->assertNotFound();
    }
}
