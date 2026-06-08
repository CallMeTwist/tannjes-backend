<?php
namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_password_is_hashed_and_can_issue_tokens(): void
    {
        $patient = Patient::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '08000000000',
            'password' => 'secret123',
            'status' => 'pending_payment',
        ]);

        $this->assertNotEquals('secret123', $patient->password);
        $this->assertTrue(Hash::check('secret123', $patient->password));

        $token = $patient->createToken('test')->plainTextToken;
        $this->assertNotEmpty($token);
    }
}
