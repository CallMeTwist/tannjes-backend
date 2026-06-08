<?php
namespace Tests\Feature;

use App\Models\Patient;
use App\Models\TestResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestResultApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_sees_only_their_results(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'approved']);
        $token = $patient->createToken('t')->plainTextToken;
        TestResult::create(['patient_id' => $patient->id, 'title' => 'FBC', 'file_path' => 'test-results/a.pdf']);

        $other = Patient::create(['name' => 'X', 'email' => 'x@e.com', 'password' => 'secret123', 'status' => 'approved']);
        TestResult::create(['patient_id' => $other->id, 'title' => 'Other', 'file_path' => 'test-results/b.pdf']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/patient/results')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'FBC');
    }

    public function test_unapproved_patient_is_blocked(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'pending_approval']);
        $token = $patient->createToken('t')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/patient/results')
            ->assertStatus(403);
    }
}
