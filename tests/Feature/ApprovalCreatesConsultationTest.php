<?php
namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Department;
use App\Models\Patient;
use App\Models\TeamMember;
use App\Services\PatientApproval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalCreatesConsultationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_creates_a_consultation_with_department_doctor(): void
    {
        $dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $doc = TeamMember::create(['name' => 'Dr. A', 'role' => 'Consultant', 'bio' => 'x', 'department_id' => $dept->id, 'is_active' => true, 'is_consultant' => true]);
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'pending_approval', 'department_id' => $dept->id]);

        PatientApproval::approve($patient, null);

        $this->assertEquals('approved', $patient->fresh()->status);
        $this->assertDatabaseHas('consultations', ['patient_id' => $patient->id, 'department_id' => $dept->id, 'doctor_id' => $doc->id]);
        $this->assertEquals(1, Consultation::where('patient_id', $patient->id)->count());
    }

    public function test_approving_twice_does_not_duplicate_consultation(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'pending_approval']);
        PatientApproval::approve($patient, null);
        PatientApproval::approve($patient->fresh(), null);
        $this->assertEquals(1, Consultation::where('patient_id', $patient->id)->count());
    }
}
