<?php
namespace Tests\Feature;

use App\Models\Department;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberDepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_belongs_to_department(): void
    {
        $dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $doc = TeamMember::create([
            'name' => 'Dr. Tunde', 'role' => 'Consultant', 'bio' => 'Heart.',
            'department_id' => $dept->id, 'is_consultant' => true,
        ]);

        $this->assertEquals('Cardiology', $doc->department->name);
        $this->assertTrue($dept->doctors->contains($doc));
    }
}
