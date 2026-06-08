<?php
namespace Tests\Feature;

use App\Models\Department;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_pending_patient_and_returns_token(): void
    {
        $dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology']);

        $res = $this->postJson('/api/patient/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '08000000000',
            'password' => 'secret123',
            'department_slug' => 'cardiology',
        ]);

        $res->assertCreated()
            ->assertJsonPath('patient.status', 'pending_payment')
            ->assertJsonStructure(['token', 'patient' => ['id', 'name', 'email', 'status']]);
        $this->assertDatabaseHas('patients', ['email' => 'jane@example.com', 'department_id' => $dept->id]);
    }

    public function test_login_returns_token_for_valid_credentials(): void
    {
        Patient::create(['name' => 'Jane', 'email' => 'jane@example.com', 'password' => 'secret123', 'status' => 'pending_payment']);

        $this->postJson('/api/patient/login', ['email' => 'jane@example.com', 'password' => 'secret123'])
            ->assertOk()->assertJsonStructure(['token', 'patient']);
    }

    public function test_login_rejects_bad_credentials(): void
    {
        Patient::create(['name' => 'Jane', 'email' => 'jane@example.com', 'password' => 'secret123', 'status' => 'pending_payment']);

        $this->postJson('/api/patient/login', ['email' => 'jane@example.com', 'password' => 'wrong'])
            ->assertStatus(422);
    }

    public function test_me_returns_authenticated_patient(): void
    {
        $patient = Patient::create(['name' => 'Jane', 'email' => 'jane@example.com', 'password' => 'secret123', 'status' => 'approved']);
        $token = $patient->createToken('t')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/patient/me')
            ->assertOk()->assertJsonPath('status', 'approved');
    }
}
