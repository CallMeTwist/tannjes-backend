<?php
namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_submit_payment_with_proof(): void
    {
        Storage::fake('public');
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'pending_payment']);
        $token = $patient->createToken('t')->plainTextToken;

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/patient/payment', [
                'amount' => 15000,
                'reference' => 'TXN-001',
                'proof' => UploadedFile::fake()->image('proof.png'),
            ]);

        $res->assertOk()->assertJsonPath('patient.status', 'pending_approval');
        $this->assertDatabaseHas('payments', ['patient_id' => $patient->id, 'reference' => 'TXN-001', 'status' => 'submitted']);
        $this->assertEquals('pending_approval', $patient->fresh()->status);
        Storage::disk('public')->assertExists(Payment::first()->proof_path);
    }
}
