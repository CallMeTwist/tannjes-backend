<?php
namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PatientApprovedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware(['auth:sanctum', 'patient.approved'])
            ->get('/api/_test/guarded', fn () => response()->json(['ok' => true]));
    }

    public function test_unapproved_patient_is_blocked(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'pending_approval']);
        $token = $patient->createToken('t')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/_test/guarded')
            ->assertStatus(403);
    }

    public function test_approved_patient_passes(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'approved']);
        $token = $patient->createToken('t')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/_test/guarded')
            ->assertOk();
    }
}
