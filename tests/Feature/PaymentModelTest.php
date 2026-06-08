<?php
namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_belongs_to_patient(): void
    {
        $patient = Patient::create([
            'name' => 'Jane', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'pending_payment',
        ]);
        $payment = Payment::create([
            'patient_id' => $patient->id,
            'amount' => 15000,
            'reference' => 'TXN123',
            'proof_path' => 'payment-proofs/x.png',
            'status' => 'submitted',
        ]);

        $this->assertEquals($patient->id, $payment->patient->id);
        $this->assertTrue($patient->payments->contains($payment));
    }
}
